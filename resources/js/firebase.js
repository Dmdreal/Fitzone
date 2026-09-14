// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyCYUQjlgerfGRyYpMMZbX0AOw3zosVGvbQ",
  authDomain: "Ironsidewebsite-a8b4b.firebaseapp.com",
  projectId: "Ironsidewebsite-a8b4b",
  storageBucket: "Ironsidewebsite-a8b4b.firebasestorage.app",
  messagingSenderId: "812695645097",
  appId: "1:812695645097:web:fdc98084386cc8e25af2b8",
  measurementId: "G-SKCBEJ1HMT"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);