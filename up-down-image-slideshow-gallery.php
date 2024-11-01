<?php
/*
Plugin Name: Up down image slideshow gallery
Plugin URI: http://www.gopiplus.com/work/2011/04/25/wordpress-plugin-up-down-image-slideshow-script/
Description: Up down image slideshow gallery lets showcase images in a vertical move style. Single image at a time and pull one by one continually. This slideshow will pause on mouse over. The speed of the plugin gallery is customizable. Persistence of last viewed image supported, so when the user reloads the page, the slideshow continues from the last image.
Author: Gopi Ramasamy
Version: 12.1
Author URI: http://www.gopiplus.com/work/
Donate link: http://www.gopiplus.com/work/2011/04/25/wordpress-plugin-up-down-image-slideshow-script/
Tags: slidshow, gallery
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: up-down-image-slideshow-gallery
Domain Path: /languages
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

global $wpdb, $wp_version;
define("WP_udisg_TABLE", $wpdb->prefix . "udisg_plugin");
define('WP_UDISG_FAV', 'http://www.gopiplus.com/work/2011/04/25/wordpress-plugin-up-down-image-slideshow-script/');

if ( ! defined( 'WP_UDISG_BASENAME' ) )
	define( 'WP_UDISG_BASENAME', plugin_basename( __FILE__ ) );
	
if ( ! defined( 'WP_UDISG_PLUGIN_NAME' ) )
	define( 'WP_UDISG_PLUGIN_NAME', trim( dirname( WP_UDISG_BASENAME ), '/' ) );
	
if ( ! defined( 'WP_UDISG_PLUGIN_URL' ) )
	define( 'WP_UDISG_PLUGIN_URL', WP_PLUGIN_URL . '/' . WP_UDISG_PLUGIN_NAME );
	
if ( ! defined( 'WP_UDISG_ADMIN_URL' ) )
	define( 'WP_UDISG_ADMIN_URL', get_option('siteurl') . '/wp-admin/options-general.php?page=up-down-image-slideshow-gallery' );

function udisg() 
{
	global $wpdb;
	$udisg_package = "";
	$udisg_title = get_option('udisg_title');
	$udisg_width = get_option('udisg_width');
	$udisg_height = get_option('udisg_height');
	$udisg_pause = get_option('udisg_pause');
	$udisg_cycles = get_option('udisg_cycles');
	$udisg_persist = get_option('udisg_persist');
	$udisg_slideduration = get_option('udisg_slideduration');
	$udisg_random = get_option('udisg_random');
	$udisg_type = get_option('udisg_type');
	
	if(!is_numeric($udisg_width)) { @$udisg_width = 250 ;}
	if(!is_numeric($udisg_height)) { @$udisg_height = 200; }
	if(!is_numeric($udisg_pause)) { @$udisg_pause = 2000; }
	if(!is_numeric($udisg_cycles)) { @$udisg_cycles = 5; }
	if(!is_numeric($udisg_slideduration)) { @$udisg_slideduration = 300; }
	
	$sSql = "select udisg_path,udisg_link,udisg_target,udisg_title from ".WP_udisg_TABLE." where 1=1";
	
	if($udisg_type <> ""){ 
		$sSql = $sSql . " and udisg_type = %s "; 
		$sSql = $wpdb->prepare($sSql, $udisg_type);
	}
	
	if($udisg_random == "YES"){ $sSql = $sSql . " ORDER BY RAND()"; }else{ $sSql = $sSql . " ORDER BY udisg_order"; }
	
	$data = $wpdb->get_results($sSql);
	
	if ( ! empty($data) ) 
	{
		foreach ( $data as $data ) 
		{
			$udisg_package = $udisg_package .'["'.$data->udisg_path.'", "'.$data->udisg_link.'", "'.$data->udisg_target.'"],';
		}
		$udisg_package = substr($udisg_package,0,(strlen($udisg_package)-1));
		?>
		<script type="text/javascript">
		var udisg_SlideShow=new udisg_Show({
			udisg_Wrapperid: "udisg_widgetss", 
			udisg_WidthHeight: [<?php echo $udisg_width; ?>, <?php echo $udisg_height; ?>], 
			udisg_ImageArray: [ <?php echo $udisg_package; ?> ],
			udisg_Displaymode: {type:'auto', pause:<?php echo $udisg_pause; ?>, cycles:<?php echo $udisg_cycles; ?>, pauseonmouseover:true},
			udisg_Orientation: "v", 
			udisg_Persist: <?php echo $udisg_persist; ?>, 
			udisg_Slideduration: <?php echo $udisg_slideduration; ?> 
		})
		</script>
		<div id="udisg_widgetss" style="max-width:100%"></div>
		<?php
	}	
	else
	{
		_e('Please check the widget setting gallery group', 'up-down-image-slideshow-gallery');
	}
}

function udisg_install() 
{
	global $wpdb;
	if($wpdb->get_var("show tables like '". WP_udisg_TABLE . "'") != WP_udisg_TABLE) 
	{
		$sSql = "CREATE TABLE IF NOT EXISTS `". WP_udisg_TABLE . "` (";
		$sSql = $sSql . "udisg_id INT NOT NULL AUTO_INCREMENT ,";
		$sSql = $sSql . "udisg_path TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,";
		$sSql = $sSql . "udisg_link TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,";
		$sSql = $sSql . "udisg_target VARCHAR( 50 ) NOT NULL ,";
		$sSql = $sSql . "udisg_title VARCHAR( 500 ) NOT NULL ,";
		$sSql = $sSql . "udisg_order INT NOT NULL ,";
		$sSql = $sSql . "udisg_status VARCHAR( 10 ) NOT NULL ,";
		$sSql = $sSql . "udisg_type VARCHAR( 100 ) NOT NULL ,";
		$sSql = $sSql . "udisg_extra1 VARCHAR( 100 ) NOT NULL ,";
		$sSql = $sSql . "udisg_extra2 VARCHAR( 100 ) NOT NULL ,";
		$sSql = $sSql . "udisg_date datetime NOT NULL default '0000-00-00 00:00:00' ,";
		$sSql = $sSql . "PRIMARY KEY ( `udisg_id` )";
		$sSql = $sSql . ") ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		$wpdb->query($sSql);
		$IsSql = "INSERT INTO `". WP_udisg_TABLE . "` (udisg_path, udisg_link, udisg_target, udisg_title, udisg_order, udisg_status, udisg_type, udisg_date)"; 
		$sSql = $IsSql . " VALUES ('".WP_UDISG_PLUGIN_URL."/images/250x167_1.jpg', '#', '_blank', 'Image 1', '1', 'YES', 'Widget', '0000-00-00 00:00:00');";
		$wpdb->query($sSql);
		$sSql = $IsSql . " VALUES ('".WP_UDISG_PLUGIN_URL."/images/250x167_2.jpg' ,'#', '_blank', 'Image 2', '2', 'YES', 'Widget', '0000-00-00 00:00:00');";
		$wpdb->query($sSql);	
		$sSql = $IsSql . " VALUES ('".WP_UDISG_PLUGIN_URL."/images/250x167_3.jpg', '#', '_blank', 'Image 3', '1', 'YES', 'Sample', '0000-00-00 00:00:00');";
		$wpdb->query($sSql);
		$sSql = $IsSql . " VALUES ('".WP_UDISG_PLUGIN_URL."/images/250x167_4.jpg', '#', '_blank', 'Image 4', '2', 'YES', 'Sample', '0000-00-00 00:00:00');";
		$wpdb->query($sSql);
	}
	add_option('udisg_title', "Up down slideshow");
	add_option('udisg_width', "250");
	add_option('udisg_height', "200");
	add_option('udisg_pause', "2000");
	add_option('udisg_cycles', "15");
	add_option('udisg_persist', "true");
	add_option('udisg_slideduration', "300");
	add_option('udisg_random', "YES");
	add_option('udisg_type', "Widget");
}

function udisg_control() 
{
	echo '<p><b>';
	 _e('Up down slideshow', 'up-down-image-slideshow-gallery');
	echo '.</b> ';
	_e('Check official website for more information', 'up-down-image-slideshow-gallery');
	?> <a target="_blank" href="<?php echo WP_UDISG_FAV; ?>"><?php _e('click here', 'up-down-image-slideshow-gallery'); ?></a></p><?php
}

function udisg_widget($args) 
{
	extract($args);
	echo $before_widget . $before_title;
	echo get_option('udisg_Title');
	echo $after_title;
	udisg();
	echo $after_widget;
}

function udisg_admin_options() 
{
	global $wpdb;
	$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
	switch($current_page)
	{
		case 'edit':
			include('pages/image-management-edit.php');
			break;
		case 'add':
			include('pages/image-management-add.php');
			break;
		case 'set':
			include('pages/image-setting.php');
			break;
		default:
			include('pages/image-management-show.php');
			break;
	}
}

add_shortcode( 'up-slideshow', 'udisg_shortcode' );

function udisg_shortcode( $atts ) 
{
	global $wpdb;
	
	//[up-slideshow type="sample" width="250" height="170" pause="3000" random="YES"]
	if ( ! is_array( $atts ) ) { return ''; }
	$udisg_type = $atts['type'];
	$udisg_width = $atts['width'];
	$udisg_height = $atts['height'];
	$udisg_pause = $atts['pause'];
	$udisg_random = $atts['random'];

	$udisg_persist = get_option('udisg_persist');
	
	if($udisg_persist == "true")
	{
		$udisg_persist = "true";
	}
	else
	{
		$udisg_persist = "false";
	}
	
	$udisg_cycles = get_option('udisg_cycles');
	$udisg_slideduration = get_option('udisg_slideduration');
	
	if(!is_numeric($udisg_width)) { @$udisg_width = 250 ;}
	if(!is_numeric($udisg_height)) { @$udisg_height = 200; }
	if(!is_numeric($udisg_cycles)) { @$udisg_cycles = 5; }
	if(!is_numeric($udisg_slideduration)) { @$udisg_slideduration = 300; }
	if(!is_numeric($udisg_pause)) { @$udisg_pause = 2000; }
	
	$sSql = "select udisg_path,udisg_link,udisg_target,udisg_title from ".WP_udisg_TABLE." where 1=1";
	
	if($udisg_type <> ""){ 
		$sSql = $sSql . " and udisg_type = %s "; 
		$sSql = $wpdb->prepare($sSql, $udisg_type);
	}
	
	if($udisg_random == "YES"){ $sSql = $sSql . " ORDER BY RAND()"; }else{ $sSql = $sSql . " ORDER BY udisg_order"; }
	
	$data = $wpdb->get_results($sSql);
	$udisg_package = "";
	$Lr = "";
	if ( ! empty($data) ) 
	{
		foreach ( $data as $data ) 
		{
			$udisg_package = $udisg_package .'["'.$data->udisg_path.'", "'.$data->udisg_link.'", "'.$data->udisg_target.'"],';
		}
		$udisg_package = substr($udisg_package,0,(strlen($udisg_package)-1));
		$type = "auto";
		$wrapperid = $udisg_type;
		$Lr = $Lr .'<script type="text/javascript">';
		$Lr = $Lr .'var udisg_SlideShow=new udisg_Show({udisg_Wrapperid: "'.$wrapperid.'",udisg_WidthHeight: ['.$udisg_width.', '.$udisg_height.'], udisg_ImageArray: [ '.$udisg_package.' ],udisg_Displaymode: {type:"'.$type.'", pause:'.$udisg_pause.', cycles:'.$udisg_cycles.', pauseonmouseover:true},udisg_Orientation: "v",udisg_Persist: '.$udisg_persist.',udisg_Slideduration: '.$udisg_slideduration.' })';
		$Lr = $Lr .'</script>';
		$Lr = $Lr .'<div id="'.$wrapperid.'"></div>';
	}	
	else
	{	
		$Lr = " Please check the short code ";
	}
		
	return $Lr;
}

function udisg_add_to_menu() 
{
	if (is_admin()) 
	{
		add_options_page( __('Up down image slideshow gallery', 'up-down-image-slideshow-gallery'), 
					__('Up down slideshow', 'up-down-image-slideshow-gallery'), 'manage_options', 'up-down-image-slideshow-gallery', 'udisg_admin_options' );
	}
}

function udisg_init()
{
	if(function_exists('wp_register_sidebar_widget')) 
	{
		wp_register_sidebar_widget('up-down-image-slideshow-gallery', __('Up down image slideshow gallery', 'up-down-image-slideshow-gallery'), 'udisg_widget');
	}
	
	if(function_exists('wp_register_widget_control')) 
	{
		wp_register_widget_control('up-down-image-slideshow-gallery', array(__('Up down image slideshow gallery', 'up-down-image-slideshow-gallery'), 'widgets'), 'udisg_control');
	} 
}

function udisg_deactivation() 
{
	// No action required.
}

function udisg_add_javascript_files() 
{
	if (!is_admin())
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'up-down-image-slideshow-gallery', WP_UDISG_PLUGIN_URL.'/inc/up-down-image-slideshow-gallery.js');
	}
}

function udisg_textdomain() 
{
	  load_plugin_textdomain( 'up-down-image-slideshow-gallery', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

function udisg_adminscripts() 
{
	if( !empty( $_GET['page'] ) ) 
	{
		switch ( $_GET['page'] ) 
		{
			case 'up-down-image-slideshow-gallery':
				wp_register_script( 'udisg-adminscripts', plugins_url( 'pages/setting.js', __FILE__ ), '', '', true );
				wp_enqueue_script( 'udisg-adminscripts' );
				$udisg_select_params = array(
					'udisg_path'   	=> __( 'Please enter the image path.', 'udisg-select', 'up-down-image-slideshow-gallery' ),
					'udisg_link'   	=> __( 'Please enter the target link.', 'udisg-select', 'up-down-image-slideshow-gallery' ),
					'udisg_target' 	=> __( 'Please enter the target option.', 'udisg-select', 'up-down-image-slideshow-gallery' ),
					'udisg_order'  	=> __( 'Please enter the display order, only number.', 'udisg-select', 'up-down-image-slideshow-gallery' ),
					'udisg_status' 	=> __( 'Please select the display status.', 'udisg-select', 'up-down-image-slideshow-gallery' ),
					'udisg_type'  	=> __( 'Please enter the gallery type.', 'udisg-select', 'up-down-image-slideshow-gallery' ),
					'udisg_delete'	=> __( 'Do you want to delete this record?', 'udisg-select', 'up-down-image-slideshow-gallery' ),
				);
				wp_localize_script( 'udisg-adminscripts', 'udisg_adminscripts', $udisg_select_params );
				break;
		}
	}
}

add_action('plugins_loaded', 'udisg_textdomain');
add_action('wp_enqueue_scripts', 'udisg_add_javascript_files');
add_action("plugins_loaded", "udisg_init");
register_activation_hook(__FILE__, 'udisg_install');
register_deactivation_hook(__FILE__, 'udisg_deactivation');
add_action('admin_menu', 'udisg_add_to_menu');
add_action( 'admin_enqueue_scripts', 'udisg_adminscripts' );
?>