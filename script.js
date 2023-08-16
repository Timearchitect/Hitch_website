import {initializeApp} from 'firebase/app';
import {getDatabase} from 'firebase/database';
import {getAnalytics} from 'firebase/analytics';

const firebaseConfig = {
     authDomain: "hitch-e4a25.firebaseapp.com",
     databaseURL: "https://hitch-e4a25-default-rtdb.europe-west1.firebasedatabase.app",
     projectId: "hitch-e4a25",
     storageBucket: "hitch-e4a25.appspot.com",
     messagingSenderId: "248317208047",
     appId: "1:248317208047:web:7a5ec6b32c11b8fd37821a",
     measurementId: "G-6Z28FCR9Y1"
    };

const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
const database = getDatabase(app);


