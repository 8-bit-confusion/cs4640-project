function cancelComment(comment_id) {
    $( "[data-comment-id=\"" + comment_id + "\"].comment ~ .comment-reply" ).hide();
    $( "[data-comment-id=\"" + comment_id + "\"].comment ~ .flex-row > .link-button" ).show();
}

function replyComment(comment_id) {
    $( "[data-comment-id=\"" + comment_id + "\"].comment ~ .comment-reply" ).show();
    $( "[data-comment-id=\"" + comment_id + "\"].comment ~ .flex-row > .link-button" ).hide();
}