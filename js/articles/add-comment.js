//add new comment js logic

const addComBlock = document.getElementById('addComment'); //get DOM

if(addComBlock) {
    addComBlock.addEventListener('submit', async (e) => {
        e.preventDefault(); //stop base refresh

        //take form
        const comForm = e.target;
        const formData = new FormData(comForm);

        //make object from form to put article id
        const data = Object.fromEntries(formData.entries());

        //get id of article
        const aBlock = e.target.closest('.articles__comments');
        const idA = aBlock.dataset.type;

        //add id to object
        data.idA = idA;

        //send to php
        try {
            const req = await fetch(`${apiPath}articles/add-comment.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify(data)
            });

            if(!req.ok) throw new Error(`Error! Problem: ${res.status}`);

            //get answer
            const res = await req.json();

            //if ok add system txt
            const sysMsgBlock = document.querySelector('.comments__notif');
            sysMsgBlock.innerHTML = '';

            if(res.status === 'error') {
                commentsReq(data.idA, 1);
                sysMsgBlock.innerHTML = `<span style="color:red">${res.msg}</span>`;
            } else {
                commentsReq(data.idA, 1);
                sysMsgBlock.innerHTML = `<span style="color:green">${res.msg}</span>`;
                addComBlock.reset(); // clear form
            }
        } catch(err) {
            console.log(err);
        }
    });
}