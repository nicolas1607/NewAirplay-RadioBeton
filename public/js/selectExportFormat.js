const select = document.querySelector('#classement');
const stats = ["stats", "export", "exportCMS", "nbPerDisc"];

stats.forEach(function(stat) {
    const btn = document.querySelector('#'+stat);
    btn.addEventListener('click', () => {
        select.value = stat;
    });
});