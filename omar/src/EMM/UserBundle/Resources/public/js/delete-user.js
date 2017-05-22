$(document).ready(function(){
	$('.btn-delete').click(function(e){
           e.preventDefault() ;                                 /*para evitarla recarga de la pagina al poner el boton eliminar usamos e.prevent#}*/
       var row= $(this).parents('tr');                                                        /* quermoesobtener el padre de donde  para tiene la etiqueta tr */
       var id=row.date('id');
       alert('hoola');
	});
});
