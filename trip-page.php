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
$defaultImage = "https://hitchapp.se/res/default_image.png";
$defaultUrl = "https://hitchapp.se/trip/$tripId";

// Initialize variables for OG tags
$ogTitle = $defaultTitle;
$ogDescription = $defaultDescription;
$ogImage = $defaultImage;
$ogUrl = $defaultUrl;

// If tripId is provided, fetch the trip data from the API
if ($tripId) {
    $apiUrl = "https://hitchapp.se:40890/trip-info/$tripId";

    // Make a GET request to the API to fetch the trip data
    $tripDataJson = @file_get_contents($apiUrl);

    // Check if the request was successful
    if ($tripDataJson !== false) {
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
    <script src="hamburger.js" defer></script>
    <link rel="stylesheet" href="style1.css" />
    <link rel="icon" type="image/x-icon" href="res/favicon.png" />
    <!-- <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> -->
  

</head>

<body>
    <main>
        <!--navbar-->
        <nav class="navbar">
            <a href="index.html"><img class="navbar-logo" src="res/Logo.png" alt="Hitch logo" /></a>
            <ul class="nav-menu">
                <li nav-item><a href="index.html">Hem</a></li>
                <li nav-item><a href="about-us.html">Om oss</a></li>
                <li nav-item><a href="privacypolicy.html">Integritetspolicy</a></li>
                <!-- <li nav-item><a href="https://hitch-e4a25.firebaseapp.com/">Web-App (Sök)</a></li> -->
                <li nav-item><a href="contact-us.html">Kontakta Oss</a></li>
                <li nav-item><a href="<?php echo $ogUrl; ?>">Resa</a></li>
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </nav>

        <section id="trip">
            <div style="text-align: center;">
                <h1><?php echo $ogTitle; ?></h1>
            </div>

            <div style="display: flex; justify-content: center; margin-top: 20px;">
            
            <img src="<?php echo $ogImage; ?>" alt="Trip Image" style="width:100%; max-width:600px; display:block; margin:auto;">
   
    </div>
            <p style="text-align: center;">För att se alla detaljer och boka din plats, ladda ner Hitch-app nu.</p>

            <div id="download-button-container">
                <!-- <a id="android-button" href="https://play.google.com/store/apps/details?id=hitch_app.se&pcampaignid=web_share" class="download-button" style="display: none;">
                    <img src="./res/google-play-icon.svg" alt="Google Play Store" class="icon" />
                    <span class="button-text">Ladda ner</span>
                </a>
                <a id="ios-button" href="https://apps.apple.com/app/hitch-transport-hitch-hike/id6499305150" class="download-button" style="display: none;">
                    <img src="./res/app-store-icon.svg" alt="App Store" class="icon" />
                    <span class="button-text">Ladda ner</span>
                </a> -->
                <div id="desktop-buttons"></div>
            </div>
            <div class="icons-container">
                <a id="android-button" href="https://hitchapp.se:40888/dlhitch3" onclick="updateStatus('Android')">
                    <img src="./res/googleplay.svg" width="200" alt="Google Play Store">
                </a>
                <a id="ios-button" href="https://hitchapp.se:40888/dlhitch3" onclick="updateStatus('iOS')">
                    <img src="./res/appstore.svg" width="200" alt="App Store">
                </a>
            </div>
            <div id="status-message" style="text-align: center; margin-top: 20px;"></div>
          
            <!-- <p style="text-align: center;">Om du fortfarande kommer hit fast att du har installerat appen.</p> -->

         
         

         
          
           
            <p id="facebook-disclaimer" class="disclaimer" style="display: none;">
                <img src="./img/Facebook_logo_PNG12.png" alt="Facebook Logo" class="facebook-logo">
                <em>Observera: Länkar fungerar inte alltid som förväntat i Facebooks och Instagrams webbläsare... <span style="font-size: 0.8em;">&#128580;</span> 
                Om du redan har installerat appen, försök att markera länken nedan genom att trycka och hålla nere, och öppna Hitch via markeringsmenyn. Om inget fungerar, öppna appen manuellt och sök efter resan. </em>  
                <p id="unilink" style="margin-top: 20px; word-wrap: break-word; white-space: normal; display: none;">
                    <!-- The unilink will be inserted here by JavaScript -->
                </p>
            </p>
            <div style="display: flex; justify-content: center; margin-top: 20px;">
                <button id="disclaimer-toggle" style="display: none; background-color: transparent; color: #416454; border: none; padding: 0; cursor: pointer; font-size: 1em; text-decoration: underline;" onClick="toggleDisclaimer()">
                    ... Appen är nedladdad ...
                </button>
            </div>

          

          
        </section>

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
    var disclaimer = document.getElementById('facebook-disclaimer');
    var unilink = document.getElementById('unilink');
    var button = document.getElementById('disclaimer-toggle');
    if (disclaimer.style.display === 'none') {
        disclaimer.style.display = 'block';
        unilink.style.display = 'block';
        button.style.display = 'none';
        
    //     button.textContent = 'Dölj Disclaimer';
    // } else {
    //     disclaimer.style.display = 'none';
    //     unilink.style.display = 'none';
    //     button.textContent = 'Visa Disclaimer';
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
        linkElement.style.color = 'green';
        linkElement.style.textDecoration = 'underline';
        linkElement.style.cursor = 'pointer';

        linkElement.addEventListener('click', function (e) {
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
            var toggleButton = document.getElementById('disclaimer-toggle');
            var desktopButtons = document.getElementById('desktop-buttons');

            console.log("User Agent: ", userAgent); // Debugging line

            if (isAndroid) {
                androidButton.style.display = 'block';
                iosButton.style.display = 'none';
                toggleButton.style.display = 'block';
                desktopButtons.style.display = 'none';
                console.log("Android detected"); // Debugging line
            } else if (isIOS && !window.MSStream) {
                iosButton.style.display = 'block';
                androidButton.style.display = 'none';
                toggleButton.style.display = 'block';
                desktopButtons.style.display = 'none';
                console.log("iOS detected"); // Debugging line
            } else {
                androidButton.style.display = 'none';
                iosButton.style.display = 'none';
                toggleButton.style.display = 'none';
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
        var tripId = '<?php echo $tripId; ?>';

        if (tripId && (isIOS || isAndroid)) {
            createUnilink(tripId);
        }
        handleDownloadButton();
        loadFooter();
        handleCookieBanner();
        handleFacebookDisclaimer();
    });
</script>
</body>
</html>