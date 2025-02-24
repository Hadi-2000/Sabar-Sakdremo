function showPassword() {
    const password = document.getElementById("password");
    const togglePassword = document.querySelector("#togglePassword i");

    if (password.type === "password") {
        password.type = "text";
        togglePassword.classList.remove("fa-eye");
        togglePassword.classList.add("fa-eye-slash");
    } else {
        password.type = "password";
        togglePassword.classList.remove("fa-eye-slash");
        togglePassword.classList.add("fa-eye");
    }

    password.focus();
    password.setSelectionRange(password.value.length, password.value.length);
}
