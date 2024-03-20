<?php
/*

Template: Carga y editar motivos para orar
Update: 06/06/2022
Author: Gabriel Caroprese

*/
if ( ! defined('ABSPATH')) exit('restricted access');


$cantidadListado = 30;

// Cheque datos de Paginado
if (isset($_GET["listado"])){
    // I check if value is integer to avoid errors
    if (strval($_GET["listado"]) == strval(intval($_GET["listado"])) && $_GET["listado"] > 0){
        $paginado = intval($_GET["listado"]);
    } else {
        $paginado = 1;
    }
} else {
     $paginado = 1;
}

// Cheque datos de ordenamiento
if (isset($_GET["ordenar"]) && isset($_GET["ordendir"])){
    $ordenar = sanitize_text_field($_GET["ordenar"]);
    $ordendir = sanitize_text_field($_GET["ordendir"]);

    if ($ordenar == 'estado'){
        $ordenar = 'status';
    } else if ($ordenar == 'nombre'){
        $ordenar = 'nombre';
    } else if ($ordenar == 'tipo'){
        $ordenar = 'tipo';
    } else {
        $ordenar = 'enviado';
    }

    if ($ordendir == 'asc'){
        $ordendir = 'ASC';
    } else {
        $ordendir = 'DESC';
    }


} else {
     $ordenar = 'enviado';
     $ordendir = 'DESC';
}

$url_motivo_oracion = get_site_url().'/wp-admin/admin.php?page=ik_oracionf_motivos';


$offset = ($paginado - 1) * $cantidadListado;

//Si se hizo un submit del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if (isset($_POST['nombre']) && isset($_POST['motivo'])){
        $nombre = sanitize_text_field($_POST['nombre']);
        $nombre = str_replace('\\', '', $nombre);
        $motivo = sanitize_text_field($_POST['motivo']);
        $motivo = str_replace('\\', '', $motivo);

        $campos_adicionales = ik_oracionf_get_campos_adicionales();

        foreach($campos_adicionales as $campo_adicional){
            if (isset($_POST[$campo_adicional['name']])){
                if ($campo_adicional['tipo'] == 'tel'){
                    $dato_campo = ik_oracionf_formato_tel($_POST[$campo_adicional['name']]);
                } else{
                    $dato_campo = sanitize_text_field($_POST[$campo_adicional['name']]);
                    $dato_campo = str_replace('\\', '', $dato_campo);
                }
                $campos[$campo_adicional['name']] = array(
                    'titulo' => $campo_adicional['titulo'],
                    'placeholder' => $campo_adicional['placeholder'],
                    'tipo' => $campo_adicional['tipo'],
                    'valor' => $dato_campo,
                );
            }
        }
        
        //Si $campos no tiene nada
        if (!isset($campos)){
            $campos[0] = '';
        }

        //0 es pedido 1 es agradecimiento
        $tipoid = 0;
        if (isset($_POST['tipo'])){
            $tipoid = absint($_POST['tipo']);
        }
        
        $campos = maybe_serialize($campos);


		global $wpdb;
		$data_campos  = array (
		    'status' => 'aprobado',
		    'tipo' => $tipoid,
		    'nombre' => $nombre,
		    'motivo' => $motivo,
		    'motivo_original' => $motivo,
		    'campos_adicionales' => $campos,
		    'enviado' => current_time('mysql'),
		    'editado' => current_time('mysql'),
		    'aprobado' => current_time('mysql'),
		);

		$tabla = $wpdb->prefix.'ik_oracionf_motivos';
		$rowResult = $wpdb->insert($tabla,  $data_campos , $format = NULL);
		
    }
    $result = 'Guardado';
} else {
    $result = '';
}
?>
<div id="ik_oracionf_agregar_motivos">
    <h1>Motivos de Oraci&oacute;n</h1>
    <form action="" method="post" enctype="multipart/form-data" autocomplete="no">
        <div class="ik_oracionf_campos">
            <h3>Agregar Motivo</h3>
    		<p>
                <h4>Nombre</h4>
    		    <input type="text" required name="nombre" /> 
    		</p>
            <?php
            $campos_adicionales = ik_oracionf_get_campos_adicionales();

            foreach($campos_adicionales as $campo_adicional){
                echo '
                <p>
                    <h4>'.$campo_adicional['titulo'].'</h4>
        		    <input type="text" name="'.$campo_adicional['name'].'" '.$campo_adicional['atributos'].' />
                </p>';
            }
            ?>
            <p>
                <h4>Tipo de Oraci&oacute;n</h4>
                <select name="tipo">
                    <option selected value="0">Pedido</option>
                    <option value="1">Agradecimiento</option>
                </select>
            </p>
    		<p>
                <h4>Motivos de Oraci&oacute;n</h4>
    		    <textarea required name="motivo" placeholder=""></textarea>
    		</p>
        </div>
        <input type="submit" class="button button-primary" value="Agregar Motivo de Oraci&oacute;n" />
        <p id="ik_dato_guardado"><?php echo $result; ?></p>
    </form>
</div>
<div id ="ik_oracionf_motivos_existentes">
<?php
	//Listo los datos ya cargados
	
	$peticiones = new Ik_Motivos_Oracion();
        
	$listado_motivo_oraciones = $peticiones->listar_datos_backend($cantidadListado, $offset, $ordenar, $ordendir);
	if ($listado_motivo_oraciones != false){
	    $listado_motivo_oraciones_todos = $peticiones->cantidad_datos();
            $peticionesRestar = $listado_motivo_oraciones_todos / $cantidadListado;
            $total_paginas = intval($peticionesRestar);
            
            if (is_float($peticionesRestar)){
                $total_paginas = $total_paginas + 1;
            }
		echo $listado_motivo_oraciones;

    	
        if ($listado_motivo_oraciones_todos > $cantidadListado){
            
            if ($paginado <= $total_paginas){
                echo '<div class="ik_oracionf_paginas">';
                
                //Habilito n de paginas a mostrar
                $mitadlistado = intval($total_paginas/2);
                
                $paginasHabilitadas[] = 1;
                $paginasHabilitadas[] = 2;
                $paginasHabilitadas[] = 3;
                $paginasHabilitadas[] = $total_paginas;
                $paginasHabilitadas[] = $total_paginas - 1;
                $paginasHabilitadas[] = $total_paginas - 2;
                $paginasHabilitadas[] = $mitadlistado - 2;
                $paginasHabilitadas[] = $mitadlistado - 1;
                $paginasHabilitadas[] = $mitadlistado;
                $paginasHabilitadas[] = $mitadlistado + 1;
                $paginasHabilitadas[] = $mitadlistado + 2;
                $paginasHabilitadas[] = $paginado+3;
                $paginasHabilitadas[] = $paginado+2;
                $paginasHabilitadas[] = $paginado+1;
                $paginasHabilitadas[] = $paginado;
                $paginasHabilitadas[] = $paginado-1;
                $paginasHabilitadas[] = $paginado-2;
                
                for ($i = 1; $i <= $total_paginas; $i++) {
                    $mostrar_pagina = false;
                    
                    //Muestro solo las paginas habilitadas
                    if (in_array($i, $paginasHabilitadas)) {
                        $mostrar_pagina = true;
                    }
                    
                    if ($mostrar_pagina == true){
                        if ($paginado== $i){
                            $PageNActual = 'actual_pagina';
                        } else {
                            $PageNActual = "";
                        }
                        echo '<a class="ik_listar_paginado_oraciones '.$PageNActual.'" href="'.$url_motivo_oracion.'&listado='.$i.'">'.$i.'</a>';
                    }
                }
                echo '</div>';
            } else {
                        echo 'no';
                    }
        }    	
	
    	
    	
    	
	} else if (isset($_GET['listado'])){
	    echo "<script>
	    window.location.href='".$url_motivo_oracion."';
	    </script>";
	}else {
	    echo '<p class="search-box">
				<label class="screen-reader-text" for="tag-search-input">Buscar Motivos:</label>
				<input type="search" id="tag-search-input" name="s" value="">
				<input type="submit" id="ik_dir_datos_buscar_motivo_oracion" class="button" value="Buscar">
			</p>';
			
	}
?>
</div>