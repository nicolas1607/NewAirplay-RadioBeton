const divCheck = document.querySelector('.checkBox');
const divChildCheck = divCheck.childNodes;

divChildCheck[1].classList.add('form-check');
divChildCheck[1].classList.add('form-switch');
divChildCheck[3].classList.add('form-check');
divChildCheck[3].classList.add('form-switch');
divChildCheck[5].classList.add('form-check');
divChildCheck[5].classList.add('form-switch');

const checkBox = document.querySelector('.form-check-input');
console.log(checkBox);
checkBox.setAttribute('role', 'switch');

const numero = document.querySelector('#numero');
const addNumero = document.querySelector('#add-numero');
const numerosList = document.querySelector('#list-numeros');
const select = document.querySelector('#select');

const wrongInventoryNumMessage = document.querySelector('#wrong_inventory_number_alert');

const alertMessage = document.querySelector('.alert-message');
if (alertMessage) {
    setTimeout(() => {
        alertMessage.remove();
    }, 1000);
};

const successMessage = document.querySelector('.success-message');
if (successMessage) {
    setTimeout(() => {
        successMessage.remove();
    }, 2000);
};

let validDisc = null;

addNumero.addEventListener('click', (ev) => {
    ev.preventDefault();

    const value = numero.value;
    const regex = new RegExp(/[0-9]/);

    if (regex.test(value) && value !== '0') {
        fetch('/playlist/request_disc/' + value, {
            method: 'get',
            headers: {
                'Content-Type': 'application/json',
            }
        })
            .then(function (request) {
                if (request.ok) {
                    request.text().then((response) => {
                        let disc = JSON.parse(response);
                        createBadge(disc);
                        createSelectOption(disc);
                    });
                } else {
                    wrongInventoryNumMessage.hidden = false;
                    setTimeout(() => {
                        wrongInventoryNumMessage.hidden = true;
                    }, 1000);
                    numero.value = "";
                }
            })
    }
    else {
        wrongInventoryNumMessage.hidden = false;
        setTimeout(() => {
            wrongInventoryNumMessage.hidden = true;
        }, 1000);
        numero.value = "";
    }
});

function createBadge(disc) {
    const index = Object.values(numerosList.children).length;

    const badge = document.createElement('div');
    badge.classList.add("col-md-4");
    badge.classList.add("card");
    badge.classList.add("bg-radio");
    badge.classList.add("m-2");
    badge.setAttribute('data-index', index);

    const badgeValues = document.createElement('div');
    badgeValues.id = "badgeValues";

    const valuesNum = document.createElement('p');
    const valuesAlbum = document.createElement('p');
    const valuesGroup = document.createElement('p');
    valuesNum.classList.add("mb-0");
    valuesNum.classList.add("py-2");
    valuesNum.classList.add("me-3");
    valuesAlbum.classList.add("mb-0");
    valuesAlbum.classList.add("py-2");
    valuesAlbum.classList.add("me-3");
    valuesGroup.classList.add("mb-0");
    valuesGroup.classList.add("py-2");
    valuesGroup.classList.add("me-3");

    valuesNum.textContent = "Num Inventaire : " + disc.inventory_num;
    valuesAlbum.textContent = "Album : " + disc.album;
    valuesGroup.textContent = "Group : " + disc.group;

    const button = document.createElement('button');
    const poubelle = document.createElement('i');
    poubelle.classList.add("fas");
    poubelle.classList.add("fa-trash-alt");

    button.appendChild(poubelle);
    button.setAttribute('type', 'button');
    button.classList.add("btn");
    button.classList.add("mt-0");
    button.classList.add("btn-light");
    button.id = "buttonDeleteLi";
    button.addEventListener('click', () => {
        deleteBadge(badge, numerosList, select, disc.id);
    });

    badgeValues.appendChild(valuesNum);
    badgeValues.appendChild(valuesAlbum);
    badgeValues.appendChild(valuesGroup);
    badgeValues.appendChild(button);

    badge.appendChild(badgeValues);
    numerosList.appendChild(badge);

    numero.value = "";
};

function createSelectOption(disc) {
    const index = Object.values(select.options).length;

    const option = document.createElement('option');
    option.setAttribute('data-index', index);
    option.setAttribute('selected', 'selected');
    option.textContent = disc.id;

    select.appendChild(option);
}

function deleteBadge(badge, list, select, id) {
    let options = Object.values(select.options);
    let badgeIndex = badge.dataset.index;
    options.forEach(option => {
        let optionIndex = option.dataset.index;
        if (Number(option.textContent) === id && Number(badgeIndex) === Number(optionIndex)) {
            select.removeChild(option);
            list.removeChild(badge);
        }
    });
}