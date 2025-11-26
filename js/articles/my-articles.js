//my articles list js logic

//globals 
let firstPage = 1; //first page
let filter = 1; //1 - all

async function loadPage(filter, page) {
    try {
        //make fetch req to php server
        const req = await fetch(`${apiPath}articles/my-articles.php`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(page)
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

        if(res.articles.length <= 0 || res.articles === false) {
            articlesBlock.innerHTML = 'No have articles';
        } else {
            //clean articles block
            articlesBlock.innerHTML = '';

            //rendering
            res.articles.forEach(article => {
                const div = renderArticle(article, 'selfList');

                //add to block
                articlesBlock.appendChild(div);
            });

            //pagination
            renderPagination(filter, res.totalPages, res.currentPage);
        }
    } catch(err) {
        console.error(err);
    }
}


//first page load
loadPage(filter, firstPage); 