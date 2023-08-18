import { initializeApp } from 'firebase/app'
import { getDatabase, ref, set } from 'firebase/database'
import { getAnalytics, logEvent } from 'firebase/analytics'

/*
var initializeApp = require('firebase/app')
var getDatabase = require('firebase/database')
var ref = require('firebase/database')
var set = require('firebase/database')
var getAnalytics = require('firebase/analytics')
var logEvent = require('firebase/analytics')
*/


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
const analytics = getAnalytics(app);
analytics.isSupported()

const db = getDatabase(app);


let name = document.getElementById("first-name-text")
let surname = document.getElementById("surnam-text")
let email = document.getElementById("email-text")
let form = document.getElementById("form-handler")


async function writeToDb(userId, firstname, surname, email) {
    await set(ref(db, 'users/' + userId), {
        firstname: firstname,
        surname: surname,
        email: email,
    }, (error) => {
        if (error) {
            console.error('Data could not be saved ' + error + '.')
        } else {
            console.error('Data has been saved.')
        }
    });
}

/*
Database structure:
    "users": {
        "id": {
            firstname: ,
            surname: ,
            email: ,
        }
    }

*/
form.addEventListener("submit", e =>{
    e.preventDefault();
    let uid = 0
    
    try{
        if(name == "" || surname == "" || email == ""){
            alert("Please enter first name, surname and email to proceed")
        } else {
            uid +=1
            writeToDb(uid, name.innerText, surname.innerText, email.innerText)
        }
        logEvent(analytics, "sign_up")
    }catch (error) {
        console.error(error)
    }

});

