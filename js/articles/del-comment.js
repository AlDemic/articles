//delete comment js logic (only if user.rank > 2)

//get DOM element of admin block
const comBlock = document.querySelector('.comments__block');

if(comBlock) {
    comBlock.addEventListener('click', async (e) => {
        const admBtn = e.target.closest('.comment__adm button'); //check if click on btn
        if(!admBtn) return; 

        const comBlock = e.target.closest('.comments__comment');
        if(!comBlock) return;

        const idA = comBlock.dataset.idArticle; //take id article for new render after delete
        const idCom = comBlock.dataset.type; //take id of comment

        if(!idA || !idCom) return;

        try {
            //make php request
            const req = await fetch(`${apiPath}articles/del-comment.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(idCom)
            });

            if(!req.ok) throw new Error(`Error! Problem: ${req.status}`); //safety

            //get json
            const res = await req.json();

            //if ok add system txt
            const sysMsgBlock = document.querySelector('.comments__notif');
            sysMsgBlock.innerHTML = '';

            //render
            commentsReq(idA, 1);

            if(res.status === 'ok') {
                sysMsgBlock.innerHTML = `<span style="color:green">${res.msg}</span>`;              
            } else {
                sysMsgBlock.innerHTML = `<span style="color:red">${res.msg}</span>`;
            }
        } catch(err) {
            console.log(err);
        }
    });
}