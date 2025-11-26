//js logic for main menu block as categories select

try {
    //get DOM of menu
    const menuBlock = document.querySelector('.categories__list');

    menuBlock.addEventListener('click', (e) => {
        e.preventDefault(); //stop base refresh

        const li = e.target.closest('li');

        const category = parseInt(li.id);
        
        //go to main page for render
        window.location.href = `/index.php?ctgry=${category}`;
    });
} catch(err) {
    console.error(err);
}