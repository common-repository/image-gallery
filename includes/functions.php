<?php
function imagepress_registration() {
    $ip_slug = sanitize_text_field( get_imagepress_option( 'ip_slug' ) );

    if ( empty( $ip_slug ) ) {
        $ip_slug = 'image';
    }

    $image_type_labels = [
        'name'                  => _x( 'Images', 'Post type general name', 'image-gallery' ),
        'singular_name'         => _x( 'Image', 'Post type singular name', 'image-gallery' ),
        'menu_name'             => __( 'ImagePress', 'image-gallery' ),
        'name_admin_bar'        => __( 'Image', 'image-gallery' ),
        'archives'              => __( 'Image archives', 'image-gallery' ),
        'parent_item_colon'     => __( 'Parent image:', 'image-gallery' ),
        'all_items'             => __( 'All images', 'image-gallery' ),
        'add_new_item'          => __( 'Add new image', 'image-gallery' ),
        'add_new'               => __( 'Add new', 'image-gallery' ),
        'new_item'              => __( 'New image', 'image-gallery' ),
        'edit_item'             => __( 'Edit image', 'image-gallery' ),
        'update_item'           => __( 'Update image', 'image-gallery' ),
        'view_item'             => __( 'View image', 'image-gallery' ),
        'search_items'          => __( 'Search image', 'image-gallery' ),
        'not_found'             => __( 'Not found', 'image-gallery' ),
        'not_found_in_trash'    => __( 'Not found in trash', 'image-gallery' ),
        'featured_image'        => __( 'Featured image', 'image-gallery' ),
        'set_featured_image'    => __( 'Set featured image', 'image-gallery' ),
        'remove_featured_image' => __( 'Remove featured image', 'image-gallery' ),
        'use_featured_image'    => __( 'Use as featured image', 'image-gallery' ),
        'insert_into_item'      => __( 'Insert into image', 'image-gallery' ),
        'uploaded_to_this_item' => __( 'Uploaded to this image', 'image-gallery' ),
        'items_list'            => __( 'Images list', 'image-gallery' ),
        'items_list_navigation' => __( 'Images list navigation', 'image-gallery' ),
        'filter_items_list'     => __( 'Filter images list', 'image-gallery' ),
    ];

    $image_type_args = [
        'label'                 => __( 'Image', 'image-gallery' ),
        'description'           => __( 'Image post type', 'image-gallery' ),
        'labels'                => $image_type_labels,
        'supports'              => [ 'title', 'editor', 'author', 'thumbnail', 'comments', 'custom-fields', 'publicize', 'wpcom-markdown' ],
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-format-gallery',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rest_base'             => $ip_slug,
        'rest_controller_class' => 'WP_REST_Posts_Controller',
    ];

    register_post_type( $ip_slug, $image_type_args );

    $image_taxonomy = [
        'name'                       => _x( 'Image categories', 'Taxonomy general name', 'image-gallery' ),
        'singular_name'              => _x( 'Image category', 'Taxonomy singular name', 'image-gallery' ),
        'menu_name'                  => __( 'Image Categories', 'image-gallery' ),
        'all_items'                  => __( 'All image categories', 'image-gallery' ),
        'parent_item'                => __( 'Parent image category', 'image-gallery' ),
        'parent_item_colon'          => __( 'Parent image category:', 'image-gallery' ),
        'new_item_name'              => __( 'New image category', 'image-gallery' ),
        'add_new_item'               => __( 'Add new image category', 'image-gallery' ),
        'edit_item'                  => __( 'Edit image category', 'image-gallery' ),
        'update_item'                => __( 'Update image category', 'image-gallery' ),
        'view_item'                  => __( 'View image category', 'image-gallery' ),
        'separate_items_with_commas' => __( 'Separate image categories with commas', 'image-gallery' ),
        'add_or_remove_items'        => __( 'Add or remove image categories', 'image-gallery' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'image-gallery' ),
        'popular_items'              => __( 'Popular image categories', 'image-gallery' ),
        'search_items'               => __( 'Search image categories', 'image-gallery' ),
        'not_found'                  => __( 'Not found', 'image-gallery' ),
        'no_terms'                   => __( 'No image categories', 'image-gallery' ),
        'items_list'                 => __( 'Image categories list', 'image-gallery' ),
        'items_list_navigation'      => __( 'Image categories list navigation', 'image-gallery' ),
    ];

    $image_category_args = [
        'labels'                => $image_taxonomy,
        'hierarchical'          => true,
        'public'                => true,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'show_in_nav_menus'     => true,
        'show_tagcloud'         => false,
        'show_in_rest'          => true,
        'rest_base'             => 'image-category',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
    ];

    register_taxonomy( 'imagepress_image_category', [ $ip_slug ], $image_category_args );
}

function ip_get_post_views( $post_id ) {
    $count = get_post_meta( $post_id, 'post_views_count', true );
    $count = empty( $count ) ? 0 : $count;

    update_post_meta( $post_id, 'post_views_count', $count );

    return $count;
}
function ip_set_post_views( $post_id ) {
    $count = get_post_meta( $post_id, 'post_views_count', true );
    $count = empty( $count ) ? 1 : $count + 1;

    update_post_meta( $post_id, 'post_views_count', $count );
}



// frontend image editor
function ip_editor() {
    global $wpdb, $post;

    $out = '';

    $current_user = wp_get_current_user();

    // check if user is author // show author tools
    if ( intval( $post->post_author ) === intval( $current_user->ID ) ) {
        $out .= ' | <a href="#" class="ip-editor-display" id="ip-editor-open">' . __( 'Author tools', 'image-gallery' ) . '</a>';

        $edit_id = get_the_ID();

        if ( ! empty( $_POST['post_id'] ) && ! empty( $_POST['post_title'] ) && isset( $_POST['update_post_nonce'] ) && isset( $_POST['postcontent'] ) ) {
            $post_id    = $_POST['post_id'];
            $post_type  = get_post_type( $post_id );
            $capability = ( 'page' === (string) $post_type ) ? 'edit_page' : 'edit_post';
            if ( current_user_can( $capability, $post_id ) && wp_verify_nonce( $_POST['update_post_nonce'], 'update_post_' . $post_id ) ) {
                $post = [
                    'ID'           => esc_sql( $post_id ),
                    'post_content' => ( stripslashes( $_POST['postcontent'] ) ),
                    'post_title'   => esc_sql( $_POST['post_title'] ),
                    'post_name'    => sanitize_text_field( $_POST['post_title'] ),
                ];
                wp_update_post( $post );

                imagepress_process_image( 'imagepress_image_file', $post_id, 1 );

                $images = get_children(
                    [
                        'post_parent'    => $post_id,
                        'post_status'    => 'inherit',
                        'post_type'      => 'attachment',
                        'post_mime_type' => 'image',
                        'order'          => 'ASC',
                        'orderby'        => 'menu_order ID',
                    ]
                );
                $count  = $images ? count( $images ) : 0;
                if ( (int) $count === 1 || ! has_post_thumbnail( $post_id ) ) {
                    foreach ( $images as $attachment_id => $image ) {
                        set_post_thumbnail( $post_id, $image->ID );
                    }
                }

                wp_set_object_terms( $post_id, (int) $_POST['imagepress_image_category'], 'imagepress_image_category' );
            }
        }

        $out         .= '<div id="info" class="ip-editor">
            <form id="post" class="post-edit front-end-form imagepress-form thin-ui-form" method="post" enctype="multipart/form-data">
                <input type="hidden" name="post_id" value="' . $edit_id . '">';
                $out .= wp_nonce_field( 'update_post_' . $edit_id, 'update_post_nonce', true, false );

                $out .= '<p>
                    <label for="post_title">' . __( 'Title', 'image-gallery' ) . '</label><br>
                    <input type="text" id="post_title" name="post_title" value="' . get_the_title( $edit_id ) . '">
                </p>
                <p>
                    <label for="postcontent">' . __( 'Description', 'image-gallery' ) . '</label><br>
                    <textarea id="postcontent" name="postcontent" rows="3">' . wp_strip_all_tags( get_post_field( 'post_content', $edit_id ) ) . '</textarea></p>
                <hr>';

                $ip_category = wp_get_object_terms( $edit_id, 'imagepress_image_category' );

                $out .= imagepress_get_image_categories_dropdown( 'imagepress_image_category', $ip_category[0]->term_id );

                $ip_upload_size = get_imagepress_option( 'ip_upload_size' );
                $uploadsize     = number_format( ( ( $ip_upload_size * 1024 ) / 1024000 ), 0, '.', '' );
                $datauploadsize = $uploadsize * 1024000;

                $out .= '<p><label for="imagepress_image_file">Replace main image (' . $uploadsize . 'MB ' . __( 'maximum', 'image-gallery' ) . ')...</label><br><input type="file" accept="image/*" data-max-size="' . $datauploadsize . '" name="imagepress_image_file" id="imagepress_image_file"></p>
                <hr>';

                $ip_delete_redirection = get_imagepress_option( 'ip_delete_redirection' );
        if ( empty( $ip_delete_redirection ) ) {
            $ip_delete_redirection = home_url();
        }

                $out .= '<p>
                    <input type="submit" id="submit" value="' . __( 'Update', 'image-gallery' ) . '">
                    <a href="#" data-redirect="' . $ip_delete_redirection . '" data-image-id="' . get_the_ID() . '" class="button" id="ip-editor-delete-image">' . __( 'Delete', 'image-gallery' ) . '</a>
                </p>
            </form>
        </div>';

        wp_reset_query();

        return $out;
    }
}

// ip_editor() related actions
function ip_delete_post() {
    check_ajax_referer( 'ajax_nonce', 'nonce' );

    if ( ! current_user_can( 'delete_posts' ) ) {
        wp_send_json_error( 'Unauthorized', 403 );
    }

    $image_id = (int) $_POST['id'];

    if ( wp_delete_post( $image_id ) ) {
        echo 'success';
    }

    wp_die();
}

add_action( 'wp_ajax_ip_delete_post', 'ip_delete_post' );

function ip_update_post_title() {
    check_ajax_referer( 'ajax_nonce', 'nonce' );

    if ( ! current_user_can( 'delete_posts' ) ) {
        wp_send_json_error( 'Unauthorized', 403 );
    }

    $updated_post = [
        'ID'         => (int) $_REQUEST['id'],
        'post_title' => (string) $_REQUEST['title'],
    ];

    wp_update_post( $updated_post );

    echo 'success';

    wp_die();
}

add_action( 'wp_ajax_ip_update_post_title', 'ip_update_post_title' );



// main ImagePress image function
function ip_main( $image_id ) {
    global $wpdb, $post;

    $post_thumbnail_id  = get_post_thumbnail_id( $image_id );
    $image_attributes   = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
    $post_thumbnail_url = $image_attributes[0];

    if ( intval( get_imagepress_option( 'ip_comments' ) ) === 1 ) {
        $ip_comments = '<em> | </em><a href="' . get_permalink( $image_id ) . '"><i class="ai-chat-dots"></i> ' . get_comments_number( $image_id ) . '</a> ';
    }
    if ( intval( get_imagepress_option( 'ip_comments' ) ) === 0 ) {
        $ip_comments = '';
    }
    ?>

    <div class="imagepress-container">
        <a href="<?php echo esc_url( $post_thumbnail_url ); ?>">
            <?php the_post_thumbnail( 'full' ); ?>
        </a>
        <?php ip_set_post_views( $image_id ); ?>
    </div>

    <div class="ip-bar">
        <?php echo wp_kses_post( ipGetPostLikeLink( $image_id ) ); ?><em> | </em><i class="ai-eye-open"></i> <?php echo intval( ip_get_post_views( $image_id ) ); ?><?php echo wp_kses_post( $ip_comments ); ?>
        <?php
        /*
         * Image editor
         */
        echo wp_kses_post( ip_editor() );
        ?>
    </div>

    <h1 class="ip-title">
        <?php echo esc_html( get_the_title( $image_id ) ); ?>
    </h1>

    <p>
        <div style="float: left; margin: 0 8px 0 0;">
            <?php echo get_avatar( $post->post_author, 40 ); ?>
        </div>
        <?php esc_html_e( 'by', 'image-gallery' ); ?> <b><?php echo wp_kses_post( ip_get_profile_url( $post->post_author ) ); ?></b>
        <br><small><?php esc_html_e( 'Uploaded', 'image-gallery' ); ?> <time title="<?php the_time( get_option( 'date_format' ) ); ?>"><?php echo esc_attr( human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ago' ); ?></time> <?php esc_html_e( 'in', 'image-gallery' ); ?> <?php echo get_the_term_list( get_the_ID(), 'imagepress_image_category', '', ', ', '' ); ?></small>
    </p>

    <div class="ip-clear"></div>

    <?php imagepress_get_images( $image_id ); ?>

    <section>
        <?php the_content(); ?>
    </section>

    <section role="navigation">
        <?php previous_post_link( '%link', esc_html__( 'Previous', 'image-gallery' ) ); ?>
        <?php next_post_link( '%link', esc_html__( 'Next', 'image-gallery' ) ); ?>
    </section>
    <?php
}



// main ImagePress image function
function ip_main_return( $image_id ) {
    global $wpdb, $post;

    $out = '';
    ip_set_post_views( $image_id );

    $post_thumbnail_id  = get_post_thumbnail_id( $image_id );
    $image_attributes   = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
    $post_thumbnail_url = $image_attributes[0];

    $ip_comments = '';

    if ( (int) get_imagepress_option( 'ip_comments' ) === 1 ) {
        $ip_comments = '<em> | </em><a href="' . get_permalink( $image_id ) . '"><i class="ai-chat-dots"></i> ' . get_comments_number( $image_id ) . '</a> ';
    }

    $out .= '<div class="imagepress-container">
        <a href="' . $post_thumbnail_url . '"><img src="' . $image_attributes[0] . '" width="' . $image_attributes[1] . '" height="' . $image_attributes[2] . '" alt="' . get_permalink( $image_id ) . '"></a>
    </div>

    <div class="ip-bar">
        <i class="ai-eye-open"></i> ' . intval( ip_get_post_views( $image_id ) ) . $ip_comments;

        $out .= ip_editor();
    $out     .= '</div>

    <h1 class="ip-title"></h1>

    <p>
        <div style="float: left; margin: 0 8px 0 0;">' . get_avatar( $post->post_author, 40 ) . '</div>' .
        __( 'by', 'image-gallery' ) . ' <b>' . ip_get_profile_url( $post->post_author ) . '</b>
        <br><small>' . __( 'Uploaded', 'image-gallery' ) . ' <time>' . human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ago' . '</time> ' . __( 'in', 'image-gallery' ) . ' ' . get_the_term_list( get_the_ID(), 'imagepress_image_category', '', ', ', '' ) . '</small>
    </p>

    <div class="ip-clear"></div>
    <section>' .
        get_the_content() .
    '</section>';

    $out .= '<hr><section role="navigation"><p>' .
        get_previous_post_link( '%link', esc_html__( 'Previous', 'image-gallery' ) ) . ' | ' .
        get_next_post_link( '%link', esc_html__( 'Next', 'image-gallery' ) ) .
    '</p></section>';

    return $out;
}



function imagepress_get_images( $post_id ) {
    $thumbnail_id = get_post_thumbnail_id();
    $images       = get_children(
        [
            'post_parent'    => $post_id,
            'post_status'    => 'inherit',
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'order'          => 'ASC',
            'orderby'        => 'menu_order ID',
        ]
    );

    if ( $images && count( $images ) > 1 ) {
        echo '<div class="ip-more">';
        foreach ( $images as $attachment_id => $image ) {
            if ( $image->ID != $thumbnail_id ) {
                $big_array = image_downsize( $image->ID, 'full' );

                echo '<img src="' . esc_url( $big_array[0] ) . '" alt="">';
            }
        }
        echo '</div>';
    }

    $videos = get_children(
        [
            'post_parent' => $post_id,
            'post_status' => 'inherit',
            'post_type'   => 'attachment',
            'order'       => 'ASC',
            'orderby'     => 'menu_order ID',
        ]
    );

    if ( $videos && count( $videos ) > 1 ) {
        echo '<div class="ip-more">';
        foreach ( $videos as $attachment_id => $video ) {
            if ( strpos( get_post_mime_type( $video->ID ), 'video' ) !== false ) {
                echo '<video width="100%" class="ip-video-secondary" controls>
                        <source src="' . esc_url( wp_get_attachment_url( $video->ID ) ) . '" type="' . esc_attr( get_post_mime_type( $video->ID ) ) . '">
                        Your browser does not support HTML5 video.
                    </video>';
            }
        }
        echo '</div>';
    }
}

function kformat( $number ) {
    $number = (int) $number;

    return number_format( $number, 0, '.', ',' );
}

function ip_related() {
    global $post;

    $out = '<h3>' . __( 'More by the same author', 'image-gallery' ) . '</h3>' .
    cinnamon_get_related_author_posts( $post->post_author );

    return $out;
}

function ip_author() {
    echo do_shortcode( '[cinnamon-profile]' );
}



function ip_return_image_sizes() {
    global $_wp_additional_image_sizes;

    $image_sizes = [];
    foreach ( get_intermediate_image_sizes() as $size ) {
        $image_sizes[ $size ] = [ 0, 0 ];
        if ( in_array( $size, [ 'thumbnail', 'medium', 'large' ] ) ) {
            $image_sizes[ $size ][0] = get_option( $size . '_size_w' );
            $image_sizes[ $size ][1] = get_option( $size . '_size_h' );
        } elseif ( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $size ] ) ) {
                $image_sizes[ $size ] = [ $_wp_additional_image_sizes[ $size ]['width'], $_wp_additional_image_sizes[ $size ]['height'] ];
        }
    }
    return $image_sizes;
}

add_filter( 'wp_dropdown_cats', 'ip_wp_dropdown_categories_required', 10, 2 );
function ip_wp_dropdown_categories_required( $output, $args ) {
    if ( isset( $args['required'] ) && $args['required'] ) {
        $output = preg_replace(
            '^' . preg_quote( '<select ' ) . '^',
            '<select required ',
            $output
        );
    }

    return $output;
}

function ip_get_user_role() {
    global $current_user;

    $user_roles = $current_user->roles;
    $user_role  = array_shift( $user_roles );

    return $user_role;
}

function ip_get_field( $atts ) {
    $attributes = shortcode_atts(
        [
            'field' => '',
        ],
        $atts
    );

    $field = get_post_meta( get_the_ID(), $attributes['field'], true );

    return $field;
}

function imagepress_order_list() {
    foreach ( $_POST['listItem'] as $position => $item ) {
        $post_data = [
            'ID'         => intval( $item ),
            'menu_order' => intval( $position ),
        ];

        wp_update_post( $post_data );
    }
}
add_action( 'wp_ajax_imagepress_list_update_order', 'imagepress_order_list' );
add_action( 'wp_ajax_nopriv_imagepress_list_update_order', 'imagepress_order_list' );



/*
 * Refactoring of option management functions.
 * Use a get_option() wrapper.
 */
function get_imagepress_option( $option ) {
    $ip_options = get_option( 'imagepress' );

    return $ip_options[ $option ];
}

function ip_update_option( $option_array ) {
    $imagepress_option = get_option( 'imagepress' );
    $updated_array     = array_merge( $imagepress_option, $option_array );

    update_option( 'imagepress', $updated_array );
}

function ip_get_profile_url( $author_id, $structure = true ) {
    $ip_profile_link = '<span class="name"><a href="' . get_author_posts_url( $author_id ) . '">' . get_the_author_meta( 'user_nicename', $author_id ) . '</a></span>';

    if ( $structure === false ) {
        $ip_profile_link = get_author_posts_url( $author_id );
    }

    return $ip_profile_link;
}

function ip_render_grid_element( $element_id ) {
    $out = '';

    // Set default values
    $post_thumbnail_id = get_post_thumbnail_id( $element_id );

    // Get ImagePress grid options
    $ip_click_behaviour    = get_imagepress_option( 'ip_click_behaviour' );
    $get_imagepress_title  = get_imagepress_option( 'ip_title_optional' );
    $get_imagepress_author = get_imagepress_option( 'ip_author_optional' );
    $get_imagepress_meta   = get_imagepress_option( 'ip_meta_optional' );
    $get_imagepress_views  = get_imagepress_option( 'ip_views_optional' );
    $get_ip_comments       = get_imagepress_option( 'ip_comments' );
    $ip_ipw                = get_imagepress_option( 'ip_ipw' );
    $size                  = get_imagepress_option( 'ip_image_size' );

    if ( $ip_click_behaviour === 'media' ) {
        // Get attachment source
        $image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );

        $ip_image_link = $image_attributes[0];
    } elseif ( $ip_click_behaviour === 'custom' ) {
        $ip_image_link = get_permalink( $element_id );
    }

    // Make all "brick" elements optional and active by default
    $ip_title_optional = '';
    if ( (int) $get_imagepress_title === 1 ) {
        $ip_title_optional = '<span class="imagetitle">' . get_the_title( $element_id ) . '</span>';
    }

    $ip_author_optional = '';
    if ( (int) $get_imagepress_author === 1 ) {
        // Get post author ID
        $post_author_id = get_post_field( 'post_author', $element_id );

        $ip_author_optional = ip_get_profile_url( $post_author_id );
    }

    $ip_meta_optional = '';
    if ( (int) $get_imagepress_meta === 1 ) {
        $ip_meta_optional = '<span class="imagecategory" data-tag="' . wp_strip_all_tags( get_the_term_list( $element_id, 'imagepress_image_category', '', ', ', '' ) ) . '">' . wp_strip_all_tags( get_the_term_list( $element_id, 'imagepress_image_category', '', ', ', '' ) ) . '</span>';
    }

    $ip_views_optional = '';
    if ( (int) $get_imagepress_views === 1 ) {
        $ip_views_optional = '<span class="imageviews"><i class="ai-eye-open"></i> ' . intval( ip_get_post_views( $element_id ) ) . '</span> ';
    }

    /**/
    $ip_comments = '';
    if ( $get_ip_comments == 1 ) {
        $ip_comments = '<span class="imagecomments"><i class="ai-chat-dots"></i> ' . get_comments_number( $element_id ) . '</span> ';
    }

    $image_attributes = wp_get_attachment_image_src( $post_thumbnail_id, $size );

    $out .= '<div class="ip_box ip_box_' . $element_id . '" style="width: ' . ( 100 / $ip_ipw ) . '%;">
        <a href="' . $ip_image_link . '" data-taxonomy="' . wp_strip_all_tags( get_the_term_list( $element_id, 'imagepress_image_category', '', ', ', '' ) ) . '" data-src="' . $image_attributes[0] . '" title="' . get_the_title( $element_id ) . '"><img src="' . $image_attributes[0] . '" alt="' . get_the_title( $element_id ) . '"></a>
        <div class="ip_box_top">' . $ip_title_optional . $ip_author_optional . $ip_meta_optional . '</div>
        <div class="ip_box_bottom">' . $ip_views_optional . $ip_comments . '</div>
    </div>';
    /**/

    return $out;
}
