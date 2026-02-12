<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hitch App - Trip</title>
    <?php
    // Get the tripId from the URL
    $tripId = isset($_GET['tripId']) ? htmlspecialchars($_GET['tripId']) : null;

    // Set cache headers to tell the browser to cache the response for 10 minutes (600 seconds)
    header("Cache-Control: public, max-age=600"); // 600 seconds (10 minutes)
    header("Expires: " . gmdate("D, d M Y H:i:s", time() + 600) . " GMT");

    // Fallback values
    $defaultTitle = "Samåkning med Hitch";
    $defaultDescription = "Följ länken för att komma till resan!";
    $defaultImage = "https://hitchapp.se/res/default_image1.png";
    $defaultUrl = "https://hitchapp.se/trip/$tripId";
    $androidUrl = "https://play.google.com/store/apps/details?id=hitch_app.se&pcampaignid=web_share";
    $iosUrl = "https://apps.apple.com/app/hitch-transport-hitch-hike/id6499305150";
    $tripMessage = "Klicka på resan för att se detaljer om denna och andra resor i Hitch-appen!";

    // Initialize variables for OG tags
    $ogTitle = $defaultTitle;
    $ogDescription = $defaultDescription;
    $ogImage = $defaultImage;
    $ogUrl = $defaultUrl;

    // If tripId is provided, fetch the trip data from the API
    if ($tripId) {
        $apiUrl = "https://hitchapp.se:40890/trip-info/$tripId";

        // Make a GET request to the API to fetch the trip data using curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout after 10 seconds
        $tripDataJson = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Check if the request was successful
        if ($httpCode == 200 && $tripDataJson !== false) {
            $tripData = json_decode($tripDataJson, true);

            if (isset($tripData['origin_city']) && isset($tripData['destination_city']) && isset($tripData['date'])) {
                // Format the date
                $date = new DateTime($tripData['date']);
                $formattedDate = $date->format('d M');

                // Use the data to update the OG tags
                $ogTitle = $formattedDate . " - Samåk mellan " . $tripData['origin_city'] . " och " . $tripData['destination_city'];
                $ogDescription = $tripData['date_string'] . ", följ länken för att se detaljerna och boka resan!";

                // Construct the OG image URL with the query parameters
                $ogImage = "https://hitchapp.se:40890/generate-image?" . http_build_query([
                    'origin_city' => $tripData['origin_city'],
                    'origin_street' => $tripData['origin_street'],
                    'destination_city' => $tripData['destination_city'],
                    'destination_street' => $tripData['destination_street'],
                    'date_string' => $tripData['date_string'],
                ]);

                $ogUrl = "https://hitchapp.se/trip/$tripId";  // The URL of the trip page
                $androidUrl = "https://hitchapp.se:40888/dlhitch3";  // The URL to download the Android app
                $iosUrl = "https://hitchapp.se:40888/dlhitch3";  // The URL to download the iOS app
                // $tripMessage = "Denna resa finns i Hitch-app!<br><br>För att se alla detaljer och boka en plats, ladda ner Hitch nu.";
            } else {
                // Set default OG image if required data is not available
                $ogImage = $defaultImage;
            }
        } else {
            // Set default OG image if API call fails
            $ogImage = $defaultImage;
        }
    }
    ?>
    <meta property="og:title" content="<?php echo htmlspecialchars($ogTitle); ?>" />
    <meta property="og:description" content="<?php echo htmlspecialchars($ogDescription); ?>" />
    <meta property="og:image" content="<?php echo $ogImage; ?>" />
    <meta property="og:url" content="<?php echo $ogUrl; ?>" />
    <meta property="og:type" content="website" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="apple-itunes-app" content="app-id=6499305150, app-argument=hitchapp://trip/<?php echo $tripId; ?>">
    <script src="navbar.js" defer></script>
    <link rel="stylesheet" href="style2.css" />
    <link rel="icon" type="image/x-icon" href="res/favicon.png" />
    <!-- <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> -->
</head>

<body>
    <main>
        <!-- Navbar placeholder -->
        <div id="navbar-placeholder"></div>
        <!-- <br />
        <br />
        <br /> -->

        <!-- <section id="trip">
   
        <section id="trip"> -->
        <div class="containera">
            <!-- <div style="text-align: center;">
                <h1>Välkommen till samåkning med Hitch</h1>
            </div> -->

            <!-- <div id="smsModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:white; padding:20px; border-radius:10px; box-shadow:0 4px 8px rgba(0,0,0,0.2);">
                <h3>Öppna resan i Hitch-appen</h3>
                <p>Ange ditt svenska telefonnummer så skickar vi en länk till din telefon:</p>
                <input type="text" id="phoneNumberInput" placeholder="+467XXXXXXXX" style="width:100%; padding:10px; margin:10px 0; font-size:16px; box-sizing: border-box;" />
                <button id="sendLinkButton" style="padding:10px 20px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer;">Skicka länk</button>
                <button id="cancelButton" style="padding:10px 20px; background:#ccc; color:black; border:none; border-radius:5px; cursor:pointer; margin-left:10px;">Avbryt</button>
            </div> -->

            <div id="smsModal" class="modal">
                <h3>Öppna resan i Hitch-appen</h3>
                <p>Ange ditt svenska telefonnummer så skickar vi en länk till din telefon via SMS (t.ex. +46701234567):</p>
                <input type="text" id="phoneNumberInput" value="+467" placeholder="+467XXXXXXXX" pattern="\+467\d{8}" title="Please enter a valid Swedish phone number starting with +467" />
                <div class="button-container">
                    <button id="sendLinkButton" class="primary-button">Skicka länk</button>
                    <button id="cancelButton" class="secondary-button">Avbryt</button>
                </div>
                <p id="error-message" style="color: red; display: none;">Vänligen ange ett giltigt svenskt telefonnummer som börjar med +467 och har 8 siffror därefter.</p>
            </div>



            <div style="display: flex; justify-content: center; margin-top: 20px;">
                <a onclick="goToTripOrAppstore()">
                    <img src="<?php echo $ogImage; ?>" 
                         alt="Trip Image" 
                         style="
                            width: 100%; 
                            max-width: 600px; 
                            display: block; 
                            margin: auto; 
                            box-shadow: 0px 8px 12px rgba(0, 0, 0, 0.2), 0px 2px 6px rgba(0, 0, 0, 0.1); 
                            border-radius: 10px; 
                            cursor: pointer;"
                    />
                </a>
            </div>

            <h4>
                <p style="text-align: center;"><?php echo $tripMessage; ?></p>
            </h4>

            <div id="download-button-container">
                <div id="desktop-buttons"></div>
            </div>
            <div class="icons-container">
                <a id="android-button" href="<?php echo $androidUrl; ?>" onclick="updateStatus('Android')">
                    <img src="./res/googleplay.svg" width="200" alt="Google Play Store">
                </a>
                <a id="ios-button" href="<?php echo $iosUrl; ?>" onclick="updateStatus('iOS')">
                    <img src="./res/appstore.svg" width="200" alt="App Store">
                </a>
            </div>
            <div id="status-message" style="text-align: center; margin-top: 20px;"></div>

            <p id="facebook-disclaimer" class="disclaimer" style="display: none; color: black;">
                <img src="./img/Facebook_logo_PNG12.png" alt="Facebook Logo" class="facebook-logo">
                <em>Observera: Länkar fungerar inte alltid som förväntat i Facebooks och Instagrams webbläsare... <span style="font-size: 0.8em;">&#128580;</span>
                    Om du redan har installerat appen, försök att markera länken nedan genom att trycka och hålla nere, och öppna Hitch via markeringsmenyn. Om inget fungerar, öppna appen manuellt och sök efter resan. </em>
                <p id="unilink" style="margin-top: 20px; word-wrap: break-word; white-space: normal; display: none;">
                    <!-- The unilink will be inserted here by JavaScript -->
                </p>
            </p>
            <div style="display: flex; justify-content: center; margin-top: 20px;"></div>
                <button id="disclaimer-toggle" style="display: none; background-color: transparent; color: black; border: none; padding: 0; cursor: pointer; font-size: 1em; text-decoration: underline;" onClick="toggleDisclaimer()">
                    ... Appen är nedladdad ...
                </button>
            </div>
        </div>
    </main>
    <div id="footer-placeholder"></div>
    <div id="cookie-banner" style="display: none;">
        <div style="background-color: #416454; padding: 10px; text-align: center; border-top: 1px solid #000000;">
            <p>
                We do not use cookies to track users. For more details, please review our
                <a href="/privacypolicy.html">Privacy Policy</a>.
            </p>
            <button onclick="acceptCookies()">OK</button>
        </div>
    </div>
    <!-- JavaScript code moved to the bottom -->
    <script>
        function goToTripOrAppstore() {
            const userAgent = navigator.userAgent || navigator.vendor || window.opera;
            const tripId = "<?php echo $tripId; ?>";

            if (/android/i.test(userAgent)) {
                window.location.href = `intent://trip/${tripId}#Intent;scheme=hitchapp;package=hitch_app.se;S.browser_fallback_url=https://play.google.com/store/apps/details?id=hitch_app.se&pcampaignid=web_share;end`;
            } else if (/iPhone|iPad|iPod/i.test(userAgent)) {
                // iOS redirection using a hybrid approach
                // iOS redirection using the custom URL scheme
                const appUrl = `hitchapp://trip/${tripId}`;
                const fallbackAppStore = "https://apps.apple.com/app/hitch-transport-hitch-hike/id6499305150";

                // Open the app URL
                window.location.href = appUrl;

                // Show an alert if the app is not opened
                let timeout = setTimeout(() => {
                    // Prompt the user to go to the App Store
                    const userConfirmed = confirm("Senaste versionen av appen verkar inte vara installerad, vill du gå till App Store?");
                    if (userConfirmed) {
                        // If user confirms, redirect to App Store
                        window.location.href = fallbackAppStore;
                    }
                }, 1500); // Timeout after 1.5 seconds (adjust as needed)

                window.addEventListener('focus', () => {
                    clearTimeout(timeout);  // Cancel the App Store redirection if the app is opened
                });
            } else {
                // Desktop fallback alertdialoge that descibbes tht hitch is in your appstore on your mobilephone
                openSmsModal();
                // alert("Hitch finns i din app-butik på din mobiltelefon!");
              
            }
        }

        // Open the modal
        function openSmsModal() {
    const modal = document.getElementById('smsModal');
    modal.style.display = 'block';

    // Handle sending the phone number
    document.getElementById('sendLinkButton').onclick = function () {
        let phoneNumber = document.getElementById('phoneNumberInput').value.replace(/\s+/g, '');
        const errorMessage = document.getElementById('error-message');
        const phonePattern = /^\+467\d{8}$/;

        if (phonePattern.test(phoneNumber)) {
            sendSms(phoneNumber); // Use sendSms function
            modal.style.display = 'none'; // Close modal
            errorMessage.style.display = 'none'; // Hide error message
        } else {
            errorMessage.style.display = 'block'; // Show error message
        }
    };

    // Handle cancel
    document.getElementById('cancelButton').onclick = function () {
        modal.style.display = 'none';
        document.getElementById('error-message').style.display = 'none'; // Hide error message
    };
}


function sendSms(phoneNumber) {
    var tripId = "<?php echo $tripId; ?>";
    var smsApiUrl = "https://hitchapp.se:40890/send-sms";

    fetch(smsApiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            phoneNumber: phoneNumber,
            tripId: tripId,
        }),
    })
    .then(response => response.json()) // Ensure the server always returns JSON
    .then(data => {
        if (data.success) {
            alert("Länken har skickats till ditt telefonnummer!"); // Success
        } else {
            alert(data.message || "Misslyckades att skicka länken. Försök igen."); // Use server-provided message
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Ett fel inträffade. Försök igen."); // General error message
    });
}
        function updateStatus(platform) {
            var statusMessage = document.getElementById('status-message');
            var androidButton = document.getElementById('android-button');
            var iosButton = document.getElementById('ios-button');

            var statusString = '';
            if (platform === 'Android') {
                statusString = 'Dirigerar till Google Play Store...'; // Swedish for "Redirecting to Google Play Store..."
                androidButton.style.display = 'none';
            } else if (platform === 'iOS') {
                statusString = 'Dirigerar till App Store...'; // Swedish for "Redirecting to App Store..."
                iosButton.style.display = 'none';
            }

            // Show a loading indicator
            statusMessage.innerHTML = statusString; // Swedish for "Loading..."
            statusMessage.style.display = 'block';

            // Simulate a delay to show the loading indicator
            setTimeout(function() {
                statusMessage.style.display = 'none';
                // statusMessage.innerHTML = 'Laddning klar!'; // Swedish for "Loading complete!"
                // Show the buttons again
                if (platform === 'Android') {
                    androidButton.style.display = 'block';
                } else if (platform === 'iOS') {
                    iosButton.style.display = 'block';
                }
            }, 2000); // 2 seconds delay
        }

        function toggleDisclaimer() {
    //         setTimeout(function() {
    //   window.location.href = "hitchapp://trip/<?php echo $tripId; ?>";
    // }, 100); // Adjust the delay if needed


            var disclaimer = document.getElementById('facebook-disclaimer');
            var unilink = document.getElementById('unilink');
            var button = document.getElementById('disclaimer-toggle');
            if (disclaimer.style.display === 'none') {
                disclaimer.style.display = 'block';
                unilink.style.display = 'block';
                button.style.display = 'none';
            }
        }

        function loadFooter() {
            fetch('footer.html')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('footer-placeholder').innerHTML = data;
                })
                .catch(error => console.error('Error loading footer:', error));
        }

        function handleCookieBanner() {
            if (!localStorage.getItem('cookiesAccepted')) {
                document.getElementById('cookie-banner').classList.remove('hidden');
            }
        }

        function acceptCookies() {
            localStorage.setItem('cookiesAccepted', 'true');
            var cookieBanner = document.getElementById('cookie-banner');
            cookieBanner.classList.add('hidden');
            console.log("Cookies accepted and banner hidden");
        }
        var userAgent = navigator.userAgent || navigator.vendor || window.opera;
        var isAndroid = /Android/i.test(userAgent);
        var isIOS = /iPhone|iPad|iPod/i.test(userAgent);

        function handleFacebookDisclaimer() {
            var userAgent = navigator.userAgent || navigator.vendor || window.opera;
            console.log("User Agent: " + userAgent);

            var isFacebookBrowser = userAgent.includes("FBAN") || userAgent.includes("FBAV");
            var isInstagramBrowser = userAgent.includes("Instagram");
            console.log("Is Facebook Browser: " + isFacebookBrowser);
            console.log("Is Instagram Browser: " + isInstagramBrowser);

            if (isFacebookBrowser || isInstagramBrowser) {
                document.getElementById("disclaimer-toggle").style.display = "block";
            }
        }

        function createUnilink(tripId) {
            var unilink = 'https://hitchapp.se/trip/' + tripId;

            var linkElement = document.createElement('a');
            linkElement.textContent = unilink;

            if (isIOS) {
                linkElement.href = unilink;
            }
            linkElement.style.color = 'purple';
            linkElement.style.textDecoration = 'underline';
            linkElement.style.cursor = 'pointer';

            linkElement.addEventListener('click', function(e) {
                e.preventDefault();
                var range = document.createRange();
                range.selectNodeContents(linkElement);
                var selection = window.getSelection();
                selection.removeAllRanges();
                selection.addRange(range);
            });

            document.getElementById('unilink').appendChild(linkElement);
        }

        function handleDownloadButton() {
            var androidButton = document.getElementById('android-button');
            var iosButton = document.getElementById('ios-button');
            // var toggleButton = document.getElementById('disclaimer-toggle');
            var desktopButtons = document.getElementById('desktop-buttons');

            console.log("User Agent: ", userAgent); // Debugging line

            if (isAndroid) {
                androidButton.style.display = 'block';
                iosButton.style.display = 'none';
                // toggleButton.style.display = 'block';
                desktopButtons.style.display = 'none';
                console.log("Android detected"); // Debugging line
            } else if (isIOS && !window.MSStream) {
                iosButton.style.display = 'block';
                androidButton.style.display = 'none';
                // toggleButton.style.display = 'block';
                desktopButtons.style.display = 'none';
                console.log("iOS detected"); // Debugging line
            } else {
                androidButton.style.display = 'none';
                iosButton.style.display = 'none';
                // toggleButton.style.display = 'none';
                insertDesktopButtons();
                console.log("Desktop browser detected"); // Debugging line
            }
        }

        // Function to insert desktop buttons
        function insertDesktopButtons() {
            var desktopButtons = document.getElementById('desktop-buttons');
            desktopButtons.innerHTML = `
        <a href="https://play.google.com/store/apps/details?id=hitch_app.se&pcampaignid=web_share" class="desktop-buttons">
            <img src="./res/googleplay.svg" width="200" alt="Google Play Store" />
        </a>
        <a href="https://apps.apple.com/app/hitch-transport-hitch-hike/id6499305150" class="desktop-buttons">
            <img src="./res/appstore.svg" width="200" alt="App Store" />
        </a>
    `;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // var tripId = '<?php echo $tripId; ?>';

            // if (tripId && (isIOS || isAndroid)) {
            //     createUnilink(tripId);
            // }
            handleDownloadButton();
            loadFooter();
            handleCookieBanner();
            // handleFacebookDisclaimer();
        });
    </script>
</body>

</html>
