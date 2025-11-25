document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector(".register-form");

    const usernameInput    = document.getElementById("username");
    const emailInput       = document.getElementById("email");
    const displayNameInput = document.getElementById("display_name");
    const pwdInput         = document.getElementById("pwd");
    const retypePwdInput   = document.getElementById("retype_pwd");

    // Put errors near the existing feedback span if present,
    // otherwise append to form.
    let feedbackSpan = document.querySelector(".feedback");
    let errorBox = document.createElement("div");
    errorBox.style.color = "rgba(255, 13, 0, 1)";
    errorBox.style.marginTop = "12px";
    errorBox.style.fontWeight = "bold";

    if (feedbackSpan) {
        feedbackSpan.insertAdjacentElement("afterend", errorBox);
    } else {
        form.appendChild(errorBox);
    }

    function clearFieldErrors() {
        [usernameInput, emailInput, displayNameInput, pwdInput, retypePwdInput]
            .forEach(input => {
                if (!input) return;
                input.classList.remove("input-error");
                input.style.outline = "";
            });
    }

    function markError(input) {
        if (!input) return;
        input.classList.add("input-error");
        input.style.outline = "2px solid rgba(143, 11, 4, 1)";
    }

    form.addEventListener("submit", (event) => {
        errorBox.textContent = "";
        clearFieldErrors();

        let errors = [];

        // Username: required, min length 3
        const username = usernameInput.value.trim();
        if (username.length < 3) {
            errors.push("Username must be at least 3 characters long.");
            markError(usernameInput);
        if (username.length > 12) {
            errors.push("Username cannot be longer than 12 characters.");
            markError(usernameInput);
        }
        }

        // Email: rely on browser's type=email but also do a simple check
        const email = emailInput.value.trim();
        if (email.length === 0) {
            errors.push("Email is required.");
            markError(emailInput);
        } else {
            const emailRegex = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
            if (!emailRegex.test(email)) {
                errors.push("Please enter a valid email address.");
                markError(emailInput);
            }
        }

        // Display name: required, some non-whitespace
        const displayName = displayNameInput.value.trim();
        if (displayName.length === 0) {
            errors.push("Display name is required.");
            markError(displayNameInput);
        }

        const pwd = pwdInput.value;
        if (pwd.length < 8) {
            errors.push("Password must be at least 8 characters long.");
            markError(pwdInput);
        }

        const retypePwd = retypePwdInput.value;
        if (retypePwd !== pwd) {
            errors.push("Passwords do not match.");
            markError(retypePwdInput);
        }

        if (errors.length > 0) {
            event.preventDefault();
            errorBox.textContent = errors.join(" ");
        }
    });
});
