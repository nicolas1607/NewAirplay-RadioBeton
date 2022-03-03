const deleteLinks = document.querySelectorAll('.delete');

deleteLinks.forEach(link => {
    link.addEventListener('click', (ev) => {
        !confirm('On supprime ? C\'est sûr ? Sûr sûr ??') && ev.preventDefault();
    })
});