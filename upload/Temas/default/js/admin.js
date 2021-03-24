/*
    T!Script > Admin
    Autor: JNeutron
    ::
    Funciones para el panel de administración
*/

/* AFILIADOS */
var ad_afiliado = {
    cache: {},
    detalles: function(aid){
    	$.ajax({
    		type: 'POST',
    		url: global_data.url + '/afiliado-detalles.php',
    		data: 'ref=' + aid,
    		success: function(h){
        		mydialog.show(true);
        		mydialog.title('Detalles del Afiliado');
        		mydialog.body(h);
                mydialog.buttons(true, true, 'Aceptar', 'mydialog.close()', true, true);
                mydialog.center();
                
    		}
    	});   
    }
}