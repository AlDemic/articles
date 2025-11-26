//js logic of articles(on moderation) and pagination rendering

//base vars of page
let currentFilter = 'all';
let currentPage = 1;

async function loadPage(filter = 'all', page = 1) {
    try {
        const req = await fetch(`${apiPath}articles/on-moderation.php?filter=${filter}&page=${page}`, {
            credentials: 'include',
            headers: {
                'Accept': 'application/json'
            }
        });

        if(!req.ok) {
            throw new Error(`HTTP Error! Status:${req.status}`);
        }
        
        //get json from php
        const res = await req.json();
        console.error(res);

        if(res.status === 'error') { //if error from php
            throw new Error('Can not load page');
        }

        //render filters
        renderFilters(res.filter);

        //render part
        const articlesBlock = document.getElementById('articles__block');

        //if no have articles in array
        if(res.articles.length <= 0 || res.articles === false) {
            articlesBlock.innerHTML = 'No have articles';
        } else {
            //render articles

            //clean articles block
            articlesBlock.innerHTML = '';

            //rendering
            res.articles.forEach(article => {
                const div = renderArticle(article, 'onMod');

                //add to block
                articlesBlock.appendChild(div);
            });

            //pagination
            renderPagination(res.filter, res.totalPages, res.currentPage);
        }
    } catch(err) {
        console.error(err);
    }

}

//load first page
loadPage(currentFilter, currentPage);
