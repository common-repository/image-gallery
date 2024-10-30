<?php
function cinnamon_count_user_posts_by_type( $userid ) {
    $ip_slug = get_imagepress_option( 'ip_slug' );

    $query_args = [
        'author'         => intval( $userid ),
        'post_type'      => $ip_slug,
        'post_status'    => 'any',
        'fields'         => 'ids',
        'posts_per_page' => -1,
        'no_found_rows'  => true,
    ];

    $query = new WP_Query( $query_args );
    $count = $query->found_posts;

    return apply_filters( 'get_usernumposts', $count, $userid );
}

function cinnamon_PostViews($id, $count = true) {
    $axCount = get_user_meta($id, 'ax_post_views', true);
    if ($axCount == '')
        $axCount = 0;

    if ($count == true) {
        $axCount++;
        update_user_meta($id, 'ax_post_views', $axCount);
    }

    return $axCount;
}

function cinnamon_get_related_author_posts($author) {
    $ip_slug = get_imagepress_option('ip_slug');
    $authors_posts = get_posts(array(
        'author' => $author,
        'posts_per_page' => 9,
        'post_type' => $ip_slug
    ));

    $output = '';
    if($authors_posts) {
        $output .= '
        <div class="cinnamon-grid"><ul>';
            foreach($authors_posts as $authors_post) {
                $post_thumbnail_id = get_post_thumbnail_id( $authors_post->ID );
                $image_attributes  = wp_get_attachment_image_src( $post_thumbnail_id, 'thumbnail' );

                $output .= '<li><a href="' . get_permalink( $authors_post->ID ) . '"><img src="' . $image_attributes[0] . '" width="' . $image_attributes[1] . '" height="' . $image_attributes[2] . ' alt="' . get_the_title( $authors_post->ID ) . '"></a></li>';
            }

        $output .= '</ul></div>';
    }

    return $output;
}
