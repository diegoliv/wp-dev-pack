<?php 

/**
 * prints a navigation menu
 * @param  string $location the location of the menu.
 * @param  string $classes  the classes added to the menu.
 * @param  bool   $walker   if menu needs the Menu_Walker class.
 * @return string           the html markup of the menu.
 * @since  1.0.0
 */
function print_menu( $location, $classes = 'default', $walker = false ){
	 
	$args = array( 
		'theme_location' => $location,
		'container' => '',
		'menu_class' => $classes,
		'menu_id'=> '',
	); 

	if( $walker ) {
		$args['walker'] = new Theme_Menu_Walker();
	}

	wp_nav_menu( $args );
}

/**
 * prints a list of terms into a comma separated string
 * @param  array $terms an array of post terms
 * @return string       comma separated string
 * @since  1.0.0
 */
function print_terms( $terms ){
	foreach ( $terms as $term ){
		$list[] = $term;
	}

	$string = join( ", ", $list );
	echo $string;
}

// Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
function print_pagination()
{
    global $wp_query;
    $big = 999999999;
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages
    ));
}
