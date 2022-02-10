// Gère le numéro d'inventaire à 0 ou non
const numInventory = document.querySelector('#valueNumInventory input').value;
document.querySelector('#valueNumInventory input').value = 0;

const leaveDate = document.querySelector('#disc_leaveDate');
leaveDate.addEventListener('change', () => {
    document.querySelector('#valueNumInventory input').value = numInventory;
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

if (numInventory != "0") {
    document.querySelector('#valueNumInventory input').value = numInventory;
    generateNumInventory.click();
}