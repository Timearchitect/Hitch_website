import { initializeApp } from "https://www.gstatic.com/firebasejs/10.2.0/firebase-app.js";
import { getDatabase, ref, set } from "https://www.gstatic.com/firebasejs/10.2.0/firebase-database.js";
import { getAnalytics, logEvent } from "https://www.gstatic.com/firebasejs/10.2.0/firebase-analytics.js";


const firebaseConfig = {
    apiKey: "AIzaSyDjWjuCwnIyNFkH1K5Kfy1DAbn_jkyXssw",
    authDomain: "hitch-e4a25.firebaseapp.com",
    databaseURL: "https://hitch-e4a25-default-rtdb.europe-west1.firebasedatabase.app",
    projectId: "hitch-e4a25",
    storageBucket: "hitch-e4a25.appspot.com",
    messagingSenderId: "248317208047",
    appId: "1:248317208047:web:7a5ec6b32c11b8fd37821a",
    measurementId: "G-6Z28FCR9Y1",
};

const app = initializeApp(firebaseConfig);
const analyze = getAnalytics(app);
const db = getDatabase(app);
console.log(db);


document.getElementById('form-handler').addEventListener("submit", onFormSubmit)

function onFormSubmit(e) {
    e.preventDefault()

    let name = document.querySelector('#firstname').value()
    let surname = document.querySelector('#surname').value()
    let email = document.querySelector('#email').value()
    let id = 1;
    writeToDb(id, name, surname, email)
    id++
    logEvent(analyze, form)
}
async function writeToDb(id, name, surname, email) {

    await set(ref(db, 'users/'+ id), {
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



