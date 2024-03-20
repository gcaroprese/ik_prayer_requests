<?php
/*

Template: Config del plugin
Update: 11/08/2021
Author: Gabriel Caroprese

*/

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if (isset($_POST['email_moderador']) ){
    
        // Levanto las variables que submití en el form
        
        $email_moderador = sanitize_text_field($_POST['email_moderador']);
        
        if (get_option('ik_oracionf_email_moderador') == NULL){
            
            add_option('ik_oracionf_email_moderador', $email_moderador);
    
        } else {
                        
            update_option('ik_oracionf_email_moderador', $email_moderador);
                
        }    
        $result = 'Guardado';
    }
    if (isset($_POST['recapkey']) && isset($_POST['recapseckey'])){
        $ik_oracionf_recaptcha_k = sanitize_text_field($_POST['recapkey']);
        $ik_oracionf_recaptcha_s = sanitize_text_field($_POST['recapseckey']);
    
        if (!empty($_POST['usar-recaptcha'])){
            $checkbox = "1";
        } else {
            $checkbox = "0";
        }
        delete_option('ik_oracionf_recaptcha_k');
        delete_option('ik_oracionf_recaptcha_s');
        delete_option('ik_oracionf_recaptcha_usar');
        add_option('ik_oracionf_recaptcha_k', $ik_oracionf_recaptcha_k, '', true);
        add_option('ik_oracionf_recaptcha_s', $ik_oracionf_recaptcha_s, '', true);
        add_option('ik_oracionf_recaptcha_usar', $checkbox, '', true);        
        
        $result = 'Guardado';
    }
} else {
    $result = '';
}

$email_moderador = get_option('ik_oracionf_email_moderador');
if ($email_moderador == false){
    $email_moderador = get_option('admin_email');
}

$recaptchakey = get_option('ik_oracionf_recaptcha_k');
$recaptchasecret = get_option('ik_oracionf_recaptcha_s');
$recapchacheckData = get_option('ik_oracionf_recaptcha_usar');

if ($recaptchakey == false || $recaptchakey == NULL){
    $recaptchakey = '';
}
if ($recaptchasecret == false || $recaptchasecret == NULL){
    $recaptchasecret = '';
}
if ($recapchacheckData != false && $recapchacheckData != '0' && $recapchacheckData != NULL){
    $recapchacheck = 'checked';
} else {
    $recapchacheck = '';
}

?>
<div id="ik_oracionf_panel_config">
    <h2>Config - Motivos de Oraci&oacute;n</h2>
    <form action="" method="post" id="db-woomoodle-form" enctype="multipart/form-data" autocomplete="no">
        <p>
            <label>
                <span>Email Moderador</span><br/>
                <input required type="email" name="email_moderador" value="<?php echo $email_moderador; ?>" autocomplete="off" />
            </label>  
        </p>
        <h3>Recaptcha V2</h3>
        <p>Crear claves en <a href="https://www.google.com/recaptcha/admin" target="_blank">Google Recaptcha</a></p>
        <p>
            <label for="recaptcha-key">
                <span>Key Recaptcha</span><br />
                <input type="text" name="recapkey" value="<?php echo $recaptchakey; ?>" />
            </label>
        </p>
        <p>
            <label for="recaptcha-secret-key">
                <span>Clave Recaptcha</span><br />
                <input type="password" readonly="readonly" onfocus="this.removeAttribute('readonly');" name="recapseckey" value="<?php echo $recaptchasecret; ?>" />
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="usar-recaptcha" <?php echo $recapchacheck; ?> value="1">
                <span>Habilitar Recaptcha.</span>
            </label>
        </p>
    	<input type="submit" class="button" value="Guardar">
    	<p id="ik_dato_guardado"><?php echo $result; ?></p>
    </form>
</div>