import { initializeApp } from "https://www.gstatic.com/firebasejs/9.4.1/firebase-app.js";
import { getAuth, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/9.4.1/firebase-auth.js";

const firebaseConfig = {
    apiKey: "AIzaSyDa_hdvFe1S-k4MvMNMqs8XojC_ohr3oNw",
    authDomain: "dourerupor-327.firebaseapp.com",
    projectId: "dourerupor-327",
    storageBucket: "dourerupor-327.firebasestorage.app",
    messagingSenderId: "305475572284",
    appId: "1:305475572284:web:4399c25a2012fa56f22939"
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
auth.languageCode = 'en';

const provider = new GoogleAuthProvider();
const googleLogin = document.getElementById("google-login-btn");

googleLogin.addEventListener("click", function () {
    signInWithPopup(auth, provider)
        .then((result) => {
            const credential = GoogleAuthProvider.credentialFromResult(result);
            const token = credential.accessToken;
            const user = result.user;

            // Log user data (for debugging purposes)
            console.log("User Info:", user);

            // Prepare data to send to PHP backend
            const userData = {
                "google-login": true,
                "email": user.email,
                "name": user.displayName
            };

            // Send user data to PHP backend (user-actions.php)
            fetch("user-actions.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(userData)

            })
                .then(response => response.json()) // Assuming the server returns JSON
                .then(data => {
                    console.log("Server Response:", data);
             

                })
                .catch(error => {
                    console.error("Error during fetch:", error);
                });
        })
        .catch((error) => {
            console.error("Error Code:", error.code);
            console.error("Error Message:", error.message);
        });
});
