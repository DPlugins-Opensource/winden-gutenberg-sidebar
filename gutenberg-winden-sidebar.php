<?php
/**
 * Plugin Name:       Gutenberg Winden Sidebar
 * Description:       Add Custom Sidebar as Code Editor
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           1.0.0
 * Author:            DPlugins
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gutenberg-winden-sidebar
 *
 * @package           create-block
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function GBWS_create_block_gutenberg_winden_sidebar_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'GBWS_create_block_gutenberg_winden_sidebar_block_init' );

function GBWS_get_winden_classes(){
	$winden_classes = array();
	$upload_dir = wp_upload_dir();
	if (file_exists($upload_dir['basedir'] . '/winden/cache/autocomplete.json')) {
		$arrContextOptions = array(
			"ssl" => array(
				"verify_peer" => false,
				"verify_peer_name" => false,
			),
		);
		$winden_classes = json_decode(file_get_contents($upload_dir['basedir'] . '/winden/cache/autocomplete.json', false, stream_context_create($arrContextOptions)));
		if ($winden_classes == null || empty($winden_classes)) {
			$winden_classes = array();
		}
	}
  
    return $winden_classes;
}

function GBWS_rest_endpoints_register_routes(){
	register_rest_route('GBWS/v1', '/winden_classes', array(
		'methods' => 'GET',
		'callback' => 'GBWS_get_winden_classes',
		'permission_callback' => function ( WP_REST_Request $request ) {
		  return current_user_can('manage_options');
		},
	));
}
add_action( 'rest_api_init', 'GBWS_rest_endpoints_register_routes' );

function GBWS_enqueue_react_app(){ ?>
	<script type="text/javascript">
	  var GWBS_apiBaseUrl = '<?php echo get_rest_url(null, '/GBWS/v1'); ?>';
	  var GWBS_ajaxnonce = '<?php echo wp_create_nonce('wp_rest'); ?>';
	</script><?php
}
add_action( 'admin_head', 'GBWS_enqueue_react_app', 1 );