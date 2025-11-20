function validateRegister() {
    const form = document.getElementById("create-form");
    // It seems there are many ways to link javascript functions to forms
    // We can use onsubmit='', EventListener, etc. 
    // Which is best? How do they all interact?


    form.addEventListener('submit', function(e) {
        const username = document.getElementById('username').value.trim();
        const email = document.getElementById('email').value.trim();
        const display_name = document.getElementById('display_name').value;
        const password = document.getElementById('pwd').value;
        const retype_pwd = document.getElementById('retype_pwd').value;

        if(username.length < 8){
            // check constaints of username and other variables
        }
    })


}


function validateItem() {
    const title = document.getElementById('resource-title').value.trim();
    const description = document.getElementById('resource-description').value.trim();
    const files = document.getElementById('resource-files').value;
    const tags = document.getElementById('resource-tags').value;




}

