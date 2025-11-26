//approve or decline article js logic

document.querySelector('.articles__block').addEventListener('click', async (e) => {
    e.preventDefault(); //stop refresh

    if(e.target.id === 'approve' || e.target.id === 'decline') {
        const articleBlock = e.target.closest('.articles__article');
        const articleId = articleBlock.dataset.type;
        const jsonForm = {
            decision: e.target.id,
            articleId: articleId 
        };

        try {
            const req = await fetch(`${apiPath}articles/approve-decline.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(jsonForm)
            });

            if(!req.ok) {
                throw new Error(`HTTP Error! Status:${req.status}`);
            }

            //get answer from php
            const res = await req.json();

            if(res.status === 'error') {
                throw new Error(`HTTP Error! Status:${res.status}`);
            }
            
            //moderation btns block
            articleBlock.querySelector('.mod_block').innerHTML = res.msg;

            //moderation votes block
            articleBlock.querySelector('.votes_block').innerHTML = `
                <p>Votes: <span style="color: green">${res.appr}</span> / <span style="color: red">${res.decl}</span></p>
                <p>Sum: ${res.sum}</p>
            `;
        } catch (err) {
            console.error(err);
        }
    }
});