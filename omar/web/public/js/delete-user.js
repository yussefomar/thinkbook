$(document).ready(function(){
	$('.btn-delete').click(function(e){
           e.preventDefault();/*para evitarla recarga de la pagina al poner el boton eliminar usamos e.prevent#}*/
       var row=$(this).parents('tr');                                                        /* quermoesobtener el padre de donde  para tiene la etiqueta tr */
       var id=row.data('id');/*recuperamos el id de tr*/
       //alert(id);
       var form=$('#form-delete');/*obtener el formulario que acabamos de seleccionar*/
       var url=form.attr('action').replace(':USER_ID',id);/*el comodin userid por el id que hemos recuperado hace unmomneto*/
       var data=form.serialize();/*para elcorrecto envio de nuestro formulario serializamos*/
      
       /*ahroa vamos aenviarlo a nuestro controllador*/

        bootbox.confirm(message,function(res){/*despue que le dio ok a l aventana*/
        	if(res==true){
        		$('#delete-progress').removeClass('hidden');
        		$.post(url,data,function(result){/*hsta aca ya estamos enviamos nuestros datos al controllador y vamos a delte action*/
                      $('#delete-progress').addClass('hidden');/*agregamso la clase hideen*/
        			if(result.removed==1)
        			{
        				row.fadeOut();/* como es uno eliminacmos, a quitar fila de nuestra esturctura de nuestra fista de usuario*/
        				$('#message').removeClass('hidden');/*que vamos a leimianr la clase hiden para que nos aparesca elmensaje*/
        				$('#user-message').text(result.message);/*colocar el texto*/

        				var totalUsers=$('#total').text(); /* antes estaba con pagination,pero ahora lo ponemos en un span y con un id para recuperarlo con jquery*/
        				
        				if($.isNumeric(totalUsers)){
        					$('#total').text(totalUsers-1);/*elimamnos un ususario*/
        				}
        				else{
        					$('#total').text(result.countUser);
        				}
        			}
        			else
        			{
        				$('#message-danger').removeClass('hidden');
        				$('#user-message-danger').text(result.message);
        			}
        		}).fail(function(){
        			alert('ERROR');
                    row.show();
        		});/*si los datos no hacn sido procesados de manera correctaa*/
        	}/*si le dimos ok a nuestra ventana */
        });/*llamo a bootbox antes,res es una variable booleana*/
	});
});