
import {initializeApp} from 'firebase/app';
import {getDatabase, ref, set} from 'firebase/database';
import {getAnalytics} from 'firebase/analytics';


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
const db = getDatabase(app);


const name = document.getElementById("first-name-text")
const surname = document.getElementById("surnam-text")
const email = document.getElementById("email-text")
const signUpBtn = document.getElementById("signup-button")

function writeToDb(userId, firstname, surname, email) {
    set(ref(db, 'users/' + userId), {
        firstname: firstname,
        surname: surname,
        email: email,
    }, (error) => {
        if(error) {
            console.log('Data could not be saved ' + error + '.')
        }else {
            console.log('Data has been saved.')
        }
    });
}

async function getFormData() {

    const response = await fetch();
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


signUpBtn.addEventListener("click", async () => {

})

