<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Jinn
 */

if ( ! function_exists( 'jinn_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function jinn_posted_on() {
        global $post;
    
	$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<i class="fa fa-refresh tip"><span class="tooltip">' .
                            esc_attr__( 'First posted: ', 'jinn' ) .
                            '<time class="entry-date published" datetime="%1$s">%2$s</time></span>' . 
                        '</i>' . 
                        '<time class="entry-date published updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		'<a href="%1$s" rel="bookmark">%2$s</a>',
                esc_url( get_permalink() ),
                wp_kses( $time_string, array( 
                    'i' => array( 'class' => array() ), 
                    'span' => array( 'class' => array() ),
                    'time' => array( 'class' => array(), 'datetime' => array() ) 
                    ) )
	);

	$byline = sprintf(
                /* translators: %s: post author name */
		esc_html_x( 'By %s', 'post author', 'jinn' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	//echo '<span class="byline"> ' . $byline . '</span>';
        echo '<span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.
        
        // Categories
        if ( 'post' === get_post_type() || 'jetpack-portfolio' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
                if( 'post' === get_post_type() ) {
                    $categories_list = get_the_category_list( __( '</li><li>', 'jinn' ) );
                } elseif ( 'jetpack-portfolio' === get_post_type() ) {
                    $categories_list = get_the_term_list( $post->ID, 'jetpack-portfolio-type', '', '</li><li>', '' );
                }
                $first = strpos( $categories_list, '</a>' );
                $first_cat = substr( $categories_list, 0, ( $first + 4 ) );
                $replaced = str_replace( '<a ', '<a class="first-cat-link" ', $first_cat );
                $the_rest = substr( $categories_list, ( $first + 4 ) );

		if ( $categories_list && jinn_categorized_blog() ) {
                        echo '<span class="cat-links">';
                        if( 'post' === get_post_type() ) {
                            esc_html_e( 'Filed under: ', 'jinn' );
                        } elseif( 'jetpack-portfolio' === get_post_type() ) {
                            esc_html_e( 'Project type: ', 'jinn' );
                        }
                        echo wp_kses( $replaced, array( 
                                            'a' => array( 
                                                'href' => array(),
                                                'class' => array(),
                                                'rel' => array()
                                            ) ) );
                        if( ! empty( $the_rest ) ) {
                            echo '<span class="jinn_cat_switch"><i class="fa fa-angle-down"></i></span>';
                            printf( '<ul class="submenu dropdown">' . wp_kses( $the_rest, array( 
                                            'li' => array( 'class' => array() ),
                                            'a' => array(
                                                'href' => array(),
                                                'class' => array(),
                                                'rel' => array()
                                            ) ) ) . '</ul>', 
                                    wp_kses( $the_rest, array( 
                                            'li' => array( 'class' => array() ),
                                            'a' => array(
                                                'href' => array(),
                                                'class' => array(),
                                                'rel' => array()
                                            ) ) ) ); // WPCS: XSS OK.     
                        }
                        echo '</span>';
		}
	}
        
        if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) {
                echo '<span class="comments-link">';
                comments_popup_link( esc_html__( 'Comment', 'jinn' ), esc_html__( '1 Comment', 'jinn' ), esc_html__( '% Comments', 'jinn' ) );
                echo '</span>';
        }
        
        edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'jinn' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		'<span class="edit-link">',
		'</span>'
	);

}
endif;

if ( ! function_exists( 'jinn_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function jinn_entry_footer() {
    global $post;
    
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() || 'jetpack-portfolio' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
            if( 'post' === get_post_type() ) {
		$categories_list = get_the_category_list( esc_html__( ', ', 'jinn' ) );
            } elseif( 'jetpack-portfolio' === get_post_type() ) {
                $categories_list = get_the_term_list( $post->ID, 'jetpack-portfolio-type', '', esc_html_x(', ', 'Used between list items, there is a space after the comma.', 'jinn' ), '');
            }
		if ( $categories_list && jinn_categorized_blog() ) {
			printf( '<span class="cat-links">' . $categories_list . '</span>', $categories_list ); // WPCS: XSS OK.
		}

		/* translators: used between list items, there is a space after the comma */
                if ( 'post' === get_post_type() ) {
                    $tags_list = get_the_tag_list( '<li class="label radius">', '</li><li class="label radius">', '</li>' );
                } elseif ( 'jetpack-portfolio' === get_post_type() ) {
                    $tags_list = get_the_term_list( $post->ID, 'jetpack-portfolio-tag', '<li class="label radius">', '</li><li class="label radius">', '</li>' );
                }
		if ( $tags_list ) {
			echo '<ul class="tags-links">' . $tags_list . '</ul>'; // WPCS: XSS OK.
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link( esc_html__( 'Leave a comment', 'jinn' ), esc_html__( '1 Comment', 'jinn' ), esc_html__( '% Comments', 'jinn' ) );
		echo '</span>';
	}
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function jinn_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'jinn_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'jinn_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so jinn_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so jinn_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in jinn_categorized_blog.
 */
function jinn_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'jinn_categories' );
}
add_action( 'edit_category', 'jinn_category_transient_flusher' );
add_action( 'save_post',     'jinn_category_transient_flusher' );



/*==============================================================================
 * JINN CUSTOM TAGS BELOW
 =============================================================================*/
/**
 * Fancy excerpts
 * 
 * @link: http://wptheming.com/2015/01/excerpt-versus-content-for-archives/
 */
function jinn_fancy_excerpt() {
    global $post;
    if( is_archive() ) {
        echo '<div class="continue-reading">';
        echo '<a class="more-link" href="' . esc_url( get_permalink() ) . '" title="' . esc_html__( 'Keep Reading ', 'jinn' ) . get_the_title() . '" rel="bookmark">Keep Reading</a>'; 
        echo '</div>';
    } elseif ( is_page_template( 'page-templates/page-child-pages.php' ) ) {
        the_excerpt();
        echo '<a class="continue-reading-arrow" href="' . esc_url( get_permalink() ) . '" title="' . esc_html__( 'Keep Reading ', 'jinn' ) . get_the_title() . '" rel="bookmark">&rarr;</a>'; 
    } elseif ( has_excerpt() || is_page_template( 'page-templates/frontpage-portfolio.php' ) ) {
        the_excerpt();
        echo '<div class="continue-reading">';
        echo '<a class="more-link" href="' . esc_url( get_permalink() ) . '" title="' . esc_html__( 'Keep Reading ', 'jinn' ) . get_the_title() . '" rel="bookmark">Keep Reading</a>'; 
        echo '</div>';
    } elseif ( strpos ( $post->post_content, '<!--more-->' ) ) {
        the_content();
    } elseif ( str_word_count ( $post->post_content ) < 200 ) {
        the_content();
    } else {
        the_excerpt();
        echo '<div class="continue-reading">';
        echo '<a class="more-link" href="' . esc_url( get_permalink() ) . '" title="' . esc_html__( 'Keep Reading ', 'jinn' ) . get_the_title() . '" rel="bookmark">Keep Reading</a>'; 
        echo '</div>';
    }
}

/*
 * Customize the read-more indicator for excerpts
 */
function jinn_excerpt_more( $more ) {
    return " &hellip;";
}
add_filter( 'excerpt_more', 'jinn_excerpt_more' );

/**
 * Add an author box below posts
 * @link http://www.wpbeginner.com/wp-tutorials/how-to-add-an-author-info-box-in-wordpress-posts/
 */
function jinn_author_box() {
    global $post;
    
    // Detect if a post author is set
    if ( isset( $post->post_author ) ) {
        
        /*
         * Get Author info
         */
        $display_name = get_the_author_meta( 'display_name', $post->post_author );                  // Get the author's display name  
            if ( empty ( $display_name ) ) $display_name = get_the_author_meta( 'nickname', $post->post_author ); // If display name is not available, use nickname
        $user_desc =    get_the_author_meta( 'user_description', $post->post_author );              // Get bio info
        $user_site =    get_the_author_meta( 'url', $post->post_author );                           // Website URL
        $user_posts =   get_author_posts_url( get_the_author_meta( 'ID', $post->post_author ) );    // Link to author archive page
        
        /*
         * Create the Author box
         */
        $author_details  = '<aside class="author_bio_section">';
        $author_details .= '<h3 class="author-title"><span>About ';
            if ( is_author() ) $author_details .= $display_name;    // If an author archive, just show the author name
            else $author_details .= 'the Author';                   // If a regular page, show "About the Author"
        $author_details .= '</span></h3>';
        
        $author_details .= '<div class="author-box">';
        $author_details .= '<section class="author-avatar">' . get_avatar( get_the_author_meta( 'user_email' ), 120 ) . '</section>';
        $author_details .= '<section class="author-info">';
        
        if ( ! empty( $display_name ) && ! is_author() ) {          // Don't show this name on an author archive page
            $author_details .= '<h3 class="author-name">';
            $author_details .= '<a class="fn" href="' . $user_posts . '">' . $display_name . '</a>';
            $author_details .= '</h3>';
        }
        if ( ! empty( $user_desc ) ) 
            $author_details .= '<p class="author-description">' . $user_desc . '</p>';
        
        if ( ! is_author() ) {  // Don't show the meta info on an author archive page
            $author_details .= '<p class="author-links entry-meta"><span class="vcard">All posts by <a class="fn" href="' . $user_posts . '">' . $display_name . '</a></span>';

            // Check if author has a website in their profile
            if ( ! empty( $user_site ) ) 
                $author_details .= '<a class="author-site" href="' . $user_site . '" target="_blank" rel="nofollow">Website</a></p>';
            else $author_details .= '</p>';
        }
        
        $author_details .= '</section>';
        $author_details .= '</div>';
        $author_details .= '<p class="show-hide-author label">Hide</p>';
        $author_details .= '</aside>';
        
        echo wp_kses_post( $author_details );

    }
    
}

function jinn_portfolio_index_footer() {
    $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
    if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
            $time_string = '<i class="fa fa-refresh tip"><span class="tooltip">' .
                        esc_attr__( 'First posted: ', 'jinn' ) .
                        '<time class="entry-date published" datetime="%1$s">%2$s</time></span>' . 
                    '</i>' . 
                    '<time class="entry-date published updated" datetime="%3$s">%4$s</time>';
    }

    $time_string = sprintf( $time_string,
            esc_attr( get_the_date( 'c' ) ),
            esc_html( get_the_date() ),
            esc_attr( get_the_modified_date( 'c' ) ),
            esc_html( get_the_modified_date() )
    );

    $posted_on = sprintf(
            '<span><a href="%1$s" rel="bookmark">%2$s</a></span>',
            esc_url( get_permalink() ),
            wp_kses( $time_string, array( 
                    'i' => array( 'class' => array() ), 
                    'span' => array( 'class' => array() ),
                    'time' => array( 'class' => array(), 'datetime' => array() ) 
                    ) )
    );
    
    $project_type = get_the_term_list( get_the_ID(), 'jetpack-portfolio-type', '<span class="portfolio-entry-meta cat-links">', esc_html_x( ', ', 'Used between list items, there is a space after the comma.', 'jinn' ), '</span>' );

    $output = '<footer class="entry-footer">';
    $output .= $posted_on;
    $output .= $project_type;
    $output .= '</footer>';
    
    echo wp_kses_post( $output );
}

if ( ! function_exists( 'jinn_breadcrumbs' ) ) :
/**
 * Display Post breadcrumbs when applicable.
 *
 * @since Jinn 1.0
 * 
 * @link: https://www.branded3.com/blog/creating-a-really-simple-breadcrumb-function-for-pages-in-wordpress/
 */
function jinn_breadcrumbs() {
    
    global $post;
    
    $output = '';
    $breadcrumbs = array();
    $separator = '<span class="breadcrumb-separator">&raquo;</span>';
    $breadcrumb_id = 'breadcrumbs';
    $breadcrumb_class = 'entry-meta';
    
    $page_title = '<span class="current">' . get_the_title( $post->ID ) . '</span>';
    $home_link = '<a aria-label="Home" title="Home" class="breadcrumb-home" href="' . home_url() . '"><i class="fa fa-home"></i></a>' . $separator;
    
    $output .= "<div aria-label='You are here:' id='$breadcrumb_id' class='$breadcrumb_class'>";
    $output .= $home_link;
    
    if( $post->post_parent ) {
        $parent_id = $post->post_parent;
        
        while( $parent_id ) {
            $page = get_page( $parent_id );
            $breadcrumbs[] = '<a href="' . get_permalink( $page->ID ) . '">' . get_the_title( $page->ID ) . '</a>';
            $parent_id = $page->post_parent;
        }
        
        $breadcrumbs = array_reverse( $breadcrumbs );
        $breadcrumbs_str = implode( $separator, $breadcrumbs ); 
        $output .= $breadcrumbs_str . $separator;
    }
    
    $output .= $page_title;
    $output .= "</div>";
    
    echo wp_kses_post( $output );
    
    }

endif;

/**
 * Social Menu
 */
function jinn_social_menu() {
    
    if ( has_nav_menu( 'social' ) ) {
        wp_nav_menu(
                array(
                    'theme_location'    => 'social',
                    'container'         => 'div',
                    'container_id'      => 'menu-social-container',
                    'container_class'   => 'menu-social',
                    'menu_id'           => 'menu-social-items',
                    'menu_class'        => 'menu-items',
                    'depth'             => 1,
                    'link_before'       => '<span class="screen-reader-text">',
                    'link_after'        => '</span>',
                    'fallback_cb'       => '',
                )
        );
    }
    
}

/*
 * Post Icon - can be set in any Post or Page with Custom Fields meta value 'post_icon'
 * Accepts BOTH Dashicons and FontAwesome icons - or returns nothing if neither fa- nor dashicons- precedes the String
 */
function jinn_post_icon() {
    
    $output = '';
    
    // Get the Page icon (if any - Set in Custom Fields for the Page)
    $icon = '';
    $icon = get_post_meta( get_the_ID(), 'post_icon', true ); // Set in the Custom Meta of the Post
    if( strstr( $icon, 'dashicons-' ) ) {
        $icon_class = 'dashicons ' . $icon;
    } else if( strstr( $icon, 'fa-' ) ) {
        $icon_class = 'fa ' . $icon;
    } else {
        $icon_class = '';
    }
    if ( $icon_class != '' ) {
        $output .= "<span class='$icon_class'></span>";
    }
    
    return $output;
    
}

function jinn_the_post_icon() {
    echo wp_kses( jinn_post_icon(), array( 'span' => array( 'class' => array() ) ) );
}

/**
 * Function to show the Jetpack sharing and Likes only at the designated locations in Posts and Pages
 */
function jinn_jetpack_sharing() {
    if ( function_exists( 'sharing_display' ) ) {
        sharing_display( '', true );
    }

    if ( class_exists( 'Jetpack_Likes' ) ) {
        $custom_likes = new Jetpack_Likes;
        echo esc_html( $custom_likes->post_likes( '' ) );
    }
}

/**
 * Prints HTML with post navigation.
 */
function jinn_post_navigation() {
    // Don't print empty makrup if there's nowhere to navigate.
    $previous   = ( is_attachment() ) ? get_post ( get_post() -> post_parent ) : get_adjacent_post( false, '', true );
    $next       = get_adjacent_post( false, '', false );
    
    if ( ! $next && ! $previous ) {
        return;
    }
    ?>
    <nav class="navigation post-navigation" role="navigation">
        <h1 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'jinn' ); ?></h1>
        <div class="nav-links" data-equalizer>
                <?php
                        previous_post_link( '<div class="nav-previous" data-equalizer-watch><div class="nav-indicator">' . esc_html_x( 'Previous Post:', 'Previous post', 'jinn' ) . '</div><h4>%link</h4></div>', '%title' );
                        next_post_link(     '<div class="nav-next" data-equalizer-watch><div class="nav-indicator">'     . esc_html_x( 'Next Post:', 'Next post', 'jinn' ) . '</div><h4>%link</h4></div>', '%title' );
                ?>
        </div> <!-- .nav-links -->
    </nav> <!-- .navigation -->
    <?php
}

if ( ! function_exists( 'jinn_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @since Twenty Fourteen 1.0
 *
 * @global WP_Query   $wp_query   WordPress Query object.
 * @global WP_Rewrite $wp_rewrite WordPress Rewrite object.
 */
function jinn_paging_nav() {
	global $wp_query, $wp_rewrite;

	// Don't print empty markup if there's only one page.
	if ( $wp_query->max_num_pages < 2 ) {
		return;
	}

	$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$query_args   = array();
	$url_parts    = explode( '?', $pagenum_link );

	if ( isset( $url_parts[1] ) ) {
		wp_parse_str( $url_parts[1], $query_args );
	}

	$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
	$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

	$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

	// Set up paginated links.
	$links = paginate_links( array(
		'base'     => $pagenum_link,
		'format'   => $format,
		'total'    => $wp_query->max_num_pages,
		'current'  => $paged,
		'mid_size' => 3,
		'add_args' => array_map( 'urlencode', $query_args ),
		'prev_text' => __( '<i class="fa fa-caret-left"></i> Previous', 'jinn' ),
		'next_text' => __( 'Next <i class="fa fa-caret-right"></i>', 'jinn' ),
                'type'      => 'list',
	) );

	if ( $links ) : 

	?>
	<nav class="navigation paging-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php esc_html_e( 'Posts navigation', 'jinn' ); ?></h1>
                <?php echo wp_kses_post( $links ); ?>
	</nav><!-- .navigation -->
	<?php
	endif;
}
endif;


if ( ! function_exists( 'jinn_copyright' ) ) :
/** 
 * Dynamic Copyright as per WPBeginner.com
 * @source: http://www.wpbeginner.com/wp-tutorials/how-to-add-a-dynamic-copyright-date-in-wordpress-footer/
 */
function jinn_copyright() {
    
    global $wpdb;
    
    $copyright_dates = $wpdb->get_results( "SELECT YEAR(min(post_date_gmt)) AS firstdate, YEAR(max(post_date_gmt)) AS lastdate FROM $wpdb->posts WHERE post_status = 'publish' " );
    $output = '';
    $blog_name = get_bloginfo();
    
    if ( $copyright_dates ) {
        $copyright = "&copy; " . $copyright_dates[0]->firstdate;
        if ( $copyright_dates[0]->firstdate != $copyright_dates[0]->lastdate ) {
            $copyright .= " &ndash; " . $copyright_dates[0]->lastdate;
        }
        $output = $copyright . " " . $blog_name;
    }
    return $output;
}
endif;