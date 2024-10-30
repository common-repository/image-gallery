<?php
/*
Plugin Name: ImagePress - Image Gallery
Plugin URI: https://getbutterfly.com/
Description: A simple, multi-user WordPress plugin with a list of advanced options for creating beautiful, responsive image gallery plugin with front-end upload.
Version: 1.3.0
Author: Ciprian Popescu
Author URI: https://getbutterfly.com
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: image-gallery

Image Gallery (c) 2016-2024 Ciprian Popescu (https://getbutterfly.com/)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

if ( ! function_exists( 'add_filter' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );

    exit();
}

define( 'IP_PLUGIN_VERSION', '1.3.0' );
define( 'IP_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname( plugin_basename( __FILE__ ) ) );
define( 'IP_PLUGIN_PATH', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );
define( 'IP_PLUGIN_FILE_PATH', WP_PLUGIN_DIR . '/' . plugin_basename( __FILE__ ) );

// Plugin initialization
function imagepress_init() {
    $ip_slug = get_imagepress_option( 'ip_slug' );

    if ( empty( $ip_slug ) ) {
        $option_array = [
            'ip_slug' => 'image',
        ];

        ip_update_option( $option_array );
    }
}

require IP_PLUGIN_PATH . '/includes/imagepress-install.php';
require IP_PLUGIN_PATH . '/includes/functions.php';
require IP_PLUGIN_PATH . '/includes/alpha-functions.php';
require IP_PLUGIN_PATH . '/includes/cinnamon-users.php';
require IP_PLUGIN_PATH . '/includes/page-settings.php';

add_action( 'plugins_loaded', 'imagepress_init' );
add_action( 'init', 'imagepress_registration' );
add_action( 'admin_menu', 'imagepress_menu' );

function imagepress_menu() {
    global $menu, $submenu;

    add_submenu_page( 'edit.php?post_type=' . get_imagepress_option( 'ip_slug' ), 'ImagePress Settings', 'ImagePress Settings', 'manage_options', 'imagepress_admin_page', 'imagepress_admin_page' );

    $submenu[ 'edit.php?post_type=' . get_imagepress_option( 'ip_slug' ) ][] = [ '<span style="color: #f6e58d;">Documentation</span>', 'manage_options', 'https://getbutterfly.com/support/documentation/imagepress/' ];
    $submenu[ 'edit.php?post_type=' . get_imagepress_option( 'ip_slug' ) ][] = [ '<span style="color: #badc58;">Upgrade</span>', 'manage_options', 'https://getbutterfly.com/wordpress-plugins/imagepress/' ];

    $args = [
        'post_type'   => get_imagepress_option( 'ip_slug' ),
        'post_status' => 'pending',
        'showposts'   => -1,
    ];

    $draft_ip_links = get_posts( $args ) ? count( get_posts( $args ) ) : 0;

    if ( $draft_ip_links ) {
        foreach ( $menu as $key => $value ) {
            if ( (string) $menu[ $key ][2] === 'edit.php?post_type=' . get_imagepress_option( 'ip_slug' ) ) {
                $menu[ $key ][0] .= ' <span class="update-plugins count-' . $draft_ip_links . '"><span class="plugin-count">' . $draft_ip_links . '</span></span>';

                return;
            }
        }

        foreach ( $submenu as $key => $value ) {
            if ( $submenu[ $key ][2] === 'edit.php?post_type=' . get_imagepress_option( 'ip_slug' ) ) {
                $submenu[ $key ][0] .= ' <span class="update-plugins count-' . $draft_ip_links . '"><span class="plugin-count">' . $draft_ip_links . '</span></span>';
                return;
            }
        }
    }
}

add_filter( 'transition_post_status', 'imagepress_notify_status', 10, 3 );
add_filter( 'widget_text', 'do_shortcode' );

/*
 * Add ImagePress CPT to singular template
 */
function imagepress_content_filter( $content ) {
    $ip_slug = get_imagepress_option( 'ip_slug' );

    if ( is_singular() && is_main_query() && in_the_loop() && get_post_type() === (string) $ip_slug ) {
        $new_content  = ip_main_return( get_the_ID() );
        $new_content .= ip_related();

        $content = $new_content;
    }

    return $content;
}

add_filter( 'the_content', 'imagepress_content_filter' );



add_shortcode( 'imagepress-add', 'imagepress_add' );

add_image_size( 'imagepress_sq_std', 250, 250, true );
add_image_size( 'imagepress_pt_std', 250, 375, true );
add_image_size( 'imagepress_ls_std', 375, 250, true );



// Custom thumbnail column
$ip_column_slug = get_imagepress_option( 'ip_slug' );

add_filter( 'manage_edit-' . $ip_column_slug . '_columns', 'imagepress_columns_filter', 10, 1 );
function imagepress_columns_filter( $columns ) {
    $column_thumbnail = [ 'thumbnail' => 'Thumbnail' ];
    $columns          = array_slice( $columns, 0, 1, true ) + $column_thumbnail + array_slice( $columns, 1, null, true );

    return $columns;
}

add_action( 'manage_posts_custom_column', 'ip_column_action', 10, 1 );
function ip_column_action( $column ) {
    global $post;

    switch ( $column ) {
        case 'thumbnail':
            echo get_the_post_thumbnail( $post->ID, 'thumbnail' );
            break;
    }
}
//

function ip_manage_users_custom_column( $output, $column_name, $user_id ) {
    if ( $column_name === 'post_type_quota' ) {
        $quota = get_the_author_meta( 'ip_upload_limit', $user_id );
        $limit = __( 'No quota', 'image-gallery' );

        if ( isset( $quota ) && ! empty( $quota ) ) {
            $limit = $quota;
        } elseif ( ! empty( get_imagepress_option( 'ip_global_upload_limit' ) ) ) {
            $limit = get_imagepress_option( 'ip_global_upload_limit' );
        }

        // Get current user uploads
        $user_uploads = cinnamon_count_user_posts_by_type( $user_id );

        if ( (int) $user_uploads > 0 ) {
            $user_uploads = '<a href="' . admin_url( 'edit.php?post_type=' . get_imagepress_option( 'ip_slug' ) . '&author=' . $user_id ) . '">' . $user_uploads . '</a>';
        }

        return $user_uploads . '/<small>' . $limit . '</small>';
    }
}
add_filter( 'manage_users_custom_column', 'ip_manage_users_custom_column', 10, 3 );

function ip_manage_users_columns( $columns ) {
    $columns['post_type_quota'] = __( 'Images/Quota', 'image-gallery' );

    return $columns;
}
add_filter( 'manage_users_columns', 'ip_manage_users_columns' );

// Main upload function
function imagepress_add( $atts ) {
    $atts = shortcode_atts(
        [
            'category' => '',
        ],
        $atts
    );

    $category = $atts['category'];

    global $wpdb, $current_user;

    $out = '';

    $ip_moderate = (int) get_imagepress_option( 'ip_moderate' );

    if ( isset( $_POST['imagepress_upload_image_form_submitted'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['imagepress_upload_image_form_submitted'] ) ), 'imagepress_upload_image_form' ) ) {
        $ip_status = ( $ip_moderate === 0 ) ? 'pending' : 'publish';

        $ip_image_author  = $current_user->ID;
        $ip_image_caption = uniqid();

        if ( ! empty( $_POST['imagepress_image_caption'] ) ) {
            $ip_image_caption = sanitize_text_field( wp_unslash( $_POST['imagepress_image_caption'] ) );
        }

        $user_image_data = [
            'post_title'   => $ip_image_caption,
            'post_content' => sanitize_text_field( wp_unslash( $_POST['imagepress_image_description'] ) ),
            'post_status'  => $ip_status,
            'post_author'  => $ip_image_author,
            'post_type'    => get_imagepress_option( 'ip_slug' ),
        ];

        // send notification email to administrator
        $notification_email   = get_imagepress_option( 'ip_notification_email' );
        $notification_subject = __( 'New image uploaded!', 'image-gallery' ) . ' | ' . get_bloginfo( 'name' );
        $notification_message = __( 'New image uploaded!', 'image-gallery' ) . ' | ' . get_bloginfo( 'name' );

        if ( ! empty( $_FILES['imagepress_image_file'] ) ) {
            $post_id = wp_insert_post( $user_image_data );
            imagepress_process_image( 'imagepress_image_file', $post_id );

            if ( isset( $_POST['imagepress_image_category'] ) ) {
                wp_set_object_terms( $post_id, (int) $_POST['imagepress_image_category'], 'imagepress_image_category' );
            }

            // Always moderate this category
            $moderated_category = get_imagepress_option( 'ip_cat_moderation_include' );

            if ( ! empty( $moderated_category ) ) {
                if ( (int) $_POST['imagepress_image_category'] === (int) $moderated_category ) {
                    $ip_post                = [];
                    $ip_post['ID']          = $post_id;
                    $ip_post['post_status'] = 'pending';

                    wp_update_post( $ip_post );
                }
            }
            //

            $headers[] = "MIME-Version: 1.0\r\n";
            $headers[] = 'Content-Type: text/html; charset="' . get_option( 'blog_charset' ) . "\"\r\n";
            wp_mail( $notification_email, $notification_subject, $notification_message, $headers );

            $ip_upload_redirection = get_imagepress_option( 'ip_upload_redirection' );
            if ( ! empty( $ip_upload_redirection ) ) {
                wp_redirect( get_imagepress_option( 'ip_upload_redirection' ) );
                exit;
            }
        }

        $out .= '<p class="message noir-success">' . get_imagepress_option( 'ip_upload_success_title' ) . '</p>';
        $out .= '<p class="message"><a href="' . get_permalink( $post_id ) . '">' . get_imagepress_option( 'ip_upload_success' ) . '</a></p>';
    }

    if ( (int) get_imagepress_option( 'ip_registration' ) === 0 && ! is_user_logged_in() ) {
        $out .= '<p>' . __( 'You need to be logged in to upload an image.', 'image-gallery' ) . '</p>';
    }

    if (
        ( (int) get_imagepress_option( 'ip_registration' ) === 0 && is_user_logged_in() ) ||
        (int) get_imagepress_option( 'ip_registration' ) === 1
    ) {
        if ( isset( $_POST['imagepress_image_caption'] ) && isset( $_POST['imagepress_image_category'] ) ) {
            $out                             .= imagepress_get_upload_image_form(
                $imagepress_image_caption     = sanitize_text_field( wp_unslash( $_POST['imagepress_image_caption'] ) ),
                $imagepress_image_category    = sanitize_text_field( wp_unslash( $_POST['imagepress_image_category'] ) ),
                $imagepress_image_description = sanitize_text_field( wp_unslash( $_POST['imagepress_image_description'] ) ),
                $category
            );
        } else {
            $out .= imagepress_get_upload_image_form( '', '', '', $category );
        }
    }

    return $out;
}

function imagepress_jpeg_quality( $quality, $context ) {
    $ip_quality = (int) get_imagepress_option( 'ip_max_quality' );

    return $ip_quality;
}
add_filter( 'jpeg_quality', 'imagepress_jpeg_quality', 10, 2 );



function imagepress_process_image( $file, $post_id, $feature = 1 ) {
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $attachment_id = media_handle_upload( $file, $post_id );

    if ( (int) $feature === 1 ) {
        set_post_thumbnail( $post_id, $attachment_id );
    }

    return $attachment_id;
}

function imagepress_get_upload_image_form( $imagepress_image_caption, $imagepress_image_category, $imagepress_image_description, $imagepress_hardcoded_category ) {
    global $wpdb;

    $current_user = wp_get_current_user();

    // upload form // customize

    // Labels
    $ip_slug               = get_imagepress_option( 'ip_slug' );
    $ip_caption_label      = get_imagepress_option( 'ip_caption_label' );
    $ip_description_label  = get_imagepress_option( 'ip_description_label' );
    $ip_upload_label       = get_imagepress_option( 'ip_upload_label' );
    $ip_upload_tos         = get_imagepress_option( 'ip_upload_tos' );
    $ip_upload_tos_url     = get_imagepress_option( 'ip_upload_tos_url' );
    $ip_upload_tos_content = get_imagepress_option( 'ip_upload_tos_content' );
    $ip_upload_size        = get_imagepress_option( 'ip_upload_size' );

    // Get global upload limit
    $ip_global_upload_limit = get_imagepress_option( 'ip_global_upload_limit' );
    if ( empty( $ip_global_upload_limit ) ) {
        $ip_global_upload_limit = 999999;
    }

    // Get current user uploads
    $user_uploads = cinnamon_count_user_posts_by_type( $current_user->ID );

    // Get upload limit for current user
    $ip_user_upload_limit = get_the_author_meta( 'ip_upload_limit', $current_user->ID );
    if ( ! empty( $ip_user_upload_limit ) ) {
        $ip_upload_limit = $ip_user_upload_limit;
    }
    if ( empty( $ip_upload_limit ) ) {
        $ip_upload_limit = 999999;
    }

    $out = '<div class="ip-uploader" id="fileuploads" data-user-uploads="' . $user_uploads . '" data-upload-limit="' . $ip_upload_limit . '">
        <form id="imagepress_upload_image_form" method="post" action="" enctype="multipart/form-data" class="imagepress-form imagepress-upload-form">';

            $out .= wp_nonce_field( 'imagepress_upload_image_form', 'imagepress_upload_image_form_submitted', true, false );

    if ( ! empty( $ip_caption_label ) ) {
        $out .= '<p>
                    <label>' . $ip_caption_label . '</label>
                    <input type="text" id="imagepress_image_caption" name="imagepress_image_caption" placeholder="' . $ip_caption_label . '" required>
                </p>';
    }

    if ( ! empty( $ip_description_label ) ) {
        $out .= '<p>
                    <label>' . get_imagepress_option( 'ip_description_label' ) . '</label>
                    <textarea id="imagepress_image_description" name="imagepress_image_description" placeholder="' . get_imagepress_option( 'ip_description_label' ) . '" rows="6"></textarea>
                </p>';
    }

            $out .= '<p>';
    if ( '' != $imagepress_hardcoded_category ) {
        $iphcc = get_term_by( 'slug', $imagepress_hardcoded_category, 'imagepress_image_category' ); // ImagePress hard-coded category
        $out  .= '<input type="hidden" id="imagepress_image_category" name="imagepress_image_category" value="' . $iphcc->term_id . '">';
    } else {
        $out .= imagepress_get_image_categories_dropdown( 'imagepress_image_category', '' );
    }
            $out .= '</p>';

            $uploadsize     = number_format( ( ( $ip_upload_size * 1024 ) / 1024000 ), 0, '.', '' );
            $datauploadsize = $uploadsize * 1024000;

            $out .= '<hr>

            <label for="imagepress_image_file" id="dropContainer" class="dropSelector">
                <b>' . __( 'Drop files here<br><small>or</small>', 'image-gallery' ) . '</b><br>
                <input type="file" accept="image/*" data-max-size="' . $datauploadsize . '" name="imagepress_image_file" id="imagepress_image_file" required>
                <br><small>' . $uploadsize . 'MB ' . __( 'maximum', 'image-gallery' ) . '</small>
            </label>

            <hr>';

    if ( (int) $ip_upload_tos === 1 && ! empty( $ip_upload_tos_content ) ) {
        $oninvalid = get_imagepress_option( 'ip_upload_tos_error' );

        $out .= '<p><input type="checkbox" id="imagepress_agree" name="imagepress_agree" value="1" onchange="this.setCustomValidity(validity.valueMissing ? \'' . $oninvalid . '\' : \'\');" required> ';

        if ( ! empty( $ip_upload_tos_url ) ) {
                $out .= '<a href="' . $ip_upload_tos_url . '" target="_blank">' . $ip_upload_tos_content . '</a>';
        } else {
                    $out .= $ip_upload_tos_content;
        }

                        $out .= '</p>
                <script>document.getElementById("imagepress_agree").setCustomValidity("' . $oninvalid . '");</script>';
    }

            $out .= '<p>
                <input type="submit" id="imagepress_submit" name="imagepress_submit" value="' . $ip_upload_label . '" class="button noir-secondary"> <span id="ipload"></span>
            </p>
        </form>
    </div>';

    return $out;
}

function imagepress_get_image_categories_dropdown( $taxonomy, $selected ) {
    return wp_dropdown_categories(
        [
            'taxonomy'        => $taxonomy,
            'name'            => 'imagepress_image_category',
            'selected'        => $selected,
            'exclude'         => get_imagepress_option( 'ip_cat_exclude' ),
            'hide_empty'      => 0,
            'echo'            => 0,
            'orderby'         => 'name',
            'show_option_all' => get_imagepress_option( 'ip_category_label' ),
            'required'        => true,
        ]
    );
}

function imagepress_activate() {
    // Nothing to see here
}

function imagepress_deactivate() {
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'imagepress_activate' );
register_deactivation_hook( __FILE__, 'imagepress_deactivate' );
//register_uninstall_hook( __FILE__, 'imagepress_uninstall');



// enqueue admin scripts and styles
add_action( 'admin_enqueue_scripts', 'ip_enqueue_color_picker' );
function ip_enqueue_color_picker() {
    wp_enqueue_style( 'imagepress', plugins_url( 'css/ip-admin.css', __FILE__ ), [], IP_PLUGIN_VERSION );
}



add_action( 'wp_enqueue_scripts', 'ip_enqueue_scripts' );

function ip_enqueue_scripts() {
    wp_enqueue_style( 'ip-bootstrap', plugins_url( 'css/ip-bootstrap.css', __FILE__ ), [], IP_PLUGIN_VERSION );

    $grid_ui = 'basic';

    if ( (string) get_imagepress_option( 'ip_grid_ui' ) === 'masonry' ) {
        wp_enqueue_script( 'masonry' );
        $grid_ui = 'masonry'; // jQuery Masonry
    } elseif ( (string) get_imagepress_option( 'ip_grid_ui' ) === 'default' ) {
        $grid_ui = 'default'; // jQuery equalHeight
    }

    wp_enqueue_style( 'akaricons', plugins_url( 'css/akar-icons/css/akar-icons.css', __FILE__ ), [], '1.1.0' );

    wp_enqueue_script( 'ipjs-main', plugins_url( 'js/jquery.main.js', __FILE__ ), [ 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ], IP_PLUGIN_VERSION, true );
    wp_localize_script(
        'ipjs-main',
        'ipAjaxVar',
        [
            'imagesperpage'                  => get_imagepress_option( 'ip_ipp' ),
            'processing_error'               => __( 'There was a problem processing your request.', 'image-gallery' ),
            'logged_in'                      => is_user_logged_in() ? 'true' : 'false',
            'ajaxurl'                        => admin_url( 'admin-ajax.php' ),
            'nonce'                          => wp_create_nonce( 'ajax-nonce' ),
            'ip_url'                         => IP_PLUGIN_URL,
            'grid_ui'                        => $grid_ui,

            'ip_global_upload_limit_message' => get_imagepress_option( 'ip_global_upload_limit_message' ),

            'swal_confirm_operation'         => __( "Are you sure? You won't be able to revert this!", 'image-gallery' ),
            'swal_confirm_button'            => __( 'Yes', 'image-gallery' ),
            'swal_cancel_button'             => __( 'No', 'image-gallery' ),
        ]
    );
}
// end



function imagepress_notify_status( $new_status, $old_status, $post ) {
    global $current_user;
    $contributor = get_userdata( $post->post_author );

    $headers[] = "MIME-Version: 1.0\r\n";
    $headers[] = 'Content-Type: text/html; charset="' . get_option( 'blog_charset' ) . "\"\r\n";

    if ( (string) $old_status !== 'pending' && (string) $new_status === 'pending' && (string) get_imagepress_option( 'ip_notification_email' ) !== '' ) {
        $subject  = '[' . get_option( 'blogname' ) . '] "' . $post->post_title . '" pending review';
        $message  = "<p>A new post by {$contributor->display_name} is pending review.</p>";
        $message .= "<p>Author: {$contributor->user_login} <{$contributor->user_email}></p>";
        $message .= "<p>Title: {$post->post_title}</p>";
        $category = get_the_category( $post->ID );
        if ( isset( $category[0] ) ) {
            $message .= "<p>Category: {$category[0]->name}</p>";
        }

        wp_mail( get_imagepress_option( 'ip_notification_email' ), $subject, $message, $headers );
    } elseif ( (string) $old_status === 'pending' && (string) $new_status === 'publish' && (string) get_imagepress_option( 'approvednotification' ) === 'yes' ) {
        $subject = '[' . get_option( 'blogname' ) . '] "' . $post->post_title . '" approved';
        $message = "<p>{$contributor->display_name}, your post has been approved and published at " . get_permalink( $post->ID ) . '.</p>';

        wp_mail( $contributor->user_email, $subject, $message, $headers );
    } elseif ( (string) $old_status === 'pending' && (string) $new_status === 'draft' && (int) $current_user->ID !== (int) $contributor->ID && (string) get_imagepress_option( 'declinednotification' ) === 'yes' ) {
        $subject = '[' . get_option( 'blogname' ) . '] "' . $post->post_title . '" declined';
        $message = "<p>{$contributor->display_name}, your post has not been approved.</p>";

        wp_mail( $contributor->user_email, $subject, $message, $headers );
    }
}
