async function showAuthor(id) {
    const headers = new Headers();
    headers.append("Content-Type", "application/json");

    const request = new Request("?command=get-author&resource-id=1", {
        method: "GET",
        headers: headers,
    });

    const response = await fetch(request);
    if (response.status != 200) {
        console.log("Server error for guess " + guess);
        return false;
    }

    var result = await response.json();
    var author = result.author;
    $( "[data-show-author-id=\"" + id + "\"]" ).html("author: " + author);
}

function hideAuthor(id) {
    $( "[data-show-author-id=\"" + id + "\"]" ).html("");
}