// Your main script

import { initializeApp } from "https://www.gstatic.com/firebasejs/10.2.0/firebase-app.js";
import { getDatabase, ref, set, push } from "https://www.gstatic.com/firebasejs/10.2.0/firebase-database.js";
import { getAnalytics, logEvent } from "https://www.gstatic.com/firebasejs/10.2.0/firebase-analytics.js";
import { getApiSecret } from "./googlecouldsm.js"; // Import the function to fetch the API key


firebaseConfig = {
    apiKey: "placeholder",
    authDomain: "hitch-e4a25.firebaseapp.com",
    databaseURL: "https://hitch-e4a25-default-rtdb.europe-west1.firebasedatabase.app",
    projectId: "hitch-e4a25",
    storageBucket: "hitch-e4a25.appspot.com",
    messagingSenderId: "248317208047",
    appId: "1:248317208047:web:7a5ec6b32c11b8fd37821a",
    measurementId: "G-6Z28FCR9Y1",
};

async function initializeFirebase() {
  const apiKey = await getApiSecret(); // Fetch the API key from the secret manager
  if (!apiKey) {
    console.error('Failed to fetch API key.');
    return;
  }

  firebaseConfig.apiKey = apiKey; // Update the apiKey property in firebaseConfig

  const app = initializeApp(firebaseConfig);
  const analyze = getAnalytics(app);
  const db = getDatabase(app);

  // The rest of your script...
}

initializeFirebase(); // Call the initialization function


const form = document.getElementById('form-handler').addEventListener("submit", onFormSubmit)
console.log(form)
function onFormSubmit(e) {
    e.preventDefault()

    let name = document.querySelector('#firstname').value
    let surname = document.querySelector('#surname').value
    let email = document.querySelector('#email').value

    writeToDb( name, surname, email)


}
async function writeToDb(name, surname, email) {

    await push(ref(db, 'users/'), {
        firstname: name,
        surname: surname,
        email: email
    }).then(() => {
        document.getElementById('form-handler').reset()
    }).catch((error) => {
        alert(error)
        console.error(error)
    });

}


// /*
// Database structure:
//     "users": {
//         "id": {
//             firstname: ,
//             surname: ,
//             email: ,
//         }
//     }

// */



