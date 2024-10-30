<?php
/**
 * ImagePress taxonomy filters
 *
 * Customises taxonomy properties
 *
 * @package ImagePress
 * @subpackage Template
 * @since 7.1.0
 */

add_shortcode( 'imagepress-loop', 'imagepress_loop' );

function imagepress_url_builder( $sort, $range, $taxonomy, $query = '' ) {
    if ( empty( $sort ) && ! empty( $_GET['sort'] ) ) {
        $sort = sanitize_text_field( wp_unslash( $_GET['sort'] ) );
    }

    if ( empty( $range ) && ! empty( $_GET['range'] ) ) {
        $range = sanitize_text_field( wp_unslash( $_GET['range'] ) );
    }

    if ( (string) $taxonomy === 'all' ) {
        $taxonomy = '';
    } elseif ( empty( $taxonomy ) && ! empty( $_GET['t'] ) ) {
        $taxonomy = sanitize_text_field( wp_unslash( $_GET['t'] ) );
    }

    $url_parameters = [
        'sort'  => $sort,
        'range' => $range,
        't'     => $taxonomy,
    ];

    if ( isset( $_SERVER['REQUEST_URI'] ) ) {
        $request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );

        // Get position where '/page' text starts
        $pos = strpos( $request_uri, '/page' );
        // Remove string from specific position
        $final_url = substr( $request_uri, 0, $pos );

        return strtok( $final_url, '?' ) . '?' . http_build_query( $url_parameters );
    }
}

function imagepress_get_discover_filters() {
    $query_value = '';

    if ( is_tax() ) {
        $term = get_query_var( 'term' );
    }

    $sort  = ( isset( $_GET['sort'] ) ) ? sanitize_text_field( wp_unslash( $_GET['sort'] ) ) : '';
    $range = ( isset( $_GET['range'] ) ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '';
    $tax   = ( isset( $_GET['t'] ) ) ? sanitize_text_field( wp_unslash( $_GET['t'] ) ) : '';

    $out = '<p class="ip-sorter-primary" id="ip-sorter-primary">
        <select name="sorter" id="sorter">
            <option value="" ' . selected( '', $sort, false ) . '>' . __( 'Sort by...', 'image-gallery' ) . '</option>
            <option value="' . imagepress_url_builder( 'newest', '', '', $query_value ) . '" ' . selected( 'newest', $sort, false ) . '>' . __( 'Newest', 'image-gallery' ) . '</option>
            <option value="' . imagepress_url_builder( 'oldest', '', '', $query_value ) . '" ' . selected( 'oldest', $sort, false ) . '>' . __( 'Oldest', 'image-gallery' ) . '</option>
            <option value="' . imagepress_url_builder( 'comments', '', '', $query_value ) . '" ' . selected( 'comments', $sort, false ) . '>' . __( 'Most comments', 'image-gallery' ) . '</option>
            <option value="' . imagepress_url_builder( 'views', '', '', $query_value ) . '" ' . selected( 'views', $sort, false ) . '>' . __( 'Most views', 'image-gallery' ) . '</option>
            <option value="' . imagepress_url_builder( 'likes', '', '', $query_value ) . '" ' . selected( 'likes', $sort, false ) . '>' . __( 'Most liked', 'image-gallery' ) . '</option>
        </select>

        <select name="ranger" id="ranger">
            <option value="" ' . selected( '', $range, false ) . '>' . __( 'Restrict by...', 'image-gallery' ) . '</option>
            <option value="' . imagepress_url_builder( '', 'alltime', '', $query_value ) . '" ' . selected( 'alltime', $range, false ) . '>' . __( 'All time', 'image-gallery' ) . '</option>
            <option value="' . imagepress_url_builder( '', 'lastmonth', '', $query_value ) . '" ' . selected( 'lastmonth', $range, false ) . '>' . __( 'This month', 'image-gallery' ) . '</option>
            <option value="' . imagepress_url_builder( '', 'lastweek', '', $query_value ) . '" ' . selected( 'lastweek', $range, false ) . '>' . __( 'This week', 'image-gallery' ) . '</option>
            <option value="' . imagepress_url_builder( '', 'lastday', '', $query_value ) . '" ' . selected( 'lastday', $range, false ) . '>' . __( 'Today', 'image-gallery' ) . '</option>
        </select>

        <select name="taxxxer" id="taxxxer">
            <option value="" ' . selected( '', $tax, false ) . '>' . __( 'Filter by category...', 'image-gallery' ) . '</option>
            <option value="' . imagepress_url_builder( '', '', 'all', $query_value ) . '">-</a>';

            $terms = get_terms( 'imagepress_image_category' );

    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
        foreach ( $terms as $term ) {
            $out .= '<option value="' . imagepress_url_builder( '', '', $term->slug, $query_value ) . '" ' . selected( $term->slug, $tax, false ) . '>' . $term->name . '</option>';
        }
    }

        $out .= '</select>

        <span id="ip-sorter-loader" class="ip-sorter-loader"></span>
    </p>';

    return $out;
}

function imagepress_loop( $atts ) {
    extract(
        shortcode_atts(
            [
                'category'      => '',
                'count'         => 0,
                'limit'         => 999999,
                'user'          => 0,
                'columns'       => '',
                'pending'       => 'no',
                'sort'          => 'no',
                'filters'       => 'no',
                'type'          => '', // 'random'
                'collection'    => '', // new parameter (will extract all images from a certain collection)
                'collection_id' => '', // new parameter (will extract all images from a certain collection)
                'order'         => '', // only used by profile viewer

                'fieldname'     => '',
                'fieldvalue'    => '',
                'mode'          => '',
            ],
            $atts
        )
    );

    $out     = '';
    $ip_slug = (string) get_imagepress_option( 'ip_slug' );

    if ( (string) trim( $filters ) === 'yes' ) {
        $out .= imagepress_get_discover_filters();
    }

    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

    if ( (int) $count > 0 ) {
        $ipp = (int) $count;
    } else {
        $ipp = (int) get_imagepress_option( 'ip_ipp' );
    }
    $args1 = [
        'post_type'      => $ip_slug,
        'paged'          => $paged,
        'posts_per_page' => $ipp,
        'post_status'    => 'publish',
    ];

    // Search query arguments
    if ( isset( $_GET['q'] ) && ! empty( $_GET['q'] ) ) {
        $query = sanitize_text_field( $_GET['q'] );

        $args1['s'] = (string) $query;
    }

    if ( isset( $_GET['t'] ) && ! empty( $_GET['t'] ) ) {
        $taxonomy           = (string) sanitize_text_field( $_GET['t'] );
        $tax_query          = [
            [
                'taxonomy'         => 'imagepress_image_category',
                'field'            => 'slug',
                'terms'            => $taxonomy,
                'include_children' => true,
                'operator'         => 'IN',
            ],
        ];
        $args1['tax_query'] = $tax_query;
    }

    // Check "category" parameter
    if ( ! empty( $category ) ) {
        $tax_query          = [
            [
                'taxonomy'         => 'imagepress_image_category',
                'field'            => 'slug',
                'terms'            => $category,
                'include_children' => true,
                'operator'         => 'IN',
            ],
        ];
        $args1['tax_query'] = $tax_query;
    }

    // Check "user/author" parameter
    if ( ! empty( $user ) ) {
        $args1['author'] = (int) $user;
    }

    if ( (string) $order === 'custom' ) {
        $args1['orderby'] = 'menu_order';
        $args1['order']   = 'ASC';
    }

    if ( ! empty( $fieldname ) && ! empty( $fieldvalue ) ) {
        $field_query         = [
            [
                'key'   => $fieldname,
                'value' => $fieldvalue,
            ],
        ];
        $args1['meta_query'] = $field_query;
    }

    // Most liked images
    if ( (string) $sort !== 'no' ) {
        $args1['meta_query'] = [
            'key' => '_like_count',
        ];
        $args1['meta_key']   = '_like_count';
        $args1['orderby']    = 'meta_value_num';
        $args1['order']      = 'DESC';
    }

    if ( isset( $_GET['sort'] ) || isset( $_GET['range'] ) ) {
        $sort  = (string) trim( $_GET['sort'] );
        $range = (string) trim( $_GET['range'] );

        if ( $sort == 'likes' ) {
            $args1['meta_query'] = [
                'key' => '_like_count',
            ];
            $args1['meta_key']   = '_like_count';
            $args1['orderby']    = 'meta_value_num';
            $args1['order']      = 'DESC';
        } elseif ( $sort == 'views' ) {
            $args1['meta_query'] = [
                'key' => 'post_views_count',
            ];
            $args1['meta_key']   = 'post_views_count';
            $args1['orderby']    = 'meta_value_num';
            $args1['order']      = 'DESC';
        } elseif ( $sort == 'comments' ) {
            $args1['orderby'] = 'comment_count';
            $args1['orderby'] = 'DESC';
        } elseif ( $sort == 'newest' ) {
            $args1['orderby'] = 'date';
            $args1['order']   = 'DESC';
        } elseif ( $sort == 'oldest' ) {
            $args1['orderby'] = 'date';
            $args1['order']   = 'ASC';
        } else {
            $args1['orderby'] = 'date';
            $args1['order']   = 'DESC';
        }

        // Range filtering
        if ( $range == 'lastmonth' ) {
            $date_query          = [
                'date_query' => [
                    'column' => 'post_date',
                    'after'  => gmdate( 'Y-m-d', strtotime( '-30 days' ) ),
                ],
            ];
            $args1['date_query'] = $date_query;
        } elseif ( $range == 'lastweek' ) {
            $date_query          = [
                'date_query' => [
                    'column' => 'post_date',
                    'after'  => gmdate( 'Y-m-d', strtotime( '-7 days' ) ),
                ],
            ];
            $args1['date_query'] = $date_query;
        } elseif ( $range == 'lastday' ) {
            $date_query          = [
                'date_query' => [
                    'column' => 'post_date',
                    'after'  => gmdate( 'Y-m-d', strtotime( '-1 days' ) ),
                ],
            ];
            $args1['date_query'] = $date_query;
        }
    }

    $ip_query = new WP_Query( $args1 );

    // Image box appearance
    $ip_box_ui = (string) get_imagepress_option( 'ip_box_ui' );

    $out .= '<div id="ip-boxes" class="ip-box-container ip-box-container-' . $ip_box_ui . '">';
    if ( $ip_query->have_posts() ) {
        while ( $ip_query->have_posts() ) {
            $ip_query->the_post();

            $out .= ip_render_grid_element( get_the_ID() );
        }

        // Pagination
        if ( function_exists( 'pagination' ) && (int) $count == 0 ) {
            $out .= pagination( $ip_query->max_num_pages );
        }
    }
    $out .= '</div><div class="ip-clear"></div>';

    wp_reset_postdata();

    return $out;
}














/*
 * ImagePress numbered pagination
 */
function pagination( $pages = '', $range = 2 ) {
    global $paged;

    $showitems = ( $range * 2 ) + 1;
    $display   = '';

    if ( empty( $paged ) ) {
        $paged = 1;
    }

    if ( 1 != $pages ) {
        $display .= '<div class="native-pagination">
            <span>' . $paged . '/' . $pages . '</span>';
        if ( $paged > 2 && $paged > $range + 1 && $showitems < $pages ) {
            $display .= '<a href="' . get_pagenum_link( 1 ) . '">&laquo;</a>';
        }
        if ( $paged > 1 && $showitems < $pages ) {
            $display .= '<a href="' . get_pagenum_link( $paged - 1 ) . '">&lsaquo;</a>';
        }

        for ( $i = 1; $i <= $pages; $i++ ) {
            if ( 1 != $pages && ( ! ( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) ) {
                $display .= ( $paged == $i ) ? '<span class="current">' . $i . '</span>' : '<a href="' . get_pagenum_link( $i ) . '" class="inactive">' . $i . '</a>';
            }
        }

        if ( $paged < $pages && $showitems < $pages ) {
            $display .= '<a href="' . get_pagenum_link( $paged + 1 ) . '">&rsaquo;</a>';
        }
        if ( $paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages ) {
            $display .= '<a href="' . get_pagenum_link( $pages ) . '">&raquo;</a>';
        }
        $display .= '</div>';
    }

    return $display;
}
