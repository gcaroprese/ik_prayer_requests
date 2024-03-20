/*
Author: Gabriel Caroprese
Update: 06/06/2022
*/
jQuery(document).ready(function ($) {

    //Para evitar quilombos de caracteres
    function htmlDecode(input){
      var e = document.createElement('textarea');
      e.innerHTML = input;
      // handle case of empty input
      return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
    }

    jQuery('#ik_dato_guardado').fadeOut(2600);
    
    jQuery("#ik_oracionf_datos_cargados th .select_all").on( "click", function() {
        if (jQuery(this).attr('seleccionado') != 'si'){
            jQuery('#ik_oracionf_datos_cargados th .select_all').prop('checked', true);
            jQuery('#ik_oracionf_datos_cargados th .select_all').attr('checked', 'checked');
            jQuery('#ik_oracionf_datos_cargados tbody tr').each(function() {
                jQuery(this).find('.select_dato').prop('checked', true);
                jQuery(this).find('.select_dato').attr('checked', 'checked');
            });        
            jQuery(this).attr('seleccionado', 'si');
        } else {
            jQuery('#ik_oracionf_datos_cargados th .select_all').prop('checked', false);
            jQuery('#ik_oracionf_datos_cargados th .select_all').removeAttr('checked');
            jQuery('#ik_oracionf_datos_cargados tbody tr').each(function() {
                jQuery(this).find('.select_dato').prop('checked', false);
                jQuery(this).find('.select_dato').removeAttr('checked');
            });   
            jQuery(this).attr('seleccionado', 'no');
            
        }
    });
    
    jQuery("#ik_oracionf_datos_cargados .ik_oracionf_boton_eliminar_seleccionados").on( "click", function() {
        var confirmar = confirm(htmlDecode('Confirmar eliminar motivos de oraci&oacute;n.'));
        if (confirmar == true) {
            jQuery('#ik_oracionf_datos_cargados tbody tr').each(function() {
            var elemento_borrar = jQuery(this).parent();
                if (jQuery(this).find('.select_dato').prop('checked') == true){
                    
                    var motivo_oracion_tr = jQuery(this);
                    var iddato = motivo_oracion_tr.attr('iddato');
                    
                    var data = {
        				action: "ik_oracionf_ajax_eliminar_motivo_oracion",
        				"post_type": "post",
        				"iddato": iddato,
        			};  
        
            		jQuery.post( ajaxurl, data, function(response) {
            			if (response){
                            motivo_oracion_tr.fadeOut(700);
                            motivo_oracion_tr.remove();
            		    }        
                    });
                }
            });
        }
        jQuery('#ik_oracionf_datos_cargados th .select_all').attr('seleccionado', 'no');
        jQuery('#ik_oracionf_datos_cargados .select_dato').prop('checked', false);
        jQuery('#ik_oracionf_datos_cargados th .select_all').prop('checked', false);
        jQuery('#ik_oracionf_datos_cargados th .select_all').removeAttr('checked');
        return false;
    });
    
    jQuery('#ik_oracionf_datos_cargados').on('click','td .ik_oracionf_boton_eliminar_motivo_oracion', function(e){
        e.preventDefault();
        var confirmar =confirm(htmlDecode('Confirmar eliminar motivo de oraci&oacute;n ya existente.'));
        if (confirmar == true) {
            var iddato = jQuery(this).parent().attr('iddato');
            var motivo_oracion_tr = jQuery('#ik_oracionf_datos_cargados tbody').find('tr[iddato='+iddato+']');
            
            var data = {
    			action: "ik_oracionf_ajax_eliminar_motivo_oracion",
    			"post_type": "post",
    			"iddato": iddato,
    		};  
    
    		jQuery.post( ajaxurl, data, function(response) {
    			if (response){
                    motivo_oracion_tr.fadeOut(700);
                    motivo_oracion_tr.remove();
                    jQuery('#ik_oracionf_edicion_dinamica_dato').remove();
    		    }        
            });
        }
    });
    
    jQuery('#ik_oracionf_datos_cargados').on('click','td .ik_oracionf_boton_aprobar_motivo_oracion', function(e){
        e.preventDefault();
 
        var idAccion = jQuery(this).attr('accion');

        if (idAccion != undefined) {
            var iddato = jQuery(this).parent().attr('iddato');
            var motivo_oracion_tr = jQuery('#ik_oracionf_datos_cargados tbody').find('tr[iddato='+iddato+']');
            var boton = jQuery(this);
            var idaccion = parseInt(idAccion);

            var data = {
    			action: "ik_oracionf_ajax_aprobar_motivo_oracion",
    			"post_type": "post",
    			"iddato": iddato,
    			"idaccion": idaccion,
    		};  
    
    		jQuery.post( ajaxurl, data, function(response) {
    			if (response){
                    var idAccion = boton.attr('accion');
                    if (idAccion == 0){
                        boton.text('Publicar');
                        boton.attr('accion', 1);
                        motivo_oracion_tr.find('.ik_oracionf_estado').text('desaprobado');
                    } else {
                        boton.text('Ocultar');
                        boton.attr('accion', 0);
                        motivo_oracion_tr.find('.ik_oracionf_estado').text('aprobado');             
                    }
    		    }        
            });
        }
    });
    
    jQuery('#ik_oracionf_datos_cargados').on('click','td .ik_oracionf_boton_editar_motivo_oracion', function(e){
        e.preventDefault();
        jQuery(this).prop('disabled', true);

        jQuery('#ik_oracionf_edicion_dinamica_dato').remove();
        var iddato = jQuery(this).parent().attr('iddato');
        var motivo_oracion_tr = jQuery('#ik_oracionf_datos_cargados tbody').find('tr[iddato='+iddato+']');
        
        var data = {
			action: "ik_oracionf_ajax_get_motivo_oracion_a_editar",
			"post_type": "post",
			"iddato": iddato,
		};  

		jQuery.post( ajaxurl, data, function(response) {
			if (response){
			    var data = JSON.parse(response);
			    var campos_ad = '';

                jQuery.each( data.campos_adicionales, function( i, val ) {
                    if (i != undefined && i != '' && i != 'tipo'){
                        campos_ad = campos_ad+'<p><span>'+val.titulo+'</span><br /><input type="'+val.tipo+'" name="'+i+'" titulo="'+val.titulo+'" placeholder="'+val.titulo+'" value="'+val.valor+'" /></p>';
                    }
                });		    
			    
			    
                motivo_oracion_tr.after('<tr id="ik_oracionf_edicion_dinamica_dato" class="ik_oracionf_editor_dato"><td colspan="6"><div><p>Enviado: '+data.enviado_fecha+'</p><p>Editado: '+data.editado_fecha+'</p><p>Aprobado: '+data.aprobado_fecha+'</p><p><span>Nombre</span><br /><input type="text" name="nombre" titulo="Nombre" placeholder="Nombre" cambiado="0" required class="ik_oracionf_editar_nombre" value="'+data.nombre+'"></p><p><p><span>Estado</span><br /><select titulo="Estado" cambiado="0" name="status" class="ik_oracionf_editar_estado"><option value="pendiente">pendiente</option><option value="aprobado">aprobado</option><option value="desaprobado">desaprobado</option></select></p><p><span>Tipo</span><br /><select titulo="tipo" id="tipo_motivo_editar" cambiado="0" name="tipo" class="ik_oracionf_editar_tipo"><option value="0">Pedido</option><option value="1">Agradecimiento</option></select></p>'+campos_ad+'<p><span>Motivo</span><br /><textarea style="height: 100px;" placeholder="Escribir Motivo" titulo="Motivo" required class="ik_oracionf_editar_motivo" id="motivo_edicion" name="motivo">'+data.motivo+'</textarea></p><p><a href="#" class="button button-primary" id="ik_oracionf_boton_guardardatos_motivo_oracion" iddato="'+iddato+'">Guardar Cambios</a><a href="#" class="button button" id="ik_oracionf_boton_restablecer_motivo_oracion" iddato="'+iddato+'" style="margin-left: 5px;">Restablecer</a><a href="#" class="button button" id="ik_oracionf_boton_cancelar_edicion_dinamica" style="margin-left: 5px;">Cancelar</a></p></div></td></tr>');
                	jQuery('#ik_oracionf_edicion_dinamica_dato .ik_oracionf_editar_estado').val(data.status);
	                jQuery('#ik_oracionf_edicion_dinamica_dato .ik_oracionf_editar_estado').trigger('change'); 
	                jQuery('#ik_oracionf_edicion_dinamica_dato .ik_oracionf_editar_tipo').val(data.tipo);
	                jQuery('#ik_oracionf_edicion_dinamica_dato .ik_oracionf_editar_tipo').trigger('change');

                    jQuery('.ik_oracionf_boton_editar_motivo_oracion').prop('disabled', false);
		    }
        });
    });
	
    jQuery('#ik_oracionf_datos_cargados').on('click','#ik_oracionf_boton_cancelar_edicion_dinamica', function(e){
        e.preventDefault();
		jQuery('#ik_oracionf_edicion_dinamica_dato').remove();
	});

    jQuery('#ik_oracionf_datos_cargados').on('click','#ik_oracionf_boton_restablecer_motivo_oracion', function(e){
        e.preventDefault();
        var iddato = jQuery(this).attr('iddato');
        var botonRestablecer = jQuery(this);
        botonRestablecer.addClass('disabled');
        
		var confirmar_restablecer = confirm(htmlDecode('Confirmar restablecer motivo de oraci&oacute;n que fue originalmente enviado por el usuario.'));
		
		if(confirmar_restablecer == true){
            var data = {
    			action: "ik_oracionf_ajax_restablecer_motivo_oracion",
    			"post_type": "post",
    			"iddato": iddato,
    		};  
    
    		jQuery.post( ajaxurl, data, function(response) {
    			if (response){
    			    var motivo = JSON.parse(response);
    
                    jQuery('#motivo_edicion').val(motivo);
                    botonRestablecer.removeClass('disabled');
    		    }      
            });		    
		}
	});
	
    jQuery('#ik_oracionf_datos_cargados').on('click','#ik_oracionf_boton_guardardatos_motivo_oracion', function(e){
        e.preventDefault();
        
        jQuery(this).addClass('disabled');
        
        var iddato = jQuery(this).attr('iddato');
        
        var motivo_oracion_tr = jQuery('#ik_oracionf_datos_cargados tbody').find('tr[iddato='+iddato+']');

        var campos_completados = '';
        jQuery('#ik_oracionf_edicion_dinamica_dato input').each(function() {
            campos_completados = campos_completados+'name:'+jQuery(this).attr("name")+',titulo:'+jQuery(this).attr("titulo")+',placeholder:'+jQuery(this).attr("placeholder")+',tipo:'+jQuery(this).attr("type")+',valor:'+jQuery(this).val()+'{{{';
        });
        
        jQuery('#ik_oracionf_edicion_dinamica_dato select').each(function() {
            if (jQuery(this).attr("name") != 'tipo'){
                campos_completados = campos_completados+'name:'+jQuery(this).attr("name")+',titulo:'+jQuery(this).attr("titulo")+',placeholder:0,tipo:select,valor:'+jQuery(this).val()+'{{{';
            }
        });
        
        jQuery('#ik_oracionf_edicion_dinamica_dato textarea').each(function() {
            if (jQuery(this).attr("name") != 'motivo'){
                campos_completados = campos_completados+'name:'+jQuery(this).attr("name")+',titulo:'+jQuery(this).attr("titulo")+',placeholder:'+jQuery(this).attr("placeholder")+',tipo:textarea,valor:'+jQuery(this).val()+'{{{';;
            }
        });
        
        var nombre = jQuery('#ik_oracionf_edicion_dinamica_dato .ik_oracionf_editar_nombre').val();
        var estado = jQuery('#ik_oracionf_edicion_dinamica_dato .ik_oracionf_editar_estado').val();
        var motivo = jQuery('#motivo_edicion').val();
        var tipo = jQuery('#tipo_motivo_editar').val();
        var tipo_texto = jQuery('#tipo_motivo_editar option[value='+tipo+']').text();
        
        if (estado == 'aprobado'){
	        boton_estado = 'Ocultar';
	        boton_estado_accion = 0;
	    } else {
	        boton_estado = 'Publicar';
	        boton_estado_accion = 1;
	    }
		
        var data = {
			action: "ik_oracionf_ajax_editar_motivo_oracion",
			"post_type": "post",
			"campos_completados": campos_completados,
			"tipo": tipo,
			"motivo": motivo,
			"iddato": iddato,
		};  

		jQuery.post( ajaxurl, data, function(response) {
			if (response){
			    var telEditado = JSON.parse(response);

                motivo_oracion_tr.fadeOut(500);
                motivo_oracion_tr.find('.nombre').text(nombre);
                motivo_oracion_tr.find('.ik_oracionf_estado').text(estado);
                motivo_oracion_tr.find('.ik_oracionf_boton_aprobar_motivo_oracion').text(boton_estado);
                motivo_oracion_tr.find('.ik_oracionf_boton_aprobar_motivo_oracion').attr('accion', boton_estado_accion);
        
                motivo_oracion_tr.find('.ik_oracionf_motivo div').text(motivo);
                
                motivo_oracion_tr.find('.ik_oracionf_tipo').text(tipo_texto);
                jQuery('#ik_oracionf_edicion_dinamica_dato').fadeOut(500);
                jQuery('#ik_oracionf_edicion_dinamica_dato').remove();
                motivo_oracion_tr.fadeIn(500);
		    } else {
			    jQuery('#ik_oracionf_edicion_dinamica_dato').fadeOut(500);
                jQuery('#ik_oracionf_edicion_dinamica_dato').remove();
		    }      
        });
    });
    
    jQuery('#ik_oracionf_motivos_existentes').on('click','#ik_dir_datos_buscar_motivo_oracion', function(e){
        e.preventDefault();
        
        var busqueda = jQuery('#tag-search-input').val();
        if (busqueda != '' && busqueda != undefined){
            var data = {
    			action: "ik_oracionf_ajax_buscar_dato",
    			"post_type": "post",
    			"busqueda": busqueda,
    		};  
    
    		jQuery.post( ajaxurl, data, function(response) {
    			if (response){
    			    var data = JSON.parse(response);
    			    jQuery('#ik_oracionf_motivos_existentes').fadeOut(500);
    			    jQuery('#ik_oracionf_motivos_existentes').empty();
    			    setTimeout(function(){
        			    jQuery('#ik_oracionf_motivos_existentes').append(data);
        			    jQuery('#ik_oracionf_motivos_existentes').fadeIn(500);
    			    }, 600);
    		    }       
            });
        }
    
    });

    jQuery('#ik_oracionf_datos_cargados').on('click','.conorden', function(e){
        e.preventDefault();

        var orden = jQuery(this).attr('orden');
        var urlactual = window.location.href;
        
        if (orden != undefined){
            if (jQuery(this).hasClass('desc')){
                var direc = 'asc';
            } else {
                var direc = 'desc';
            }
            if (orden == 'estado'){
                var ordenar = '&ordenar=estado&ordendir='+direc;
                window.location.href = urlactual+ordenar;
            } else if (orden == 'nombre'){
                var ordenar = '&ordenar=nombre&ordendir='+direc;
                window.location.href = urlactual+ordenar;
            } else if (orden == 'tipo'){
                var ordenar = '&ordenar=tipo&ordendir='+direc;
                window.location.href = urlactual+ordenar;
            } else {
                var ordenar = '&ordenar=fecha&ordendir='+direc;
                window.location.href = urlactual+ordenar;
            }
        }

    });
    
});