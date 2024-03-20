<?php
/*

Template: Contenido del shortcode para mostrar peticiones
Update: 06/06/2022
Author: Gabriel Caroprese

*/

?>
<style>
#ik_oracionf_peticiones{
    display: grid;
}
#ik_oracionf_peticiones .ik_oraciondf_item_peticiones ul {
    list-style: none;
    padding: 20px 0;
    margin: 0;
}
#ik_oracionf_peticiones .ik_oraciondf_item_peticiones {
    background: #f1f1f1;
    padding: 0 20px;
    display: block;
    max-width: 300px;
    width: auto;	
    float: left;
    margin: 20px 20px 20px 0px;
}
#ik_oracionf_peticiones .ik_oraciondf_motivo_actividad {
    position: relative;
    float: right;
    z-index: 99999;
    min-width: 40px;
    padding-bottom: 10px;
}
#ik_oracionf_peticiones .ik_oraciondf_orando{
    z-index: 99999;
}
#ik_oracionf_peticiones .ik_oraciondf_counter{
    padding: 2px 7px;
    font-size: 13px;
    background: #fff;
    border-radius: 5px;
    margin-right: 4px;
    box-shadow: none;
}
#ik_oracionf_peticiones .ik_oraciondf_item_peticiones .ik_oraciondf_campo{
    font-weight: 700;
}
#ik_oracionf_peticiones .ik_oraciondf_listado_peticiones{
    margin: 0 auto;
}
#ik_oracionf_formulario input.text, #ik_oracionf_formulario input.title, #ik_oracionf_formulario input[type=text], #ik_oracionf_formulario select, #ik_oracionf_formulario textarea, #ik_oracionf_formulario input[type=submit] {
    margin: 0;
    padding: 6px 12px;
    border-radius: 5px;
    min-width: 200px;
}
#ik_oracionf_formulario input[type=submit] {
    background: #faa152;
    border: 0px solid;
    color: #fff;
    cursor: pointer;
    padding: 12px;
}
.nohighlight{
    -webkit-tap-highlight-color: transparent;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
.nohighlight:focus {
    outline: none !important;
}
.ik_motivos_oracion_paginado {
    display: inline-block;
    width: 100%;
    text-align: center;
}
#ik_oracionf_peticiones .ik_oraciondf_agregar_intercesor{
    display: inline-block;
}
#ik_oracionf_peticiones .interc-activo{
    color: #faa152;
}
@keyframes ik_motivos_pulse{25%{transform:scale(1.1)}75%{transform:scale(0.9)}}
.ik_oraciondf_agregar_intercesor:hover {
    animation-name: ik_motivos_pulse;
    animation-duration: 1s;
    animation-timing-function: linear;
    animation-iteration-count: infinite;
    color: #faa152;
}
.ik_motivos_oracion_paginado .ik_listar_paginado_oraciones{
    margin-right: 2px;
    padding: 5px 12px;
    cursor: pointer;
}
#ik_oracionf_peticiones .cargando_peticiones {
    text-align: center;
    max-width: 90%;
    margin: 20px 5%;
}
#ik_oracionf_peticiones .ik_listar_paginado_oraciones.pagina-actual{
    background: #ccc;
}
#ik_oracionf_peticiones .ik_oraciondf_dato_fecha{
	text-transform: capitalize;		
}
@media (min-width: 1150px){
    .ik_oraciondf_item_peticiones {
        width: calc(33.33% - 20px);
        float: left;
    }
	.ik_oraciondf_item_peticiones_columna{
		padding-top: 30px;
		display: table-row;
	}
}
@media (min-width: 767px) and (max-width: 1150px){
    .ik_oraciondf_item_peticiones {
        min-width: 400px;
    }
}
@media (max-width: 500px){
    .ik_oraciondf_item_peticiones{
        width: 100%;
    }
}
</style>
<div id="ik_oracionf_peticiones">
  <?php
  //Listo las peticiones de 30 days para hoy
  $dias = 30;
  $pagina = 1;
  $cantidadLimite = 12;
  
  $peticiones = new Ik_Motivos_Oracion();

  $peticiones_dataTodo = $peticiones->datos_peticiones($dias);
  $peticiones_data = $peticiones->datos_peticiones($dias, $cantidadLimite);
  
  //Si hay peticiones las muestro
  if ($peticiones_data == false){
      ?>
    <div class="ik_oraciondf_listado_peticiones_wrap">
      <div class="ik_oraciondf_listado_peticiones">
      <p><?php echo html_entity_decode('No hay peticiones de oraci&oacute;n para mostrar.'); ?></p>
      </div>
      <?php
  } else {
    ?>
        <div class="ik_oraciondf_listado_peticiones">
          <?php echo $peticiones->listar_peticiones($peticiones_data); ?>
        </div>
    </div>
    <?php
            $peticiones_dataTotal = count($peticiones_dataTodo);
            $peticiones_dataListadas = count($peticiones_data);
            
            if ($peticiones_dataTotal > $peticiones_dataListadas){
                $peticiones_dataRestar = $peticiones_dataTotal / $peticiones_dataListadas;
                $total_paginas = intval($peticiones_dataRestar);
                
                if (is_float($peticiones_dataRestar)){
                    $total_paginas = $total_paginas + 1;
                }
                
                if ($peticiones_dataTotal > $cantidadLimite && $pagina <= $total_paginas){
                    echo '<div class="ik_motivos_oracion_paginado">';
                    
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
                    $paginasHabilitadas[] = $pagina+3;
                    $paginasHabilitadas[] = $pagina+2;
                    $paginasHabilitadas[] = $pagina+1;
                    $paginasHabilitadas[] = $pagina;
                    $paginasHabilitadas[] = $pagina-1;
                    $paginasHabilitadas[] = $pagina-2;
                    
                    for ($i = 1; $i <= $total_paginas; $i++) {
                        
                        $mostrar_pagina = false;
                        
                        //Muestro solo las paginas habilitadas
                        if (in_array($i, $paginasHabilitadas)) {
                            $mostrar_pagina = true;
                        }
                        
                        if ($mostrar_pagina == true){
                            if ($pagina == $i){
                                $PageNActual = 'pagina-actual';
                            } else {
                                $PageNActual = "";
                            }
                            echo '<button href="#" class="ik_listar_paginado_oraciones '.$PageNActual.'" listado_id="'.$i.'" cantidad="'.$cantidadLimite.'">'.$i.'</button>';
                        }
                    }
                    echo '</div>';
                }
            }
        ?>
    </div>
    <?php
  }
  ?>
</div>