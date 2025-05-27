//mask for name
document.addEventListener('DOMContentLoaded', function () {
  const nameInput = document.querySelector('#precnet-lead-form #name');
  if(nameInput){
    nameInput.addEventListener('input', function (e) {
      let inputValue = e.target.value
      e.target.value = inputValue.replace(/[0-9]/g, '')
    })
  }
  
})

//mask for phone
document.addEventListener('DOMContentLoaded', function () {
  const telefoneInput = document.querySelector('#precnet-lead-form #phone');
  if(telefoneInput){
      telefoneInput.addEventListener('input', function (e) {
        let input = e.target.value;

        // Remove todos os caracteres não numéricos
        input = input.replace(/\D/g, '');

        // Aplica a formatação apropriada
        if (input.length > 10) {
            input = input.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3'); // Formato (DD) 12345-6789
        } else if (input.length > 6) {
            input = input.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3'); // Formato (DD) 1234-5678
        } else if (input.length > 2) {
            input = input.replace(/^(\d{2})(\d{0,5}).*/, '($1) $2'); // Formato (DD) 12345
        } else if (input.length > 0) {
            input = input.replace(/^(\d*)/, '($1'); // Formato (DD
        }

        e.target.value = input;
    });
    
    telefoneInput.addEventListener('keydown', function (e) {
      // Permitir que Backspace funcione corretamente
      if (e.key === 'Backspace' || e.key === 'Delete') {
        let input = e.target.value;
        input = input.replace(/\D/g, ''); // Remove todos os caracteres não numéricos

        if (input.length <= 2) {
          e.target.value = ''; // Limpa completamente o campo se o usuário apagar tudo
        }
      }
    });
  }
  
});
