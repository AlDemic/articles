//create new article js logic

async function checkPromotion() {
    //send to php
    try {
        const req = await fetch(`${apiPath}users/promotion-stats.php`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Accept': "application/json"
            }
        });

        if(!req.ok) {
            throw new Error(`HTTP Error! Status:${req.status}`);
        }

        //get answer from php
        const res = await req.json();

        //send res to render
        renderPromotionStats(res);
    } catch(err) {
        console.error(err);
    }
}

//page load
checkPromotion();
