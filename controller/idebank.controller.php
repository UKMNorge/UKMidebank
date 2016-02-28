<?php	
require_once('WPOO/WPOO/Post.php');
require_once('WPOO/WPOO/Author.php');

switch_to_blog( UKM_HOSTNAME == 'ukm.dev' ? 13 : 881 );
	$page = get_page_by_path( $PAGE_SLUG );
	$TWIGdata['post'] = new WPOO_Post( $page );
restore_current_blog();