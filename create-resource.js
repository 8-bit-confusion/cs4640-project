document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("create-form");
    const titleInput = document.getElementById("resource-title");
    const tagsInput = document.getElementById("resource-tags");

    // create error box!
    let errorBox = document.createElement("div");
    errorBox.style.color = "rgba(255, 13, 0, 1)";
    errorBox.style.marginTop = "12px";
    errorBox.style.fontWeight = "bold";
    form.appendChild(errorBox);

    form.addEventListener("submit", (event) => {

        errorBox.textContent = ""; // clear old errors
        let errors = [];

        if (titleInput.value.trim().length === 0) {
            errors.push("Title is required.");
        }

        const tagValue = tagsInput.value.trim();
        if (tagValue.length > 0) {

            const tagRegex = /^[A-Za-z_]+(\s+[A-Za-z_]+)*$/;

            if (!tagRegex.test(tagValue)) {
                errors.push("Tags must be space-separated strings with only letters and underscores.");
            }
        }

        if (errors.length > 0) {
            event.preventDefault();    
            // doesn't send form
            errorBox.textContent = errors.join(" ");
            return;
        }
    });
});
