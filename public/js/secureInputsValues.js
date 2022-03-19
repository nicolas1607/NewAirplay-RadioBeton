const inputsToSecure = document.querySelectorAll('.secure');
const notWantedCharacters = new RegExp(/[<|>|/|\?]/);

inputsToSecure.forEach( input => {
    input.addEventListener('change', () => {
        input.value.split('').forEach( (character, index) => {
            if(character.match(notWantedCharacters)){
               input.value = input.value.slice(0, index);
            }
        })
    })
})