// Gère le numéro d'inventaire à 0 ou non
const numInventory = document.querySelector('#valueNumInventory input').getAttribute('data-value');

const leaveDate = document.querySelector('#disc_leaveDate');
leaveDate.addEventListener('change', () => {
    console.log(leaveDate);
    if (leaveDate.value == "") {
        document.querySelector('#valueNumInventory input').value = 0;
    } else if (document.querySelector('#valueNumInventory input').value == 0) {
        document.querySelector('#valueNumInventory input').value = numInventory;
    }
});

// Gère le boutton 'Générer numéro d'inventaire'
const generateNumInventory = document.querySelector('#generateNumInventory');
const valueNumInventory = document.querySelector('#valueNumInventory');
if (generateNumInventory) {
    generateNumInventory.addEventListener('click', () => {
        valueNumInventory.style.display = 'block';
        generateNumInventory.remove();
    });
}