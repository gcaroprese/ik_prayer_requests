<?php
/*

Ajax Functions
Update: 26/08/2021
Author: Gabriel Caroprese

*/

//Ajax para borrar un motivo
add_action( 'wp_ajax_ik_oracionf_ajax_eliminar_motivo_oracion', 'ik_oracionf_ajax_eliminar_motivo_oracion');
function ik_oracionf_ajax_eliminar_motivo_oracion(){
    if(isset($_POST['iddato'])){
        $idMotivo = absint($_POST['iddato']);

        $motivos = new Ik_Motivos_Oracion();
        $result = $motivos->eliminar($idMotivo);
        
        echo json_encode( $result );
    }
    wp_die();         
}

//Ajax para aprobar un motivo
add_action( 'wp_ajax_ik_oracionf_ajax_aprobar_motivo_oracion', 'ik_oracionf_ajax_aprobar_motivo_oracion');
function ik_oracionf_ajax_aprobar_motivo_oracion(){
    if(isset($_POST['iddato']) && isset($_POST['idaccion'])){
            
        $idMotivo = absint($_POST['iddato']);
        $idaccion = intval($_POST['idaccion']);
        
        $motivos = new Ik_Motivos_Oracion();
        $result = $motivos->aprobar($idMotivo, $idaccion);
            
        echo json_encode( $result );
    }
    wp_die();         
}

//Ajax para dar informacion de un motivo para orar
add_action( 'wp_ajax_ik_oracionf_ajax_get_motivo_oracion_a_editar', 'ik_oracionf_ajax_get_motivo_oracion_a_editar');
function ik_oracionf_ajax_get_motivo_oracion_a_editar(){
	if (isset($_POST['iddato'])){
	    $iddato = absint($_POST['iddato']);

        $motivos = new Ik_Motivos_Oracion();
    	$motivo = $motivos->get_motivo($iddato);
    
    	if (isset($motivo[0]->id)){
    	    $datosmotivo['response'] = true;
    	    $datosmotivo['status']= $motivo[0]->status;
    	    //0 es pedido 1 es agradecimiento
    	    $datosmotivo['tipo']= ($motivo[0]->tipo == 0) ? '0' : '1'; 
    	    $datosmotivo['nombre'] = $motivo[0]->nombre;
    	    $datosmotivo['motivo'] = $motivo[0]->motivo;
    	    $datosmotivo['motivo_original'] = $motivo[0]->motivo;

    	    $datosmotivo['campos_adicionales'] = maybe_unserialize($motivo[0]->campos_adicionales);
    	    if (intval(date('Y', strtotime($motivo[0]->enviado))) < 2002){
    	        $datosmotivo['enviado_fecha'] = '-';
    	    } else {
    	        $datosmotivo['enviado_fecha'] = date('d-m-Y h:i:s', strtotime($motivo[0]->enviado));
    	    }

    	    if (intval(date('Y', strtotime($motivo[0]->editado))) < 2002){
    	        $datosmotivo['editado_fecha'] = '-';
    	    } else {
    	        $datosmotivo['editado_fecha'] = date('d-m-Y h:i:s', strtotime($motivo[0]->editado));
    	    }
    	    
    	    if (intval(date('Y', strtotime($motivo[0]->aprobado))) < 2002){
    	        $datosmotivo['aprobado_fecha'] = '-';
    	    } else {
    	        $datosmotivo['aprobado_fecha'] = date('d-m-Y h:i:s', strtotime($motivo[0]->aprobado));
    	    }
    	    
    	    echo json_encode( $datosmotivo);
    	}
	} 
	wp_die();
}

//Ajax para restablecer el motivo original en campo al editar
add_action( 'wp_ajax_ik_oracionf_ajax_restablecer_motivo_oracion', 'ik_oracionf_ajax_restablecer_motivo_oracion');
function ik_oracionf_ajax_restablecer_motivo_oracion(){
	if (isset($_POST['iddato'])){
	    $iddato = absint($_POST['iddato']);

        $motivos = new Ik_Motivos_Oracion();
    	$motivo_original = $motivos->get_motivo_original($iddato);
    
        echo json_encode( $motivo_original);
	} 
	wp_die();      
}

//Ajax para editar un motivo para orar
add_action( 'wp_ajax_ik_oracionf_ajax_editar_motivo_oracion', 'ik_oracionf_ajax_editar_motivo_oracion');
function ik_oracionf_ajax_editar_motivo_oracion(){
    if(isset($_POST['campos_completados']) && isset($_POST['motivo']) && isset($_POST['iddato'])){
        
        $motivo = sanitize_textarea_field($_POST['motivo']);
 
 
        $campos_personalizados = explode("{{{", $_POST['campos_completados']);

/*
            [0] => name:nombre
            [1] => titulo:Nombre
            [2] => placeholder:Nombre
            [3] => tipo:text
            [4] => valor:Gabriel

*/
        //Creo un array de los distintos campos
        foreach ($campos_personalizados as $indice => $campos_personalizado){
            
            $campos_personalizados_array[$indice] = explode(",", $campos_personalizado);
        
        }
        
        //Creo un array de los distintos campos
        foreach ($campos_personalizados_array as $indice => $campos_personalizado_array){
            
            $campos_personalizados_sub1 = explode(":", $campos_personalizado_array[0]);
            
            if (isset($campos_personalizados_sub1[1])){
                $campos_datos_array[$indice][$campos_personalizados_sub1[0]] = $campos_personalizados_sub1[1];
                $campos_personalizados_sub2 = explode(":", $campos_personalizado_array[1]);
                $campos_datos_array[$indice][$campos_personalizados_sub2[0]] = $campos_personalizados_sub2[1];
                $campos_personalizados_sub3 = explode(":", $campos_personalizado_array[2]);
                $campos_datos_array[$indice][$campos_personalizados_sub3[0]] = $campos_personalizados_sub3[1];
                $campos_personalizados_sub4 = explode(":", $campos_personalizado_array[3]);
                $campos_datos_array[$indice][$campos_personalizados_sub4[0]] = $campos_personalizados_sub4[1];
                $campos_personalizados_sub5 = explode(":", $campos_personalizado_array[4]);
                $campos_datos_array[$indice][$campos_personalizados_sub5[0]] = $campos_personalizados_sub5[1];
                
            }
        
        }
        
        foreach ($campos_datos_array as $campo){
            
            if ($campo['name'] == 'nombre'){
                $nombre = sanitize_text_field($campo['valor']);
            } else if ($campo['name'] == 'status'){
                $status = $campo['valor'];
                if ($status != 'aprobado'){
                    $status = 'desaprobado';
                }
            } else {
                if (isset($campo['valor'])){
                    if ($campo['tipo'] == 'tel'){
                        $valor_campo = ik_oracionf_formato_tel($campo['valor']);
                    } else {
                        $valor_campo = sanitize_text_field($campo['valor']);
                    }
                    if ($campo['name'] != ''){
                        $campos_adicionales[$campo['name']] = array(
                            'titulo' => $campo['titulo'],
                            'placeholder' => $campo['placeholder'],
                            'tipo' => $campo['tipo'],
                            'valor' => $valor_campo,
                        );
                    }
                }
            }
        }
        
        //Si existen las variables obligatorias
        if (isset($nombre) && isset($status) && isset($motivo)){
            
            //Si $campos no tiene nada
            if (!isset($campos_adicionales)){
                $campos_adicionales[0] = '';
            }

            //0 es pedido 1 es agradecimiento
            $tipoid = 0;
            if (isset($_POST['tipo'])){
                $tipoid = absint($_POST['tipo']);
            }

    		$datos_modificados  = array (
    		    'status' => $status,
    		    'tipo' => $tipoid,
    		    'nombre' => $nombre,
    		    'motivo' => $motivo,
    		    'campos_adicionales' => $campos_adicionales,
    		);
       
            $idRegistro = absint($_POST['iddato']);
    
            $motivos = new Ik_Motivos_Oracion();
            $result = $motivos->editar($idRegistro, $datos_modificados);
            
            echo json_encode( $result );
        }
    }
    wp_die();         
}

//Ajax para listar una busqueda
add_action( 'wp_ajax_ik_oracionf_ajax_buscar_dato', 'ik_oracionf_ajax_buscar_dato');
function ik_oracionf_ajax_buscar_dato(){
    if(isset($_POST['busqueda'])){
        $busqueda = sanitize_text_field($_POST['busqueda']);
        
        $peticiones = new Ik_Motivos_Oracion();
        
        $resultado = $peticiones->listar_datos_backend('', '', 'enviado', 'DESC', $busqueda);
        
        echo json_encode( $resultado );
    }
    wp_die();         
}

//Ajax para sumar intercesores a un motivo
add_action('wp_ajax_nopriv_ik_oracionf_ajax_agregar_intercesor', 'ik_oracionf_ajax_agregar_intercesor');
add_action( 'wp_ajax_ik_oracionf_ajax_agregar_intercesor', 'ik_oracionf_ajax_agregar_intercesor');
function ik_oracionf_ajax_agregar_intercesor(){
    if(isset($_POST['idMotivo'])){
        $idMotivo = absint($_POST['idMotivo']);
        
        //$operacion 0 es sumar y 1 es restar
        $operacion = (isset($_POST['operacion'])) ? absint($_POST['operacion']) : 0;
        
        $peticiones = new Ik_Motivos_Oracion();
        
        $intercesores_data = $peticiones->sumar_intercesor($idMotivo, $operacion);
        
        echo json_encode( $intercesores_data );
    }
    wp_die();         
}

//Ajax para cargar listado de oraciones
add_action('wp_ajax_nopriv_ik_oracionf_ajax_mostrar_pagina', 'ik_oracionf_ajax_mostrar_pagina');
add_action( 'wp_ajax_ik_oracionf_ajax_mostrar_pagina', 'ik_oracionf_ajax_mostrar_pagina');
function ik_oracionf_ajax_mostrar_pagina(){
    
    //Mensaje por error
    
    $listado = '<p>Error cargando listado de oraciones.</p>';
    
    if(isset($_POST['listado_id']) && isset($_POST['cantidad'])){
        $listado_id = absint($_POST['listado_id']);
        $cantidad = absint($_POST['cantidad']);
        
        if ($listado_id > 0){
            //Para poner un limite y no saturar el server
            if ($cantidad > 300){
                $cantidad = 300;
            } else if ($cantidad < 1){
                $cantidad = 1;
            }
            
            $dias = 30;
            
            if ($listado_id == 1){
               $offset = 0; 
            } else {
                $offset = ($listado_id - 1)*$cantidad;
            }
             
            
            $peticiones = new Ik_Motivos_Oracion();

            
            $peticionesTodo = $peticiones->datos_peticiones($dias);
            $peticiones_data = $peticiones->datos_peticiones($dias, $cantidad, $offset);
              
            //Si hay peticiones las muestro
            if ($peticiones_data != false){
            
                $listado = $peticiones->listar_peticiones($peticiones_data);
                $listado = str_replace(PHP_EOL, null, $listado);
                $listado = str_replace(array("\r\n", "\r", "\n"), '', $listado);

            }
            
        }
    }
    
    echo json_encode( $listado );
    wp_die();         
}
?>
