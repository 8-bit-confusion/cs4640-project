window.onload = function () {
    let pfp_anchor = document.getElementById("explore-pfp-anchor");
    let pfp_menu = document.getElementById("explore-pfp-menu");
    pfp_anchor.addEventListener("mouseover", (event) => pfp_menu.style.visibility = "visible");
    pfp_anchor.addEventListener("mouseout", (event) => pfp_menu.style.visibility = "hidden");
}