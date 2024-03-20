<?php
/*
Plugin Name: IK Motivos de Oraci&oacute;n
Description: Gestiona motivos de oraci&oacute;n. Formulario: [form_peticiones_orar] | Motivos de oraci&oacute;n: [mostrar_peticiones_orar]
Version: 2.1.5
Author: Gabriel Caroprese
Requires at least: 5.3
Requires PHP: 7.3
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$ik_oracionf_dir = dirname( __FILE__ );
$ik_oracionf_public_dir = plugin_dir_url(__FILE__ );
//Defino el valor por defecto de mostreo de resultados y registros por pueblo
define ('IK_ORACIONF_CANT_LISTADO', 3);
define ('IK_ORACIONF_CANT_POR_PUEBLO', 3);
define( 'IK_ORACIONF_PLUGIN_DIR', $ik_oracionf_dir);
define( 'IK_ORACIONF_PLUGIN_DIR_PUBLIC', $ik_oracionf_public_dir);


//I add plugin functions
require_once($ik_oracionf_dir . '/include/init.php');
require_once($ik_oracionf_dir . '/include/classes/motivos.class.php');
require_once($ik_oracionf_dir . '/include/general_functions.php');
require_once($ik_oracionf_dir . '/include/ajax_functions.php');
register_activation_hook( __FILE__, 'ik_oracionf_activacion' );

?>