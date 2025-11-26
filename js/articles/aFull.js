//Article full view js logic (with comments)

//get url id
const jsURL = new URLSearchParams(window.location.search);
const idA = Number(jsURL.get('id') || 0);
const comPage = Number(jsURL.get('page') || 1);

async function loadPage(id = 0, page = 1) {
    try {
        //get article info and comments
        const req = await fetch(`${apiPath}articles/aFull.php?id=${id}&comPage=${page}`, {
            headers: {
                'Accept': 'application/json'
            }
        });

        //safety
        if(!req.ok) throw new Error(`Error! Problem: ${req.status}`);

        //get answer
        const res = await req.json();

        //safety
        if(res.status === 'error') throw new Error(`Error! Problem: ${res.msg}`);

        //render part
        const articlesBlock = document.getElementById('articles__block');

        if(res.article === false || res.article.length <= 0) {
            articlesBlock.innerHTML = 'No have article';
        } else {
            //add to comments block id of current article(to render properly)
            const comBlock = document.querySelector('.articles__comments');
            comBlock.dataset.type = idA;

            //clean articles block
            articlesBlock.innerHTML = '';

            //rendering
            const div = renderArticle(res.article, 'modFull');

            //add to block
            articlesBlock.appendChild(div);

            //get comments
            commentsReq(id, page);    
        }
    } catch (err) {
        console.log(err);
    }
} 

//first load
loadPage(idA, comPage);
