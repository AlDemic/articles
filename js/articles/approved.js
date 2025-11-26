//render articles that passed moderation

//globals 
let firstPage = 1; //first page
let urlCtgry = new URLSearchParams(window.location.search);
let ctgry = Number(urlCtgry.get('ctgry') || 0);

async function loadPage(ctgry, page) {
    try {
        //make fetch req to php server
        const req = await fetch(`${apiPath}articles/approved.php?ctgry=${ctgry}&page=${page}`, {
            credentials: 'include',
            headers: {
                'Accept': 'application/json'
            }
        });

        //safety
        if(!req.ok) {
            throw new Error(`Error! Details: ${req.status}`);
        }

        //get answer from php
        const res = await req.json();

        if(res.status === 'error') {
            throw new Error(`Error due to getting answer. Details: ${res.msg}`);
        }

        //render articles
        const articlesBlock = document.getElementById('articles__block');

        if(res.articles.length <= 0 ||res.articles === false) {
            articlesBlock.innerHTML = 'No have articles';
        } else {
            //clean articles block
            articlesBlock.innerHTML = '';

            //rendering
            res.articles.forEach(article => {
                const div = renderArticle(article, 'mod');

                //add to block
                articlesBlock.appendChild(div);
            });

            //pagination
            renderPagination(res.ctgry, res.totalPages, res.currentPage);
        }
    } catch(err) {
        console.error(err);
    }
}

//first page load
loadPage(ctgry, firstPage); 
