<?php
/*

Template: Contenido del shortcode del formulario
Update: 06/06/2022
Author: Gabriel Caroprese

*/

//Si se hizo un submit del formulario
$result = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if (isset($_POST['nombre']) && isset($_POST['motivo'])){
        
        //Me fijo si recaptcha esta activo
        if (ik_oracionf_get_recaptcha_form(true)){
            if(isset($_POST['g-recaptcha-response'])){
                $captcha = $_POST['g-recaptcha-response'];
            } else {
                $captcha = false;
            }
            
            $secretKey = get_option('ik_oracionf_recaptcha_s');
            $ip = $_SERVER['REMOTE_ADDR'];
            $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
            $response = file_get_contents($url);
            $responseKeys = json_decode($response,true);
            
            if ($responseKeys["success"]){
                $recaptchaBien = true;
            } else{
                $recaptchaBien = false;
            }

        } else {
            $recaptchaBien = true;
        }
        
        if($recaptchaBien == true) {
            
            //0 es pedido 1 es agradecimiento
            $tipoid = 0;
            if (isset($_POST['tipo'])){
                $tipoid = absint($_POST['tipo']);
            }
       
            $nombre = sanitize_text_field($_POST['nombre']);
            $nombre = str_replace('\\', '', $nombre);
            $motivo = sanitize_textarea_field($_POST['motivo']);
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
            
            $campos = maybe_serialize($campos);
    
    
    		global $wpdb;
    		$data_campos  = array (
    		    'status' => 'pendiente',
    		    'tipo' => $tipoid,
    		    'nombre' => $nombre,
    		    'motivo' => $motivo,
    		    'motivo_original' => $motivo,
    		    'campos_adicionales' => $campos,
    		    'enviado' => current_time('mysql'),
    		);
    
    		$tabla = $wpdb->prefix.'ik_oracionf_motivos';
    		$rowResult = $wpdb->insert($tabla,  $data_campos , $format = NULL);
    		
    		$email_moderador = get_option('ik_oracionf_email_moderador');
            if ($email_moderador == false){
                $email_moderador = get_option('admin_email');
            }
    		
    		$to = $email_moderador;
            $subject = html_entity_decode('Nuevo motivo de Oraci&oacute;n pendiente de moderaci&oacute;n');
            $body = html_entity_decode('<p>'.$nombre.' dej&oacute; el siguiente motivo de oraci&oacute;n: <br />'.$motivo .'</p><p>Acceder a '.get_site_url().'/wp-admin/admin.php?page=ik_oracionf_motivos para que sea publicado.</p>');
            $headers = array('Content-Type: text/html; charset=UTF-8');
             
            wp_mail( $to, $subject, $body, $headers );
    		
    		
    		$result = html_entity_decode('Gracias por contactarnos. Tu motivo ser&aacute; revisado y publicado. Estaremos orando por tu vida.');

        } else {
            $result = 'Error al confirmar no ser un robot.';
        }
		
    }
}



?>
<div id="ik_oracionf_formulario">
   <form action="" method="post" enctype="multipart/form-data" autocomplete="no">
        <div class="ik_oracionf_campos">
    		<p>
                <h4>Nombre</h4>
    		    <input type="text" required name="nombre" placeholder="Tu Nombre" /> 
    		</p>
            <?php
            $campos_adicionales = ik_oracionf_get_campos_adicionales();

            foreach($campos_adicionales as $campo_adicional){
                echo '
                <p>
                    <h4>'.$campo_adicional['titulo'].'</h4>
        		    <input type="text" name="'.$campo_adicional['name'].'" placeholder="'.$campo_adicional['placeholder'].'" '.$campo_adicional['atributos'].' />
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
                <h4>Motivo de Oraci&oacute;n</h4>
    		    <textarea required name="motivo" placeholder=""></textarea>
    		</p>
        </div>
        
        <?php echo ik_oracionf_get_recaptcha_form(); ?>
        
        <input type="submit" class="button button-primary" value="Enviar Motivo de Oraci&oacute;n" />
        <p id="ik_dato_guardado"><?php echo $result; ?></p>
    </form>
</div>
<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>