    // Notif Overlay
    function showNotif(message) {
        const overlay = document.getElementById("notifOverlay");
        const messageBox = document.getElementById("notifMessage");
        messageBox.textContent = message;
        overlay.classList.remove("hidden");
    }
    
    function closeNotif() {
        document.getElementById("notifOverlay").classList.add("hidden");
    }

document.addEventListener("DOMContentLoaded", function () {
    // Toggle form login/sign-up
    const signInBtn = document.getElementById("sign-in");
    const signUpBtn = document.getElementById("sign-up");
    const loginInForm = document.getElementById("login-in");
    const loginUpForm = document.getElementById("login-up");

    signUpBtn.addEventListener("click", () => {
        loginInForm.classList.add("none");
        loginUpForm.classList.remove("none");
    });

    signInBtn.addEventListener("click", () => {
        loginInForm.classList.remove("none");
        loginUpForm.classList.add("none");
    });

    // Validasi Sign In
    loginInForm.querySelector(".login__button").addEventListener("click", function (e) {
        e.preventDefault(); // Tetap dicegah dulu

        const username = loginInForm.querySelector("input[placeholder='Username']").value.trim();
        const password = loginInForm.querySelector("input[placeholder='Password']").value.trim();

        if (!username || !password) {
            showNotif("Username dan Password harus diisi.");
            return;
        }

        if (password.length < 6) {
            showNotif("Password minimal 6 karakter.");
            return;
        }

        // Kirim ke server
        loginInForm.submit(); 
    });

    // Validasi Sign Up
    loginUpForm.querySelector(".login__button").addEventListener("click", function (e) {
        e.preventDefault(); // Mencegah submit sebelum validasi

        const username = loginUpForm.querySelector("input[name='username']").value.trim();
        const email = loginUpForm.querySelector("input[name='email']").value.trim();
        const password = loginUpForm.querySelector("input[name='password']").value.trim();

        if (!username || !email || !password) {
            showNotif("Semua field harus diisi.");
            return;
        }

        if (!validateEmail(email)) {
            showNotif("Format email tidak valid.");
            return;
        }

        if (password.length < 6) {
            showNotif("Password minimal 6 karakter.");
            return;
        }

        // Kirim data ke server (register.php)
        loginUpForm.submit();
    });

    function validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
});