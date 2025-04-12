import { initializeApp } from "https://www.gstatic.com/firebasejs/9.4.1/firebase-app.js";
import { getAuth, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/9.4.1/firebase-auth.js";
import { GithubAuthProvider } from "https://www.gstatic.com/firebasejs/9.4.1/firebase-auth.js";

const firebaseConfig = {
    apiKey: "AIzaSyDa_hdvFe1S-k4MvMNMqs8XojC_ohr3oNw",
    authDomain: "dourerupor-327.firebaseapp.com",
    projectId: "dourerupor-327",
    storageBucket: "dourerupor-327.firebaseapp.com",
    messagingSenderId: "305475572284",
    appId: "1:305475572284:web:4399c25a2012fa56f22939"
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
auth.languageCode = 'en';

// Google login setup
const googleProvider = new GoogleAuthProvider();
const googleLogin = document.getElementById("google-login-btn");
googleLogin.addEventListener("click", function () {
    signInWithPopup(auth, googleProvider)
        .then((result) => {
            const credential = GoogleAuthProvider.credentialFromResult(result);
            const token = credential.accessToken;
            const user = result.user;
            
            // Log user data (for debugging purposes)
            console.log("Google User Info:", user);
            
            // Prepare data to send to PHP backend
            const userData = {
                "google-login": true,
                "email": user.email,
                "name": user.displayName
            };
            
            // Send user data to PHP backend
            sendUserDataToBackend(userData);
        })
        .catch((error) => {
            console.error("Error Code:", error.code);
            console.error("Error Message:", error.message);
            window.location.href = "../home.php";
            // Handle error
        });
});

// GitHub login setup
const githubProvider = new GithubAuthProvider();
const githubLogin = document.getElementById("github-login-btn");
githubLogin.addEventListener("click", function () {
    signInWithPopup(auth, githubProvider)
        .then((result) => {
            const credential = GithubAuthProvider.credentialFromResult(result);
            const token = credential.accessToken;
            const user = result.user;
            
            // Log user data (for debugging purposes)
            console.log("GitHub User Info:", user);
            
            // Prepare data to send to PHP backend
            const userData = {
                "github-login": true,
                "email": user.email,
                "name": user.displayName
            };
            
            // Send user data to PHP backend
            sendUserDataToBackend(userData);
        })
        .catch((error) => {
            console.error("GitHub Error Code:", error.code);
            console.error("GitHub Error Message:", error.message);
            window.location.href = "../home.php";
            // Handle error
        });
});

// Helper function to send user data to backend
function sendUserDataToBackend(userData) {
    fetch("user-actions.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(userData)
    })
    .then(response => {
        console.log("Server Response Status:", response.status);
        window.location.href = "../home.php";
    })
    .catch(error => {
        console.error("Error during fetch:", error);
        window.location.href = "../home.php";
        // Handle error
    });
}