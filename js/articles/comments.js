//article comments request js logic

let curPage = 1;
let id = 0;

async function commentsReq(idA = 0, page = 1) {
    try {
        //make php request
        const req = await fetch(`${apiPath}articles/comments.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'include',
            body: JSON.stringify({
                'idA': idA,
                'page': page
            })
        });

        //safety
        if(!req.ok) throw new Error(`Error! Problem: ${req.status}`);

        //if ok get json
        const res = await req.json();

        //call render
        if(res.status === 'error') {
            renderComments(comments === false);
        } else {
            renderComments(res.comments);

            //render pagination of comments
            renderPaginationCom(res.idA, res.totalPages, res.currentPage);

            curPage = res.currentPage;
            id = res.idA;
        }
    } catch(err) {
        console.log(err);
    }
}

//refresh comments every 1 sec
setInterval(() => {
    commentsReq(id, curPage);
}, 3000);
