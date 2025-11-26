//js logic of avatar changing

const avaBtnChange = document.getElementById('avatar-select');

if(avaBtnChange) {
    avaBtnChange.addEventListener("submit", async (e) => {
        e.preventDefault(); //stop refresh

        try {
            //get data from form
            const form = e.target;
            const formData = new FormData(form);

            //make request to server
            const req = await fetch(`${apiPath}users/avatar.php`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            if(!req.ok) throw new Error(`Error! Problem: ${req.status}`);

            //if ok 
            const res = await req.json();

            //notification for user and reload user block if all is ok
            const notifBlock = document.getElementById('articles__status');
            notifBlock.innerHTML = ''; //clear

            //inform user
                if(res.status === 'ok'){
                    reloadAvatarBlock(res.userId, res.ext); //reload pic current pic
                    avaBtnChange.reset(); //reset  form

                    notifBlock.innerHTML = `<p style="color:green">${res.msg}</p>`;  
                } else {
                    notifBlock.innerHTML = `<p style="color:red">${res.msg}</p>`;
                }
        } catch(err) {
            console.log(err);
        }
    });
}