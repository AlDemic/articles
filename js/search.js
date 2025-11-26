// js logic of searching

//globals
//get "search" str
const windowUrl = new URLSearchParams(window.location.search);
const searchStr = windowUrl.get('search') ?? '';
const firstPage = 1;

async function loadPage(filter, page) {
    if(filter === '') return; //if nothing in search - return

    try {
            const req = await fetch(`${apiPath}search.php?filter=${filter}&page=${page}`, {
                headers: {
                    'Accept': "application/json"
                }
            });

            if(!req.ok) {
                throw new Error(`HTTP Error! Status:${req.status}`);
            }

            //get answer from php
            const res = await req.json();

            //render articles
            const articlesBlock = document.getElementById('articles__block');

            if(res.articles.length <= 0 || res.articles === false) {
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
                renderPagination(res.filter, res.totalPages, res.currentPage);
            }
        } catch(err) {
            console.error(err);
        }
}

//first load
loadPage(searchStr, firstPage);