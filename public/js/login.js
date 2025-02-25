const SECRET_KEY = "your_secret_key"; // Gantilah dengan key yang lebih aman

function showRememberPopup() {
    if (
        localStorage.getItem("remember_username") &&
        localStorage.getItem("remember_password")
    ) {
        document.getElementById("rememberPopup").style.display = "block";
    } else {
        alert("Tidak ada data login tersimpan.");
    }
}

function closePopup() {
    document.getElementById("rememberPopup").style.display = "none";
}

function autoFillLogin() {
    let savedUsername = localStorage.getItem("remember_username");
    let encryptedPassword = localStorage.getItem("remember_password");

    if (savedUsername && encryptedPassword) {
        document.getElementById("username").value = savedUsername;
        document.getElementById("password").value = CryptoJS.AES.decrypt(
            encryptedPassword,
            SECRET_KEY
        ).toString(CryptoJS.enc.Utf8); // Dekripsi password
    }
    closePopup();
}

// Simpan username & password saat login (dengan enkripsi AES)
document.querySelector("form").addEventListener("submit", function (event) {
    let remember = document.getElementById("remember").checked;
    if (remember) {
        let username = document.getElementById("username").value;
        let password = document.getElementById("password").value;

        localStorage.setItem("remember_username", username);
        localStorage.setItem(
            "remember_password",
            CryptoJS.AES.encrypt(password, SECRET_KEY).toString()
        ); // Enkripsi AES
    } else {
        localStorage.removeItem("remember_username");
        localStorage.removeItem("remember_password");
    }
});
