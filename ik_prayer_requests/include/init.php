<?php
/*

Funciones Iniciales
Update: 06/06/2022
Author: Gabriel Caroprese

*/

//Script de activacion
function ik_oracionf_activacion(){
    // Creo una tabla en la base de datos para almacenar los datos del directorio
    ik_oracionf_dbcrear();
}

//funcion para crear tablas de DB
function ik_oracionf_dbcrear() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$tabla_motivos = $wpdb->prefix . 'ik_oracionf_motivos';

	$sql = "CREATE TABLE ".$tabla_motivos." (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		status varchar(20) NOT NULL,
		tipo int(1) DEFAULT '0' NOT NULL,
		nombre varchar(255) NOT NULL,
		motivo longtext NOT NULL,
		motivo_original longtext NOT NULL,
		campos_adicionales longtext NOT NULL,
		enviado datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		editado datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		aprobado datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		n_intercesores int(10) DEFAULT '0' NOT NULL,
		UNIQUE KEY id (id)
	) ".$charset_collate.";";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}


//Creo el menu de WP Admin para cobros
function ik_oracionf_admin_menu(){
    add_menu_page('Oraci&oacute;n', 'Oraci&oacute;n', 'manage_options', 'ik_oracionf_motivos', 'ik_oracionf_motivos', IK_ORACIONF_PLUGIN_DIR_PUBLIC . 'img/logo-plugin-oracion.png' );
    add_submenu_page('ik_oracionf_motivos', 'Config', 'Config', 'manage_options', 'ik_oracionf_config', 'ik_oracionf_config', 2 );
}
add_action('admin_menu', 'ik_oracionf_admin_menu');


/*
    Cargo el contenido de cada menu
                                    */
function ik_oracionf_config(){
    include(IK_ORACIONF_PLUGIN_DIR.'/templates/config.php');
}
function ik_oracionf_motivos(){
    include(IK_ORACIONF_PLUGIN_DIR.'/templates/motivos.php');
}


//Carga de lenguajes
function ik_oracionf_textdomain_init() {
    load_plugin_textdomain( 'ik_oracionf', false, IK_ORACIONF_PLUGIN_DIR . '/languages' ); 
}
add_action( 'plugins_loaded', 'ik_oracionf_textdomain_init' );


//Agrego los scripts y styles en WP Admin
function ik_oracionf_add_js_scripts() {
	wp_register_style( 'ik_oracionf_css_style', IK_ORACIONF_PLUGIN_DIR_PUBLIC . 'css/ik_backend_motivosoraciones.css', false, '1.0.7', 'all' );
    wp_register_script( 'ik_oracionf_scripts', IK_ORACIONF_PLUGIN_DIR_PUBLIC . 'js/backend-motivos-oraciones.js', '', '1.0.27', true );
	wp_enqueue_style('ik_oracionf_css_style');
	wp_enqueue_script( 'ik_oracionf_scripts' );
}
add_action( 'admin_enqueue_scripts', 'ik_oracionf_add_js_scripts' );

//Agrego los scripts y styles en frontend
function ik_oracionf_add_js_scripts_frontend() {
	wp_enqueue_style('ik_oracionf_css_fontawesome', IK_ORACIONF_PLUGIN_DIR_PUBLIC . 'css/fontawesome/css/all.css', '0.1.0', 'all');
    wp_enqueue_script('ik_oracionf_peticiones_script', IK_ORACIONF_PLUGIN_DIR_PUBLIC . '/js/peticiones-script.js', array('jquery'), '1.1.14', true );
	wp_localize_script( 'ik_oracionf_peticiones_script', 'ik_oracionf_peticiones_variables', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'plugin_motivos_oracion_url' => IK_ORACIONF_PLUGIN_DIR_PUBLIC));
}
add_action('wp_enqueue_scripts', 'ik_oracionf_add_js_scripts_frontend');

?>