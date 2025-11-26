//js logic of promotion button

const statBlock = document.querySelector('.articles__stats');

if(statBlock) {
    statBlock.addEventListener('click', async (e) => {
        const statBtn = e.target.closest('#prom-btn');
        if(!statBtn) return;

        e.preventDefault(); //stop refresh

        try{
            //send to php 
            const req = await fetch(`${apiPath}users/promotion-btn.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json'
                }
            });

            //safety
            if(!req.ok) throw new Error(`Error! Problem: ${req.status}`);

            //if ok get json
            const res = await req.json();
            
            //get DOM for msg block
            const msgBlock = document.getElementById('articles__status');
            if(!msgBlock) return;

            msgBlock.innerHTML = ''; //clean block

            //inform user
            if(res.status === 'ok'){
                //reload user block
                reloadUserBlock();

                msgBlock.innerHTML = `<p style="color:green">${res.msg}</p>`;  
            } else {
                msgBlock.innerHTML = `<p style="color:red">${res.msg}</p>`;
            }

            //remove btn
            document.getElementById('prom-btn').remove();  
        } catch(err) {
            console.log(err);
        }
    });
}
