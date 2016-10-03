<?php  
/* 
Plugin Name: UKMidebank
Plugin URI: http://www.ukm-norge.no
Description: Idébanken i arrangørsystemet. Henter ut innhold fra UKM.no/arrangorer/idebank-siden
Author: UKM Norge / M Mandal 
Version: 1.0
Author URI: http://www.ukm-norge.no
*/

if(is_admin()) {
	if( get_option('site_type') != false ) {
		add_action('admin_menu', 'UKMide_menu');
	}
}

// Regular menu
function UKMide_menu() {
	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	// !!! DEV MODE !!! DEV MODE !!! DEV MODE !!! DEV MODE !!! DEV MODE !!!
	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	if( !is_super_admin() ) {
		return;
	}
	$page = add_menu_page('Idébank', 'Idébank', 'editor', 'idebank', 'UKMide', 'http://ico.ukm.no/news-16.png',5);
	add_action( 'admin_print_styles-' . $page, 'UKMide_scripts_and_styles' );	
	
	// LIST UT ALLE IDÉBANKER
	global $ID_ARRANGOR;
	
	# Bytt til arrangor
	switch_to_blog( UKM_HOSTNAME == 'ukm.dev' ? 13 : 881 );
	
	# Hent alle sider
	$parent_page = get_page_by_path( 'idebank' );
	# Hent alle sider
	$my_wp_query = new WP_Query();
	$children_pages = $my_wp_query->query( array('post_parent' => $parent_page->ID, 'post_type'=>'page', 'posts_per_page' => 100, 'orderby' => 'menu_order', 'order' => 'ASC') );

	# Legg til menyelementer og enqueue scripts + styles
	foreach( $children_pages as $child ) {
		$subpage = add_submenu_page('idebank', $child->post_title, $child->post_title, 'editor', 'UKMide_'.$child->post_name, 'UKMide');
		add_action( 'admin_print_styles-' . $subpage, 'UKMide_scripts_and_styles' );	
	}
	
	# Restore til aktiv side
	restore_current_blog();
}

function UKMide_scripts_and_styles(){
	wp_enqueue_script('WPbootstrap3_js');
	wp_enqueue_style('WPbootstrap3_css');
	wp_enqueue_style( 'UKMide_css', plugin_dir_url( __FILE__ ) .'ukmidebank.css');
}

function UKMide() {
	$TWIGdata = array();
	
	$PAGE_SLUG = str_replace('UKMide_', '', $_GET['page']);
	switch( $PAGE_SLUG ) {
		case 'idebank':
			require_once('controller/idebank.controller.php');
			$VIEW = 'idebank';
			break;
		default:
			if( isset( $_GET['subpage'] ) ) {
				$PAGE_SLUG = $PAGE_SLUG .'/'. $_GET['subpage'];
			}
			require_once('controller/page.controller.php');
			$VIEW = 'page';
			break;
	}
	
	$TWIGdata['current_page'] = $_GET['page'];

	echo TWIG($VIEW. '.twig.html', $TWIGdata, dirname(__FILE__));
}
