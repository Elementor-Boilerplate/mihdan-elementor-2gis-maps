<?php
/**
 * Plugin Name: Mihdan: Elementor 2gis Maps
 * Description: Elementor 2gis Maps Widget - Easily add multiple address pins onto the same map with support for different map types (Road Map/Satellite/Hybrid/Terrain) and custom map style. Freely edit info window content of your pins with the standard Elementor text editor. And many more custom map options.
 * Plugin URI:  https://github.com/mihdan/mihdan-elementor-2gis-maps
 * Version:     1.1.0
 * Author:      Mikhail Kobzarev
 * Author URI:  https://www.kobzarev.com/
 * Text Domain: mihdan-elementor-2gis-maps
 * GitHub Plugin URI: https://github.com/mihdan/mihdan-elementor-2gis-maps
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EB_2GIS_MAPS_FILE', __FILE__ );
define( 'EB_2GIS_MAPS_VERSION', '1.1.0' );

require_once __DIR__ . '/elementor-helper.php';

/**
 * Загрузка плагина
 *
 * Load the plugin after Elementor (and other plugins) are loaded.
 *
 * @since 1.0.0
 */
function mihdan_elementor_2gis_maps() {
	// Load localization file
	load_plugin_textdomain( 'mihdan-elementor-2gis-maps' );

	// Notice if the Elementor is not active
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'mihdan_elementor_2gis_maps_fail_load' );
		return;
	}

	// Check required version
	$elementor_version_required = '1.8.0';
	if ( ! version_compare( ELEMENTOR_VERSION, $elementor_version_required, '>=' ) ) {
		add_action( 'admin_notices', 'mihdan_elementor_2gis_maps_load_out_of_date' );
		return;
	}

	add_action( 'elementor/widgets/widgets_registered', function () {
		require_once __DIR__ . '/widgets/2gis-maps-widget.php';
	} );
}
add_action( 'plugins_loaded', 'mihdan_elementor_2gis_maps' );

/**
 * Уведомление админу, что плагин зависит от Elementor
 */
function mihdan_elementor_2gis_maps_fail_load() {

	$message = '<p>' . __( 'You do not have Elementor Page Builder on your WordPress. Mihdan: Elementor 2gis Maps require Elementor in order to work.', 'mihdan-elementor-2gis-maps' ) . '</p>';

	echo '<div class="error">' . $message . '</div>';
}

/**
 * Уведомление админу, что пора бы обновить Elementor
 */
function mihdan_elementor_2gis_maps_load_out_of_date() {

	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	$file_path = 'elementor/elementor.php';

	$upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );

	// Текст сообщения
	$message  = '<p>' . __( 'Elementor Google Map Extended may not work or is not compatible because you are using an old version of Elementor.', 'mihdan-elementor-2gis-maps' ) . '</p>';
	$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $upgrade_link, __( 'Update Elementor Now', 'mihdan-elementor-2gis-maps' ) ) . '</p>';

	echo '<div class="error">' . $message . '</div>';
}

/**
 * Get the value of a settings field
 *
 * @param string $option settings field name
 * @param string $section the section name this field belongs to
 * @param string $default default text if it's not found
 *
 * @return mixed
 */
function mihdan_elementor_2gis_maps_get_option( $option, $section, $default = '' ) {

	$options = get_option( $section );

	if ( isset( $options[ $option ] ) ) {
		return $options[ $option ];
	}

	return $default;
}

function mihdan_elementor_2gis_maps_editor_before_enqueue_scripts() {
	wp_enqueue_style( 'mihdan-elementor-2gis-maps-admin', plugins_url( '/assets/css/mihdan-elementor-2gis-maps-admin.css', EB_2GIS_MAPS_FILE ) );
	//wp_enqueue_script( 'mihdan-elementor-2gis-maps-api-admin', 'https://maps.api.2gis.ru/2.0/loader.js?pkg=full&source=admin', [ 'jquery', 'elementor-editor' ], EB_2GIS_MAPS_VERSION, true );
	wp_enqueue_script( 'mihdan-elementor-2gis-maps-admin', plugins_url( '/assets/js/mihdan-elementor-2gis-maps-admin.js', EB_2GIS_MAPS_FILE ), [ 'jquery', 'elementor-editor'/*'mihdan-elementor-2gis-maps-api-admin'*/ ], EB_2GIS_MAPS_VERSION, true );
}
add_action( 'elementor/editor/before_enqueue_scripts', 'mihdan_elementor_2gis_maps_editor_before_enqueue_scripts' );

function mihdan_elementor_2gis_maps_frontend_after_enqueue_styles() {
	wp_enqueue_style( 'mihdan-elementor-2gis-maps', plugins_url( '/assets/css/mihdan-elementor-2gis-maps.css', EB_2GIS_MAPS_FILE ) );
}
add_action( 'elementor/frontend/after_enqueue_styles', 'mihdan_elementor_2gis_maps_frontend_after_enqueue_styles' );

function mihdan_elementor_2gis_maps_frontend_after_register_scripts() {
	wp_register_script( 'mihdan-elementor-2gis-maps-api', 'https://maps.api.2gis.ru/2.0/loader.js?pkg=full&lazy__=true&source=frontend', [ 'jquery' ], EB_2GIS_MAPS_VERSION, true );
	//wp_localize_script( 'mihdan-elementor-2gis-maps-api', 'EB_WP_URL', array( 'plugin_url' => plugin_dir_url( __FILE__ ) ) );
	wp_register_script( 'mihdan-elementor-2gis-maps', plugins_url( '/assets/js/mihdan-elementor-2gis-maps.js', EB_2GIS_MAPS_FILE ), [ 'mihdan-elementor-2gis-maps-api' ], EB_2GIS_MAPS_VERSION, true );
}
add_action( 'elementor/frontend/after_register_scripts', 'mihdan_elementor_2gis_maps_frontend_after_register_scripts' );

// eof;
