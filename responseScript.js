jQuery( document ).ready(function( $ ){
  $( document ).on('submit_success', function( event, response ){
      if ( response.data.output ) {
          let data = JSON.parse(response.data.output)
          console.log(data)
          // if(data.status == 200){
          // }else if(data.status == 400){
          //     document.querySelector('#formAccountMessage').innerHTML = 'Os dados estão incorretos ou a conta já existe'
          // }
      }
  });
});