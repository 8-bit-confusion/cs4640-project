function queryAuthor(id) {
    return new Promise(resolve => {
        var ajax = new XMLHttpRequest();
        ajax.open("GET", "?command=get-author&resource-id=" + id, true);
        ajax.responseType = "json";
        ajax.send(null);
        
        ajax.addEventListener("load", function() {
            if (this.status == 200) {
                resolve(this.response);
            } else {
                console.log("Could not get author (HTTP error code)");
            }
        });
        
        ajax.addEventListener("error", function() {
            console.log("Could not get author (internal error)");
        });
    });
}

async function showAuthor(id) {
    var author_result = await queryAuthor();
    var author = author_result.author;
    $( "[data-show-author-id=\"" + id + "\"]" ).html(author);
}

function hideAuthor(id) {
    $( "[data-show-author-id=\"" + id + "\"]" ).html("");
}