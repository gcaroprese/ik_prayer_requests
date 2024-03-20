<?php
/*

Class Ik_Motivos_Oracion
Update: 06/05/2022
Author: Gabriel Caroprese

*/

if ( ! defined( 'ABSPATH' ) ) {
    return;
}

class Ik_Motivos_Oracion{

    private $db_table_motivos;
    
    public function __construct(){
        global $wpdb;
        $this->db_table_motivos = $wpdb->prefix.'ik_oracionf_motivos';
    }
    
    //method para devolver motivo
    public function get_motivo($idmotivo){
        $idmotivo = absint($idmotivo);
    	global $wpdb;
    	$querymotivoID = "SELECT * FROM ".$this->db_table_motivos." WHERE id = ".$idmotivo;
    	$motivo = $wpdb->get_results($querymotivoID);        
    	
    	return $motivo;
        
    }
    
    //Listar motivos de oracion aprobadas desde una cierta cantidad de $dias
    public function datos_peticiones($dias, $limit = 200, $offset = 0){
        $offset = absint($offset);
        $limit = absint($limit);
    	global $wpdb;
    	$dias = intval($dias);
    	if ($dias > 0){
        	$queryPeticiones = "SELECT * FROM ".$this->db_table_motivos." WHERE status = 'aprobado' AND enviado > now() - INTERVAL ".$dias." day ORDER BY enviado DESC LIMIT ".$limit." OFFSET ".$offset;
        	$peticiones = $wpdb->get_results($queryPeticiones);   
        	if (isset($peticiones[0]->id)){
                return $peticiones;     
        	}
    	}
    	return false;
    }
    
    //Listar motivos de oracion
    public function listar_datos_backend($cantidad = '', $offsetList = '', $orderby = 'enviado', $orderDir = 'DESC', $buscar = NULL){
    	
    	if (is_int($offsetList) && is_int($cantidad)){
    	    $offsetList = ' LIMIT '.$cantidad.' OFFSET '.$offsetList;
    	} else {
    	    $offsetList = '';
    	}
    	
    	//Creo los valores para las clases de los ordenadores
    	$vacio = '';
    	$fechaClass = $vacio;
    	$nombreClass = $vacio;
    	$statusClass = $vacio;
    	$tipoClass = $vacio;
        
    	if ($orderDir != 'DESC'){
    	    $orderDir= ' ASC';
    	    $ordenClass= 'sorted';
    	} else {
    	    $orderDir = ' DESC';
    	    $ordenClass= 'sorted desc';
        }
        
    	if ($orderby != 'enviado'){
    	    if ($orderby == 'status'){
    		    $orderQuery = ' ORDER BY status '.$orderDir;
    		    $statusClass = $ordenClass;
    	    } else if ($orderby == 'tipo'){
    		    $orderQuery = ' ORDER BY tipo '.$orderDir;
    		    $tipoClass = $ordenClass;
    	    } else {
    	        $orderQuery = ' ORDER BY nombre '.$orderDir;
    	        $nombreClass = $ordenClass;
    	    }
    	} else {
    	    $orderQuery = ' ORDER BY enviado '.$orderDir;
    	    $fechaClass = $ordenClass;
        }
        
        if ($buscar != NULL){
            $buscar = sanitize_text_field($buscar);
            $busqueda = $buscar;
            $where = " WHERE nombre LIKE '%".$buscar."%' OR motivo LIKE '%".$buscar."%' OR motivo LIKE '%".$buscar."%'";
        } else {
            $where = '';
            $busqueda = '';
        }
    
        
    	global $wpdb;
    	$queryMotivos = "SELECT * FROM ".$this->db_table_motivos."".$where.$orderQuery.$offsetList;
    	$motivos = $wpdb->get_results($queryMotivos);
    
    	// Si existen campos los listo
    	if (isset($motivos[0]->id)){
    	    $url_motivos = get_site_url().'/wp-admin/admin.php?page=ik_oracionf_motivos';
    		$listado = '
    		<p class="search-box">
    			<label class="screen-reader-text" for="tag-search-input">Buscar Motivos:</label>
    			<input type="search" id="tag-search-input" name="s" value="'.$busqueda.'">
    			<input type="submit" id="ik_dir_datos_buscar_motivo_oracion" class="button" value="Buscar">
    		</p>';
    		if ($buscar != NULL){
    		    $listado .= '<p><a href="'.$url_motivos.'" class="button button-primary">Mostrar Todo</a></p>';
    		}
    
    		$listado .= '<table id="ik_oracionf_datos_cargados">
    			<thead>
    				<tr>
    					<th><input type="checkbox" class="select_all" /></th>
    					<th orden="fecha" class="conorden '.$fechaClass.'">Fecha <span class="sorting-indicator"></span></th>
    					<th orden="estado" class="conorden solodesktop '.$statusClass.'">Estado <span class="sorting-indicator"></span></th>
    					<th orden="tipo" class="conorden solodesktop '.$tipoClass.'">Tipo <span class="sorting-indicator"></span></th>
    					<th orden="nombre" class="conorden solodesktop '.$nombreClass.'">Nombre <span class="sorting-indicator"></span></th>
    					<th>Motivo</th>
    					<th><a href="#" class="ik_oracionf_boton_eliminar_seleccionados button action">Eliminar</a></th>
    				</tr>
    			</thead>
    			<tbody>';
    			foreach ($motivos as $motivo){
    			
    			    if (strlen((string)$motivo->motivo) > 200){
    			        $motivo->motivo = substr($motivo->motivo, 0, 200).'...';
    			    }
    			    if ($motivo->status == 'aprobado'){
    			        $boton_estado = 'Ocultar';
    			        $boton_estado_accion = 0;
    			    } else {
    			        $boton_estado = 'Publicar';
    			        $boton_estado_accion = 1;
    			    }
    			    
    			    if (absint($motivo->tipo) == 0){
    			        $tipo = 'Pedido';
    			    } else {
    			        $tipo = 'Agradecimiento';
    			    }
    			    
        			$listado .= '
        			    <tr iddato="'.$motivo->id.'">
        					<td><input type="checkbox" class="select_dato" /></td>
        					<td class="ik_oracionf_iddato">'.date('d-m-Y h:mA', strtotime($motivo->enviado)).'</td>
        					<td class="ik_oracionf_estado">'.$motivo->status.'</td>
        					<td class="ik_oracionf_tipo">'.$tipo.'</td>
        					<td class="ik_oracionf_nombre">'.$motivo->nombre.'</td>
        					<td class="ik_oracionf_motivo"><div>'.$motivo->motivo.'</div></td>
        					<td iddato="'.$motivo->id.'">
        						<button class="ik_oracionf_boton_aprobar_motivo_oracion button action" accion="'.$boton_estado_accion.'">'.$boton_estado.'</button>
        						<button class="ik_oracionf_boton_editar_motivo_oracion button action">Editar</button>
        						<button class="ik_oracionf_boton_eliminar_motivo_oracion button action">Eliminar</button></td>
        				</tr>';
    				
    	        }
    			$listado .= '
                </tbody>
    		    <tfoot>
    				<tr>
    					<th><input type="checkbox" class="select_all" /></th>
    					<th orden="fecha" class="conorden '.$fechaClass.'">Fecha <span class="sorting-indicator"></span></th>
    					<th orden="estado" class="conorden solodesktop '.$statusClass.'">Estado <span class="sorting-indicator"></span></th>
    					<th orden="tipo" class="conorden solodesktop '.$tipoClass.'">Tipo <span class="sorting-indicator"></span></th>
    					<th orden="nombre" class="conorden solodesktop '.$nombreClass.'">Nombre <span class="sorting-indicator"></span></th>
    					<th>Motivo</th>
    					<th><a href="#" class="ik_oracionf_boton_eliminar_seleccionados button action">Eliminar</a></th>
    				</tr>
    			</tfoot>
    			<tbody>
    		</table>';
    	    
    	    return $listado;
    	    
    	} else {
    	    if ($buscar != NULL){
    	        $listado = '
    		<p class="search-box">
    			<label class="screen-reader-text" for="tag-search-input">Buscar Motivos:</label>
    			<input type="search" id="tag-search-input" name="s" value="'.$busqueda.'">
    			<input type="submit" id="ik_dir_datos_buscar_motivo_oracion" class="button" value="Buscar">
    		</p>
    		<div id="ik_oracionf_datos_cargados">
    		<p>No se encontraron resultados</p>
    		<p><a href="'.$url_motivos.'" class="button button-primary">Mostrar Todo</a></p></div>';
    	        return $listado;
    	    }
    	}
    	
    	return false;
    }
    
    //Cuento la cantidad de motivos para orar
    public function cantidad_datos(){
        
    	global $wpdb;
    	$queryMotivo = "SELECT * FROM ".$this->db_table_motivos."";
    	$motivos = $wpdb->get_results($queryMotivo);
    
    	// Si existen motivos devuelvo el conteo
    	if (isset($motivos[0]->id)){ 
            
            $motivo_conteo = count($motivos);
            return $motivo_conteo;
    	    
    	} else {
        	return false;
    	}
    }

    //devuelvo datos de usuario sobre IDs cliquedos a favor de interceder
    private function interc_ids_user(){
        //chequeo los IDs de intercesiones del usuario por cookie o datos de usuario
        $user_id = get_current_user_id();

        //verifico si el usuario se encuentra logueado
        if ($user_id > 0){
            $user_intercec_ids_data = get_user_meta( $user_id, 'ik_oracionf_interc_ids', true );
        
            //si hay datos lo asigno para verificacion o sino creo un array con un valor en 0
            if (is_array($user_intercec_ids_data)){
                foreach ($user_intercec_ids_data as $user_intercec_id_data){
                    $user_intercec_ids[] = $user_intercec_id_data;
                }
            }
            
        } else {
            
            //Chequeo si hay una cookie
            $cookie_name = $this->get_cookie_name();

            //Veo si existe una cookie guardando datos de motivos sumados
            if (isset($_COOKIE[$cookie_name])) {
                $cookie_motivos_ids = explode( ',', $_COOKIE[$cookie_name] );
                
                if (isset($cookie_motivos_ids[0])){
                    foreach ($cookie_motivos_ids as $cookie_motivos_id){
                        $cookie_motivos_id = absint($cookie_motivos_id);
                        $user_intercec_ids[] = $cookie_motivos_id;
                    }
                }    
            }

        }
        
        if (!isset($user_intercec_ids)){
            //si no hay una cookie existente 
            $user_intercec_ids[] = 0;
        }
        
        return $user_intercec_ids;

    }
    

    //Mostrar listado de peticiones de oracion cargadas
    public function listar_peticiones($peticiones){
        $output_listado = '<div class="ik_oraciondf_item_peticiones_columna">';
        $contadorColumnas = 0;
        
        //Chequeo veces que el usuario o visitante le dio al boton interceder
        $user_intercec_ids = $this->interc_ids_user();
        
        foreach($peticiones as $peticion){
            $campos_adicionales = maybe_unserialize($peticion->campos_adicionales);
            if ($contadorColumnas > 2){
                $contadorColumnas = 0;
                $output_listado .= '</div><div class="ik_oraciondf_item_peticiones_columna">';
                
            }
            
            if (absint($peticion->tipo) !== 0){
                $tipo = 'Agredecimiento';
            } else {
                $tipo = 'Pedido';
            }
            
            $output_listado .= '<div class="ik_oraciondf_item_peticiones"><ul><li><span class="ik_oraciondf_campo">Fecha: </span><span class="ik_oraciondf_dato ik_oraciondf_dato_fecha">'.date_i18n('l, F j, Y', strtotime($peticion->enviado)).'</span></li><li><span class="ik_oraciondf_campo">Nombre: </span><span class="ik_oraciondf_dato">'.$peticion->nombre.'</span></li><li><span class="ik_oraciondf_campo">Tipo: </span><span class="ik_oraciondf_dato">'.$tipo.'</span></li>';
 
            //Listo los campos adicionales
            foreach ($campos_adicionales as $campo_adicional){
                
                /*
                
                a:1:{s:8:"telefono";a:4:{s:6:"titulo";s:15:"Tel&eacute;fono";s:11:"placeholder";s:18:"Tu Tel&eacute;fono";s:4:"tipo";s:3:"tel";s:5:"valor";s:17:"+23 (534) 534-345";}}
                */
                if (isset($campo_adicional['titulo']) && isset($campo_adicional['valor'])){
                    if ($campo_adicional['titulo'] != ''){
                    $output_listado .= '<li><span class="ik_oraciondf_campo">'.$campo_adicional['titulo'].': </span><span class="ik_oraciondf_dato">'.$campo_adicional['valor'].'</span></li>';

                    }
                }
             
            }
            
            //chequeo si el usuario le dio al boton de interceder a este motivo
            $activoClass = (in_array(intval($peticion->id), $user_intercec_ids)) ? ' interc-activo' : '';
                
            $output_listado .= '<li><span class="ik_oraciondf_campo">'.html_entity_decode('Motivo de Oraci&oacute;n:').'</span><p><span class="ik_oraciondf_dato">'.$peticion->motivo.'</span></p></li></ul><div data-id="'.$peticion->id.'" class="ik_oraciondf_motivo_actividad nohighlight"><div class="ik_oraciondf_orando"><span class="ik_oraciondf_counter">'.$peticion->n_intercesores.'</span><a href="#" class="ik_oraciondf_agregar_intercesor'.$activoClass.'"><i class="fa fa-pray"></i></a></div></div></div>';
            $contadorColumnas = $contadorColumnas + 1;
        }
        
        return $output_listado;
    }
    
    private function get_cookie_name(){
                    
        $url_site = get_site_url().'intercediendo';
        $cookie_name = preg_replace("/[^a-zA-Z0-9]+/", "", $url_site);
        
        return $cookie_name;
    }
    
    //method para devolver status
    private function get_status($idMotivo){
        $idmotivo = absint($idMotivo);
    	global $wpdb;
    	$querymotivoID = "SELECT status FROM ".$this->db_table_motivos." WHERE id = ".$idMotivo;
    	$motivo = $wpdb->get_results($querymotivoID);  
    	
    	if (isset($motivo[0]->status)){
    	    $status = $motivo[0]->status;
    	} else {
    	    $status = 'desaprobado';
    	}
    	
    	return $status;
    }

    //method para devolver status
    private function get_count_intercesores($idMotivo){
        $idmotivo = absint($idMotivo);
    	global $wpdb;
    	$querymotivoID = "SELECT n_intercesores FROM ".$this->db_table_motivos." WHERE id = ".$idMotivo;
    	$motivo = $wpdb->get_results($querymotivoID);  
    	
    	if (isset($motivo[0]->n_intercesores)){
    	    $n_intercesores = $motivo[0]->n_intercesores;
    	} else {
    	    $n_intercesores = 0;
    	}
    	
    	return $n_intercesores;
    }

    //method para devolver motivo original no editado
    public function get_motivo_original($idMotivo){
        $idmotivo = absint($idMotivo);
    	global $wpdb;
    	$querymotivoID = "SELECT motivo_original FROM ".$this->db_table_motivos." WHERE id = ".$idMotivo;
    	$motivo = $wpdb->get_results($querymotivoID);  
    	
    	if (isset($motivo[0]->motivo_original)){
    	    $motivo_original = $motivo[0]->motivo_original;
    	} else {
    	    $motivo_original = '';
    	}
    	
    	return $motivo_original;
    }
    
    //method para editar motivo
    public function editar($idMotivo, $args){
        $idMotivo = absint($idMotivo);
        $tiempo = current_time('mysql');
        $args_update['editado'] = $tiempo;
        
        $args_update['status'] = 'desaprobado';
        if (isset($args['status'])){
            if ($args['status'] == 'aprobado'){
                $args_update['status'] = 'aprobado';
                if ($this->get_status($idMotivo) == 'desaprobado'){
                    $args_update['aprobado'] = $tiempo;
                }
            }
        }

        $args_update['tipo'] = 0;
        if (isset($args['tipo'])){
            $args_update['tipo'] = absint($args['tipo']);
        }

        $args_update['nombre'] = (isset($args['nombre'])) ? sanitize_text_field($args['nombre']) : '';
        $args_update['motivo'] = (isset($args['motivo'])) ? sanitize_textarea_field($args['motivo']) : '';
        $args_update['motivo'] = str_replace('\\', '', $args_update['motivo']);
        $args_update['campos_adicionales'] = (isset($args['campos_adicionales'])) ? $args['campos_adicionales'] : $args_update['campos_adicionales'][0] = '';
        $args_update['campos_adicionales'] = maybe_serialize($args_update['campos_adicionales']);

        global $wpdb;
        $where = [ 'id' => $idMotivo ];

        $rowResult = $wpdb->update($this->db_table_motivos, $args_update, $where);
            
    }
    
    //method para aprobar motivo
    public function aprobar($idMotivo, $idaccion){
        $idMotivo = absint($idMotivo);
        $fecha_editado = current_time('mysql');
        
        if ($idaccion == 0){
            $status = 'desaprobado';
            $fecha_aprobado = '0000-00-00 00:00:00';

        } else {
            $status = 'aprobado';
            $fecha_aprobado = $fecha_editado;
        }

        if ($idMotivo != 0){
            
            global $wpdb;
            $where = [ 'id' => $idMotivo ];
                
            $datos_modificados  = array (
                            'status'=> $status,
                            'editado'=> $fecha_editado,
                            'aprobado'=> $fecha_aprobado,
                    );
            $rowResult = $wpdb->update($this->db_table_motivos,  $datos_modificados , $where);
            
            return true;
        } else {
            return false;
        }
    }


    //method para sumar intercesores de un motivo en particular
    public function sumar_intercesor($idMotivo, $operacion = 0){
        //chequeo si el usuario se encuentra logueado
        $user_id = get_current_user_id();
        $idMotivo = absint($idMotivo);
        $n_intercesores = $this->get_count_intercesores($idMotivo);
        $intercesores_data['cookie'] = $this->get_cookie_name();
		
        //0 es sumar y 1 es restar
        if ($operacion == 0){
            $idMotivoOper = $idMotivo;
            $n_intercesores = $n_intercesores + 1;
        } else {
            $n_intercesores = $n_intercesores - 1;
            $idMotivoOper = 0;
        }
        
        //Actualizo el # en la DB
        global $wpdb;
        $where = [ 'id' => $idMotivo ];
        $datos_modificados  = array (
                        'n_intercesores'=> $n_intercesores,
                );
        $rowResult = $wpdb->update($this->db_table_motivos,  $datos_modificados , $where);
        
        

        //Chequeo veces que el usuario o visitante le dio al boton interceder
        $user_intercec_ids = $this->interc_ids_user();
        
        
        
        //chequeo si el usuario se encuentra logueado
        if ($user_id > 0){
                
            /**
            *  Si el $idMotivo_key se mantiene en 0 luego del foreach, eso quiere 
            *   decir que el motivo no fue agregado. 
            *   Si hay que borrarlo se borra, sino se agrega al array.
            */
            
            $idMotivo_key = 0;
            
            foreach ($user_intercec_ids as $key => $user_intercec_id){
                if ($idMotivo === $user_intercec_id){
                    $idMotivo_key = $key;
                }
            }
            
            if ($operacion === 0){
                //Agrego el $idMotivo al array
                if ($idMotivo_key == 0){
                    $user_intercec_ids[] = $idMotivo;
                }
            } else {
               if ($idMotivo_key !== 0){
                   //elimino el $idMotivo del array
                    unset($user_intercec_ids[$idMotivo_key]);
                }                    
            }
            
            // si no queda data elimino el user meta
            if(count($user_intercec_ids) == 0){
                delete_user_meta($user_id, 'ik_oracionf_interc_ids');
            } else {
                update_user_meta( $user_id, 'ik_oracionf_interc_ids', $user_intercec_ids);
                $intercesores_data['motivos_id'] = $user_intercec_ids;
            }
                if ($operacion === 0){
                    $user_intercec_ids[] = $idMotivo;
                    update_user_meta( $user_id, 'ik_oracionf_interc_ids', $user_intercec_ids);
                }
                    
            //Regenero las cookies con los datos de usuario
            $intercesores_data['cookie_delete'] = 'yes';     
            
        } else {
            
            foreach ($user_intercec_ids as $cookie_motivos_id){
                $cookie_motivos_id = absint($cookie_motivos_id);
                $addtocookie = true;
                if ($cookie_motivos_id == $idMotivo){
                    if ($idMotivoOper !== $idMotivo){
                        $addtocookie = false;
                    }
                }
                if ($addtocookie == true){
                    $idMotivoOper = $idMotivoOper.','.$cookie_motivos_id;
                }
            }
            
            $intercesores_data['motivos_id'] = $idMotivoOper;
            
            $intercesores_data['cookie_delete'] = (isset($_COOKIE[$intercesores_data['cookie']])) ? 'yes' : 'no';
        }
            
        //devuelvo la cantidad de intercesores actuales
        $intercesores_data['conteo'] = $n_intercesores;
        
        return $intercesores_data;
    }

    //method para eliminar motivo    
    public function eliminar($idMotivo){
        $idMotivo = absint($idMotivo);
        global $wpdb;
        $rowResult = $wpdb->delete($this->db_table_motivos , array( 'id' => $idMotivo ) );
        
        return true;
    }

}

?>