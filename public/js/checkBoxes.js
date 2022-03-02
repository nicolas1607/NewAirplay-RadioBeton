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