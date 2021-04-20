<?php	
require_once('WPOO/WPOO/Post.php');
require_once('WPOO/WPOO/Author.php');

UKMwp_innhold::registerFunctions();
UKMide::addViewData('page', getPage( UKMide::SLUG .'/'. $_GET['PAGE_SLUG'] ));

switch_to_blog( UKM_HOSTNAME == 'ukm.dev' ? 13 : 881 );
	// HENT ALLE POSTS (flere på side 2 enn side 1 riktig her..?)
	$page = isset($_GET['pagination']) ? $_GET['pagination'] : 1;
	$limit = 6;
	$show_posts = [];

	# Hvilken kategori skal vises
	$category = get_category_by_slug( $_GET['PAGE_SLUG'] );
	if( is_object( $category ) ) {
	    $post_query = (isset($_GET['POST_QUERY']) ? $_GET['POST_QUERY'].'&' : '' ). 'post_status=publish&posts_per_page='.$limit.'&paged='.$page.'&cat='. $category->cat_ID;
		$posts = query_posts($post_query);
			
		// CREATE OOP POST OBJECTS
		global $post;
		if( is_array( $posts ) ) {
			foreach( $posts as $key => $post ) {
				the_post();
				$show_posts[] = new WPOO_Post( $post );
			}
		}
	
		// NESTE / FORRIGE-LENKER
		UKMide::addViewData('pagination_current', $page);
		if(sizeof($posts) == $limit)
			UKMide::addViewData('pagination_next', $page+1);
		if($page > 1)
			UKMide::addViewData('pagination_prev', $page-1);
	
		if( isset( $_GET['post'] ) ) {
			// HVIS EN GITT SAK ER VALGT
			$posts = query_posts( 'p='.$_GET['post'] );
			$post = $posts[0];
			the_post();
			UKMide::addViewData('post', new WPOO_Post( $post ));
		}
	}
	UKMide::addViewData('posts', $show_posts);
	
restore_current_blog();