const numero = document.querySelector('#numero');
const addNumero = document.querySelector('#add-numero');
const numerosList = document.querySelector('#list-numeros');
const select = document.querySelector('#select');

let validDisc = null;

addNumero.addEventListener('click', (ev) => {
    ev.preventDefault();

    const value = numero.value;
    const regex = new RegExp(/[0-9]/);
    
    if(regex.test(value) && value !== '0' )
    {
        fetch('/playlist/test/' + value, {
            method: 'get',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(function(request){
            if(request.ok){
                request.text().then( (response) => {
                    let disc = JSON.parse(response);
                    createBadge(disc);
                    createSelectOption(disc);
                });
            }else{
                alert('Attention ! Le n°' + value + ' n\'est pas valide.');
            }
        })
    }
    else
    {
        alert('Attention ! La valeur saisie n\'est pas valide.');
    }
});

function createBadge(disc){
    const index = Object.values(numerosList.children).length;

    const badge = document.createElement('li');
    badge.setAttribute('data-index', index);
    
    const badgeValues = document.createElement('div');

    const values = document.createElement('div');
    values.textContent = disc.inventory_num + ' | ' + disc.album + ' | ' + disc.group;

    const button = document.createElement('button');
    button.textContent = 'X';
    button.setAttribute('type', 'button');
    button.addEventListener('click', () => {
        deleteBadge(badge, numerosList, select, disc.id);
    });

    badgeValues.appendChild(values);
    badgeValues.appendChild(button);
    
    badge.appendChild(badgeValues);
    numerosList.appendChild(badge);

    numero.value = "";
};

function createSelectOption(disc){
    const index = Object.values(select.options).length;

    const option = document.createElement('option');
    option.setAttribute('data-index', index);
    // option.setAttribute('data-id', disc.id);
    option.setAttribute('selected', 'selected');
    option.textContent = disc.id;

    select.appendChild(option);
}

function deleteBadge(badge, list, select, id){
    let options = Object.values(select.options);
    let badgeIndex = badge.dataset.index;

    options.forEach( (option, index) => {
        if( Number(option.textContent) === id && Number(badgeIndex) === index ){
            // select.removeChild(option);
            // list.removeChild(badge);
            console.log(option.textContent);
            console.log(id);
            console.log(badgeIndex);
            console.log(index);

            select.removeChild(option);
            list.removeChild(badge);

        }
    })

    // options.forEach( (option, index) => {
    //     if( Number(option.textContent) === id )
    // })
    // let badges = Object.values(list.children);

    // badges.forEach( (badge, index) => {
    //     options.forEach( (option, ind) => {
    //         if( Number(option.textContent) === id && index === ind){
    //             // select.removeChild(option[index]);
    //             // list.removeChild(badge);
    //             console.log(option);
    //             console.log(select);
                
    //         }
    //     })
    // });
}