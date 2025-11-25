window.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("welcome-login-form");
    if (!form) return;

    const username = document.getElementById("wl-username");
    const password = document.getElementById("wl-password");
    const feedback = document.querySelector(".feedback");

    // to track login state (s4 req)
    const loginState = {
        field: "",
        lastAttempt: null,
    };

    // dynamic behavior: highlight on focus (style manipulation)
    const highlight = (input) => {
        input.style.boxShadow = "0 0 0 2px rgba(25,118,210,0.8)";
    };

    const removeHighlight = (input) => {
        input.style.boxShadow = "none";
    };

    username.addEventListener("focus", () => highlight(username));
    username.addEventListener("blur", () => removeHighlight(username));
    password.addEventListener("focus", () => highlight(password));
    password.addEventListener("blur", () => removeHighlight(password));

    // login validation
    form.addEventListener("submit", (e) => {
        let errors = [];

        const userVal = username.value.trim();
        const passVal = password.value.trim();

        loginState.field = userVal;
        loginState.lastAttempt = new Date();

        // basic validations
        if (userVal === "") {errors.push("Username is required.");}

        if (passVal === "") {errors.push("Password is required.");}

        if (errors.length > 0) {
            e.preventDefault(); // stop PHP submit

            // DOM update for error
            if (feedback) {
                feedback.textContent = errors[0];
                feedback.style.color = "#b3261e";
            }
            form.classList.add("has-errors");
            
        } else {
            if (feedback) feedback.textContent = "";
            form.classList.remove("has-errors");
        }
    });
});