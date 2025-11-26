//create new article js logic

const apiPath = window.location.origin + '/api/';

document.getElementById('addArticle').addEventListener('submit', async (e) => {
    e.preventDefault(); //stop refresh

    //take all form
    const form = e.target;
    const formData = new FormData(form); 

    //send to php
    try {
        const req = await fetch(`${apiPath}articles/create-article.php`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Accept': "application/json"
            },
            body: formData
        });

        if(!req.ok) {
            throw new Error(`HTTP Error! Status:${req.status}`);
        }

        //get answer from php
        const result = await req.json();

        //result message(json answer from php)
        const msgBlock = document.getElementById('articles__status');

        if(result.status === "ok") {
            msgBlock.innerHTML = `<p style="color:green">${result.msg}</p>`;
            form.reset(); //reset fields
        } else {
            msgBlock.innerHTML = `<p style="color:red">${result.msg}</p>`;
        }

    } catch(err) {
        console.error(err);
    }
});