const numero = document.querySelector('#numero');
const addNumero = document.querySelector('#add-numero');
const numerosList = document.querySelector('#list-numeros');
const select = document.querySelector('#select');

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
                    alert('Attention ! Le n°' + value + ' n\'est pas valide.');
                }
            })
    }
    else {
        alert('Attention ! La valeur saisie n\'est pas valide.');
    }
});

function createBadge(disc) {
    const index = Object.values(numerosList.children).length;

    const badge = document.createElement('li');
    badge.classList.add("badge");
    badge.classList.add("bg-radio");
    badge.classList.add("m-2");
    badge.setAttribute('data-index', index);

    const badgeValues = document.createElement('div');
    badgeValues.classList.add("d-flex");
    badgeValues.id = "badgeValues";

    const values = document.createElement('p');
    values.classList.add("mb-0");
    values.classList.add("me-3");
    values.textContent = disc.inventory_num + ' | ' + disc.album + ' | ' + disc.group;

    const button = document.createElement('button');
    const poubelle = document.createElement('i');
    poubelle.classList.add("fas");
    poubelle.classList.add("fa-trash-alt");

    button.appendChild(poubelle);
    button.setAttribute('type', 'button');
    button.classList.add("btn");
    button.classList.add("btn-sm");
    button.classList.add("btn-light");
    button.id = "buttonDeleteLi";
    button.addEventListener('click', () => {
        deleteBadge(badge, numerosList, select, disc.id);
    });

    badgeValues.appendChild(values);
    badgeValues.appendChild(button);

    badge.appendChild(badgeValues);
    numerosList.appendChild(badge);

    numero.value = "";
};

function createSelectOption(disc) {
    const indexOption = Object.values(select.options).length;

    const option = document.createElement('option');
    option.setAttribute('data-index', indexOption);
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