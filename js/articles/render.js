//js render file

const apiPath = window.location.origin + '/api/'; //base api path

//base status of article render = 'mod' => means moderated and no admin btns render
//'onMod' => for moderation (if article on moderation and user isn't author AND user didn't make moderation for this article => render btns)
function renderArticle(article, status = 'mod') {
    //full view of article
    let full_desc = '';
    if(status === 'onMod' || status === 'modFull' || status === 'selfList') {
        full_desc = ` <b>Full description:</b> ${article.full_desc}</span> `;
    }

    //switch off author block if self-list articles
    let authorBlock = '';
    if(status !== 'selfList') {
        authorBlock = `
                        <small>Article id: ${article.id}</small>
                        <small>Author id: ${article.id_author}</small>
                        <small>Added: ${article.added_at}</small>
                        `;
    }

    //Article "Status" for articles self list
    let aStatus = '';
    if(status === 'selfList') aStatus = `<p>Article status: ${article.article_status}</p>`; 

    //"Read full" btn
    let readFull = '';
    if(status === 'mod') {
        readFull = `<a href='/models/articles/aFull.php?id=${article.id}&comPage=1'>Read full</a>`;
    }

    //moderated btns
    let modBtns = '';
    if(article.isModerated === false && status === 'onMod') {
        modBtns = `
                        <div class="mod_block">
                            <button id="approve">Approve</button> / <button id="decline">Decline</button>
                        </div>
        `;
    } else if(status === 'mod' || status === 'modFull' || status === 'selfList') {
        modBtns = '';
    } else {
        modBtns = 'You can\'t moderate this article';
    } 

    //render votes status(only for moderation)
    let votesBlock = '';
    if(status === 'onMod') {
        votesBlock = `
            <div class="votes_block">
                <p>Votes: <span style="color: green">${article.appr}</span> / <span style="color: red">${article.decl}</span></p>
                <p>Sum: ${article.sum}</p>
            </div>
        `;
    }

    const div = document.createElement('article');
            div.className = 'articles__article';
            div.dataset.type = `${article.id}`;
            div.innerHTML = `
                                <h3><b>Title:</b> ${article.title}</h3>
                                <p><b>Category: </b> ${article.ctgry}</p>
                                <p><b>Short description:</b> ${article.short_desc}</p>
                                ${full_desc}
                                ${aStatus}
                                <div class="article__additional">
                                    <div class="author_block">
                                        ${authorBlock}
                                    </div>
                                    ${readFull}
                                </div>   
                                ${votesBlock}
                                ${modBtns}  
                            `;
            return div;
}

//render article pagination
function renderPagination(filter = 'all', totalPages = 0, currentPage = 1) {
    //get DOM
    const pagBlock = document.getElementById('articles__pagination');

    //clean
    pagBlock.innerHTML = '';

    //render
    for(let i = 1; i <= totalPages; i++) {
        const btnPage = document.createElement('button');
        btnPage.textContent = i;
        if(i === currentPage) btnPage.disabled = true; //switch off number if current
        //call render if pressed
        btnPage.addEventListener('click', () => loadPage(filter, i));

        //add to html
        pagBlock.appendChild(btnPage);
    }
}

//render comments pagination
function renderPaginationCom(idA = 0, totalPages = 1, currentPage = 1) {
    //get DOM
    const pagBlock = document.getElementById('articles__pagination');

    //clean
    pagBlock.innerHTML = '';

    //render
    for(let i = 1; i <= totalPages; i++) {
        const btnPage = document.createElement('button');
        btnPage.textContent = i;
        if(i === currentPage) btnPage.disabled = true; //switch off number if current
        //call render if pressed
        btnPage.addEventListener('click', () => commentsReq(idA, i));

        //add to html
        pagBlock.appendChild(btnPage);
    }
}

//render filter block
function renderFilters(currentFilter = 'all') {
    //get DOM 
    const filterBlock = document.querySelector('.articles__filters');

    filterBlock.innerHTML = ''; //clean dom

    //create select
    const selectBlock = document.createElement('select');
    selectBlock.name = 'filter';

    const filtersArray = [ //btns 
        ['all', 'All'],
        ['canMod', 'Can moderate'],
        ['doneMod', 'Moderated'],
        ['declined', 'Declined']
    ];

    //options render
    filtersArray.forEach(([key, value]) =>  {
        const option = document.createElement('option');
        option.value = key;
        option.textContent = value;
        if(key === currentFilter) option.selected = true;

        //add to select
        selectBlock.appendChild(option);
    });

    //event listener
    selectBlock.addEventListener('change', (e) =>{
        const selectedFilter = e.target.value;
        loadPage(selectedFilter, 1);
    }); 

    //add to html
    filterBlock.appendChild(selectBlock);
}

//comments render
function renderComments(comments) {
    //get DOM for html comments insert
    const comBlock = document.getElementById('comments__block');

    comBlock.innerHTML = ''; //clear block
    
    //render
    if(comments.status === false || !comments || comments.length <= 0) {
        comBlock.innerHTML = 'No have comments';
    } else {
        comments.forEach(comment => {
            //adm block render
            let admBlock;
            if(comment.isAdm) {
                admBlock = `<div class='comment__adm'>
                                <button type='button' id='comment__del'>Del</button>
                            </div>`;
            } else {
                admBlock = '';
            }
            const div = document.createElement('div');
            div.className = 'comments__comment';
            div.dataset.idArticle = `${comment.id_article}`;
            div.dataset.type = `${comment.id}`;
            div.innerHTML = `
                <div class='comment__author'>
                    <p>${comment.id_user}</p>
                    <span>${comment.added_at}</span>
                </div>
                <div class='comment__block'>
                    <p>${comment.msg}</p>
                </div>
                ${admBlock}
            `;

            comBlock.appendChild(div); //add comment to block
        });
    }
}

//render promotion stats
function renderPromotionStats(res) {
    //get DOM 
    const statBlock = document.querySelector('.articles__stats');
    if(!statBlock) return;
    const msgBlock = document.getElementById('articles__status');
    if(!msgBlock) return;

    let statBlockHtml = `
                        <p><b>Approved:</b> ${res.appr}</p>
                        <p><b>Declined:</b> ${res.decl}</p>
                        <p><b>On moderation:</b> ${res.mod}</p>`;
    let isProm = '';
    if(res.isProm) {
        isProm = `<button type='button' id='prom-btn'>Get promotion</button>`;
    }

    statBlock.innerHTML = ''; //clean dom
    msgBlock.innerHTML = ''; //clean dom

    if(res.status === "ok" && res.isProm === true) {
        statBlock.innerHTML = `
                                ${statBlockHtml}
                                ${isProm}
        `;
        msgBlock.innerHTML = `<p style="color:green">${res.msg}</p>`;
    } else {
        statBlock.innerHTML = `
                                ${statBlockHtml}
                                ${isProm}
        `;
        msgBlock.innerHTML = `<p style="color:red">${res.msg}</p>`;
    }
}

//reload user block
async function reloadUserBlock() {
    const userBlock = document.querySelector('.user');
    const html = await fetch('/models/user-block.php', {
        credentials: 'include'
    }).then(r => r.text());

    userBlock.innerHTML = html;
}

//reload avatar changing block
function reloadAvatarBlock(userId, ext) {
    //get DOM
    const userBlock = document.querySelector('.user img');
    const avaChange = document.querySelector('.articles__avatar img');

    //update pictures(bcs of cash)
    userBlock.src = `/img/avatars/${userId}.${ext}?${Date.now()}`;
    avaChange.src = `/img/avatars/${userId}.${ext}?${Date.now()}`;
}
