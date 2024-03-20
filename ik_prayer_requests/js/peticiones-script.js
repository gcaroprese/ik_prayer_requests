/*
Author: Gabriel Caroprese
Update: 06/06/2022
*/
jQuery(document).ready(function ($) {

    function setCookie(name,value,days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }
    
    jQuery('#ik_oracionf_peticiones').on('click', '.ik_listar_paginado_oraciones', function(){
        var pag_seleccionada = jQuery(this);
        var listado_id = pag_seleccionada.attr('listado_id');  
        var cantidad = pag_seleccionada.attr('cantidad');  
        var wrap_oraciones = jQuery('#ik_oracionf_peticiones .ik_oraciondf_listado_peticiones');
        
        wrap_oraciones.empty();
        jQuery('#ik_oracionf_peticiones')[0].scrollIntoView();
        wrap_oraciones.append('<img src="'+ik_oracionf_peticiones_variables.plugin_motivos_oracion_url+'/img/cargando.gif" alt="cargando peticiones" class="cargando_peticiones" />');
        
       	var data = {
    		action: "ik_oracionf_ajax_mostrar_pagina",
    		"post_type": "post",
    		"listado_id": listado_id,
    		"cantidad": cantidad,
    	};  
    
    	jQuery.post( ik_oracionf_peticiones_variables.ajaxurl, data, function(response) {
    		if (response){
    		    var listado = JSON.parse(response);
                wrap_oraciones.empty();
                wrap_oraciones.append(listado);
                jQuery('#ik_oracionf_peticiones')[0].scrollIntoView();
                jQuery('#ik_oracionf_peticiones .pagina-actual').removeClass('pagina-actual');
                jQuery(pag_seleccionada).addClass('pagina-actual');
    	    }        
        });
    });

    jQuery('#ik_oracionf_peticiones').on('click', '.ik_oraciondf_agregar_intercesor', function(e){
        e.preventDefault();
        var idMotivo = parseInt(jQuery(this).parent().parent().attr('data-id'));
        var boton_orar = jQuery(this);
        
        var operacion = 0;
        if (boton_orar.hasClass('interc-activo')){
            operacion = 1;
        }
        
       	var data = {
    		action: "ik_oracionf_ajax_agregar_intercesor",
    		"post_type": "post",
    		"idMotivo": idMotivo,
    		"operacion": operacion,
    	};  
    
    	jQuery.post( ik_oracionf_peticiones_variables.ajaxurl, data, function(response) {
    		if (response){
    		    var interc_data = JSON.parse(response);
                
                if (interc_data.cookie_delete == 'yes'){
            	   document.cookie = interc_data.cookie +'=; Path=/;Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
                }
                
    		    if (operacion === 0){
    		        boton_orar.fadeOut(250);
    		        boton_orar.addClass('interc-activo');
    		        boton_orar.fadeIn(250);
    		    } else {
    		        boton_orar.removeClass('interc-activo');
    		    }
    		   
    		    setCookie(interc_data.cookie, interc_data.motivos_id, 90);
    		    boton_orar.parent().find('.ik_oraciondf_counter').text(interc_data.conteo);
    	    }
        });
    });
});
