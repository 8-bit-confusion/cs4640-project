window.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("profile-form");
    const bio = document.getElementById("pf-bio");
    const displayInput = document.getElementById("pf-display");
    const counter = document.getElementById("bio-counter");

    if (!form || !bio || !counter) return;

    // obj to track the draft state
    const profileDraft = {
        displayName: displayInput ? displayInput.value : "",
        bio: bio.value,
        lastUpdated: null,
    };

    const maxBio = parseInt(bio.getAttribute("maxlength") || "200", 10);

    // helper to update the counter text + style
    const updateBioCounter = () => {
        const length = bio.value.length;
        counter.textContent = `${length} / ${maxBio} characters`;

        profileDraft.bio = bio.value;
        profileDraft.lastUpdated = new Date();

        const remaining = maxBio - length;
        if (remaining <= 20) {counter.style.color = "#b3261e"; // red-ish but change as needed
        } else {counter.style.color = "";
        }
    };

    updateBioCounter();

    // DOM manipulation + event listener
    bio.addEventListener("input", updateBioCounter);

    // subtle highlight behavior for display name (style change)
    const highlight = (el) => {el.style.boxShadow = "0 0 0 2px rgba(25,118,210,0.8)";};
    const clearHighlight = (el) => {el.style.boxShadow = "none";};

    if (displayInput) {
        displayInput.addEventListener("focus", () => highlight(displayInput));
        displayInput.addEventListener("blur", () => clearHighlight(displayInput));
    }
});