<?php	
require_once('WPOO/WPOO/Post.php');
require_once('WPOO/WPOO/Author.php');

switch_to_blog( UKM_HOSTNAME == 'ukm.dev' ? 13 : 881 );
	$page = get_page_by_path( 'idebank/'.$PAGE_SLUG );
	$TWIGdata['page'] = new WPOO_Post( $page );
	
	// HENT ALLE POSTS (flere på side 2 enn side 1 riktig her..?)
	$page = isset($_GET['pagination']) ? $_GET['pagination'] : 1;
	$limit = 6;
	
	# Hvilken kategori skal vises
	$category = get_category_by_slug( $PAGE_SLUG );
	if( is_object( $category ) ) {
	    $post_query = (isset($POST_QUERY) ? $POST_QUERY.'&' : '' ). 'post_status=publish&posts_per_page='.$limit.'&paged='.$page.'&cat='. $category->cat_ID;
		$posts = query_posts($post_query);
			
		// CREATE OOP POST OBJECTS
		global $post;
		if( is_array( $posts ) ) {
			foreach( $posts as $key => $post ) {
				the_post();
				$TWIGdata['posts'][] = new WPOO_Post( $post );
			}
		}
	
		// NESTE / FORRIGE-LENKER
		$TWIGdata['pagination_current'] = $page;
		if(sizeof($posts) == $limit)
			$TWIGdata['pagination_next'] = $page+1;
		if($page > 1)
			$TWIGdata['pagination_prev'] = $page-1;
	
		if( isset( $_GET['post'] ) ) {
			// HVIS EN GITT SAK ER VALGT
			$posts = query_posts( 'p='.$_GET['post'] );
			$post = $posts[0];
			the_post();
			$TWIGdata['post'] = new WPOO_Post( $post );
		}
	} else {
		$TWIGdata['posts'] = array();
	}
	
restore_current_blog();