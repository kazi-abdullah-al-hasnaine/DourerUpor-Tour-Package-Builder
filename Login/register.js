// Firebase Imports
import { initializeApp } from "https://www.gstatic.com/firebasejs/9.4.1/firebase-app.js";
import { getAuth, GoogleAuthProvider, GithubAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/9.4.1/firebase-auth.js";

// Firebase Config
const firebaseConfig = {
    apiKey: "AIzaSyDa_hdvFe1S-k4MvMNMqs8XojC_ohr3oNw",
    authDomain: "dourerupor-327.firebaseapp.com",
    projectId: "dourerupor-327",
    storageBucket: "dourerupor-327.appspot.com",
    messagingSenderId: "305475572284",
    appId: "1:305475572284:web:4399c25a2012fa56f22939"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
auth.languageCode = 'en';

// Google Login
const googleProvider = new GoogleAuthProvider();
const googleLogin = document.getElementById("google-login-btn");

if (googleLogin) {
    googleLogin.addEventListener("click", function () {
        signInWithPopup(auth, googleProvider)
            .then((result) => {
                const user = result.user;

                const userData = {
                    "google-login": true,
                    "email": user.email,
                    "name": user.displayName
                };

                fetch("user-actions.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(userData)
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Google login: Server Response:", data);
                    window.location.href = "../home.php";
                })
                .catch(error => {
                    console.error("Google login: Error sending to backend:", error);
                    window.location.href = "../home.php";
                });

            })
            .catch((error) => {
                console.error("Google login error:", error);
                window.location.href = "../home.php";
            });
    });
}

// GitHub Login
const githubProvider = new GithubAuthProvider();
const githubLogin = document.getElementById("github-login-btn");

if (githubLogin) {
    githubLogin.addEventListener("click", function () {
        signInWithPopup(auth, githubProvider)
            .then((result) => {
                const user = result.user;

                const userData = {
                    "github-login": true,
                    "email": user.email,
                    "name": user.displayName
                };

                fetch("user-actions.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(userData)
                })
                .then(response => response.json())
                .then(data => {
                    console.log("GitHub login: Server Response:", data);
                    window.location.href = "../home.php";
                })
                .catch(error => {
                    console.error("GitHub login: Error sending to backend:", error);
                    window.location.href = "../home.php";
                });

            })
            .catch((error) => {
                console.error("GitHub login error:", error);
                window.location.href = "../home.php";
            });
    });
}
