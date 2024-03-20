<?php
/*

General Functions
Update: 06/05/2022
Author: Gabriel Caroprese

*/

//Defino campos adicionales
function ik_oracionf_get_campos_adicionales(){
    $campos_adicionales[] = array(
        'name' => 'ubicacion',
        'titulo' => 'Ubicación',
        'placeholder' => '¿Dónde estás?',
        'atributos' => 'required',
        'tipo' => 'text',
        );
    return $campos_adicionales;
}


//Funcion para darle formato al telefono
function ik_oracionf_formato_tel($phoneNumber){
    $phoneFormatting = sanitize_text_field($phoneNumber);
    $phoneFormatting = str_replace('"', '', $phoneFormatting);
    $phoneFormatting = str_replace(',', '', $phoneFormatting);
    $phoneFormatting = str_replace('.', '', $phoneFormatting);
    $phoneFormatting = str_replace('-', '', $phoneFormatting);
    $phoneFormatting = str_replace(')', '', $phoneFormatting);
    $phoneFormatting = str_replace('(', '', $phoneFormatting);
    $phoneFormatting = str_replace('+', '', $phoneFormatting);
    $phoneFormatting = str_replace('_', '', $phoneFormatting);
    $phoneFormatting = str_replace(' ', '', $phoneFormatting);

    $phone = preg_replace('/[^0-9.]+/', '', $phoneFormatting);
    
    //Para formatear para link de tel: o whatsapp
    if (strlen((string)$phone) < 7 && strlen((string)$phone) > 13){
        $phone_formated = '-';
    } else {
        if (strlen((string)$phone) == 7){ //4102721
            $first3Numbers = substr($phone, 0, 3);
            $last4Numbers = substr($phone, 3, 4);
            $phone_formated = $first3Numbers.'-'.$last4Numbers; // 410-2721
        } else if (strlen((string)$phone) == 9){ // 651097965
            $first3Numbers = substr($phone, 0, 3);
            $second3Numbers = substr($phone, 3, 3);
            $last3Numbers = substr($phone, 6, 3);
            $phone_formated = '('.$first3Numbers.') '.$second3Numbers.'-'.$last3Numbers; // (651) 097-965
        } else if (strlen((string)$phone) == 10){ // 3524102721
            if (substr($phone, 0, 2) == '11' || substr($phone, 0, 2) == '15'){
                $first2Numbers = substr($phone, 0, 2);
                $second3Numbers = substr($phone, 2, 4);
                $last4Numbers = substr($phone, 6, 4);
                $phone_formated = $first2Numbers.' '.$second3Numbers.'-'.$last4Numbers; // 11 4089‑5463
            } else {
                $first3Numbers = substr($phone, 0, 3);
                $second3Numbers = substr($phone, 3, 3);
                $last4Numbers = substr($phone, 6, 4);
                $phone_formated = '('.$first3Numbers.') '.$second3Numbers.'-'.$last4Numbers; // (352) 410-2721
            }
        } else if (strlen((string)$phone) == 11){ //3444497965
            $first2Numbers = substr($phone, 0, 2);
            $areCode = substr($phone, 2, 3);
            $threeNumbers = substr($phone, 5, 3);
            $lastDigits = substr($phone, 8, 3);
            $phone_formated = '+'.$first2Numbers.' ('.$areCode.') '.$threeNumbers.'-'.$lastDigits; //+34 444 651 097 965
        } else if (strlen((string)$phone) == 12){ //543764616757
            $first2Numbers = substr($phone, 0, 2);
            $areCode = substr($phone, 2, 3);
            $threeNumbers = substr($phone, 5, 3);
            $lastDigits = substr($phone, 8, 4);
            $phone_formated = '+'.$first2Numbers.' ('.$areCode.') '.$threeNumbers.'-'.$lastDigits; //+54 (376) 461-6757
        } else if (strlen((string)$phone) == 13){
            $first2Numbers = substr($phone, 0, 2);
            $thirdDigit = substr($phone, 2, 1);
            $areaCode = substr($phone, 3, 3);
            $threeNumbers = substr($phone, 6, 3);
            $lastDigits = substr($phone, 9, 4);
            $phone_formated = '+'.$first2Numbers.' '.$thirdDigit.' ('.$areaCode.') '.$threeNumbers.'-'.$lastDigits; //+54 9 (376) 461-6757
        } else {
            $phone_formated = '-'.$phone;
        }
    }
    return $phone_formated;
}

//Devolver recaptcha si se encuentra habilitado
function ik_oracionf_get_recaptcha_form($saberActivo = false){
        
    $recapchacheckUsar = get_option('ik_oracionf_recaptcha_usar');
    
    if ($recapchacheckUsar != false && $recapchacheckUsar != '0' && $recapchacheckUsar != NULL){
        $recapchaHabilitado = true;
    } else {
        $recapchaHabilitado = false;
    }

    
    if ($recapchaHabilitado == true){
    
        $recaptchakey = get_option('ik_oracionf_recaptcha_k');
        $recaptchasecret = get_option('ik_oracionf_recaptcha_s');
        
        if ($recaptchakey == false || $recaptchakey == NULL || $recaptchasecret == false || $recaptchasecret == NULL){
            //No hay claves
            return;
        }        
        
        $recaptcha = "<script src='https://www.google.com/recaptcha/api.js' async defer></script>
        <p>
            <div class='g-recaptcha' data-sitekey='".$recaptchakey."'></div>
        </p>";        
        
        //Si solamente quiero saber si se encuentra activo
        if ($saberActivo == true){
            return $recapchaHabilitado;
        } else {
            return $recaptcha;
        }

        
    } else{
        //recaptcha no habilitado
        return;
    }
}


// Shortcode para mostrar formulario de motivos para orar
function ik_oracionf_form_peticiones_orar($atts = [], $content = null, $tag = '') {
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    $attrib_qform = shortcode_atts([
                                    'logueado' => 'false',
                                    ], $atts, $tag);
                                        
    // turn on output buffering to capture script output
    ob_start();
    
        include(IK_ORACIONF_PLUGIN_DIR.'/templates/formulario.php');
     
    $content = ob_get_clean();
    return $content;
}
add_shortcode('form_peticiones_orar', 'ik_oracionf_form_peticiones_orar');


// Shortcode para mostrar las peticiones recientes para estar orando
function ik_oracionf_mostrar_peticiones_orar($atts = [], $content = null, $tag = '') {
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    $attrib_qform = shortcode_atts([
                                    'logueado' => 'false',
                                    ], $atts, $tag);
                                        
    // turn on output buffering to capture script output
    ob_start();
    
        include(IK_ORACIONF_PLUGIN_DIR.'/templates/peticiones.php');
     
    $content = ob_get_clean();
    return $content;
}
add_shortcode('mostrar_peticiones_orar', 'ik_oracionf_mostrar_peticiones_orar');

?>