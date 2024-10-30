<?php
function imagepress_admin_page() {
    ?>
    <div class="wrap">
        <h1><b>ImagePress - Image Gallery</b> Settings</h1>

        <?php
        $ip_slug = get_imagepress_option( 'ip_slug' );

        $tab     = ( filter_has_var( INPUT_GET, 'tab' ) ) ? filter_input( INPUT_GET, 'tab' ) : 'dashboard_tab';
        $section = 'edit.php?post_type=' . $ip_slug . '&page=imagepress_admin_page&amp;tab=';
        ?>
        <h2 class="nav-tab-wrapper ip-nav-tab-wrapper">
            <a href="<?php echo esc_attr( $section ); ?>dashboard_tab" class="nav-tab <?php echo $tab === 'dashboard_tab' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Dashboard', 'image-gallery' ); ?></a>
            <a href="<?php echo esc_attr( $section ); ?>settings_tab" class="nav-tab <?php echo $tab === 'settings_tab' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Settings', 'image-gallery' ); ?></a>
            <a href="<?php echo esc_attr( $section ); ?>configurator_tab" class="nav-tab <?php echo $tab === 'configurator_tab' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Configurator', 'image-gallery' ); ?></a>

            <a href="<?php echo esc_attr( $section ); ?>label_tab" class="nav-tab <?php echo $tab === 'label_tab' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Labels', 'image-gallery' ); ?></a>
            <a href="<?php echo esc_attr( $section ); ?>upload_tab" class="nav-tab <?php echo $tab === 'upload_tab' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Upload', 'image-gallery' ); ?></a>
        </h2>

        <?php
        if ( $tab === 'dashboard_tab' ) {
            global $wpdb;

            // Get the WP built-in version
            $ipdata = get_plugin_data( IP_PLUGIN_FILE_PATH );

            echo '<div id="gb-ad">
                <h3 class="gb-handle">Thank you for using ImagePress - Image Gallery!</h3>
                <div id="gb-ad-content">
                    <div class="inside">
                        <p>If you enjoy this plugin, get the <a href="https://getbutterfly.com/wordpress-plugins/imagepress/" rel="external">PRO version</a>! We work hard to update it, fix bugs, add new features and make it compatible with the latest web technologies.</p>
                    </div>
                    <div class="gb-footer">
                        <p>For support, feature requests and bug reporting, please visit the <a href="https://getbutterfly.com/" rel="external">official website</a>.<br>&copy;' . esc_attr( date_i18n( 'Y' ) ) . ' <a href="https://getbutterfly.com/" rel="external"><strong>getButterfly</strong>.com</a> &middot; <a href="https://getbutterfly.com/support/documentation/imagepress/">Documentation</a> &middot; <small>Code wrangling since 2005</small></p>
                    </div>
                </div>
            </div>

            <p>
                <small>You are using <b>ImagePress - Image Gallery</b> plugin version <strong>' . esc_attr( $ipdata['Version'] ) . '</strong>.</small><br>
                <small>You are using PHP version ' . PHP_VERSION . ' and MySQL server version ' . esc_attr( $wpdb->db_version() ) . '.</small>
            </p>

            <h2>Installation</h2>
            <p>Check the installation steps below and make the required changes.</p>';

            $single_template = 'single-' . $ip_slug . '.php';

            echo '<div class="gb-assistant">';

            if ( (string) $ip_slug === '' ) {
                echo '<p><div class="dashicons dashicons-no"></div> <b>Error:</b> Your image slug is not set. Go to <b>Configurator</b> section and set it.</p>';
            } else {
                echo '<p><div class="dashicons dashicons-yes"></div> <b>Note:</b> Your image slug is <code>' . esc_attr( $ip_slug ) . '</code>. If you changed it recently, visit your <b>Permalinks</b> section and resave the changes.</p>';
            }
            if ( '' !== locate_template( $single_template ) ) {
                echo '<p><div class="dashicons dashicons-yes"></div> <b>Note:</b> You have a custom image template available.</p>';
            }

            echo '</div>

            <div class="gb-assistant">';

            if ( get_option( 'default_role' ) === 'author' ) {
                echo '<p><div class="dashicons dashicons-yes"></div> <b>Note:</b> New user default role is <code>author</code>. Subscribers and contributors are not able to edit their uploaded images.</p>';
            } else {
                echo '<p><div class="dashicons dashicons-no"></div> <b>Error:</b> New user default role should be <code>author</code> in order to allow for front-end image editing. Subscribers and contributors are not able to edit their uploaded images. <a href="' . esc_url( admin_url( 'options-general.php' ) ) . '">Change it</a>.</p>';
            }

            echo '</div>';

            echo '<h3>Shortcodes</h3>
            <p>
                <code>[imagepress-add]</code> - show the submission form.<br>
                <code>[imagepress-add category="landscapes"]</code> - show the submission form with a fixed (hidden) category. Use the category <b>slug</b>.<br>
                <code>[imagepress-loop]</code> - display all images.<br>
                <code>[imagepress-loop user="7"]</code> - filter images by user ID.<br>
                <code>[imagepress-loop count="4"]</code> - display a specific number of images.<br>
                <code>[imagepress-loop filters="yes"]</code> - display all images with filters/sorters.<br>
                <code>[imagepress-loop sort="views" count="10"]</code> - display images sorted by likes.<br>
                <code>[imagepress-loop category="landscapes"]</code> - display all images in a specific category. Use the category <b>slug</b>.<br>
            </p>';
            ?>
            <h2><?php esc_html_e( 'ImagePress PRO', 'image-gallery' ); ?></h2>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); grid-gap: 2em;">
                <div class="ip-card">
                    <h3>What's included?</h3>

                    <ul class="feature-list--pro">
                        <li><span class="dashicons dashicons-yes-alt"></span> Secondary uploads (additional images, variants, progress shots, making of, etc.)</li>
                        <li><span class="dashicons dashicons-yes-alt"></span> Additional styles and themes</li>
                        <li><span class="dashicons dashicons-yes-alt"></span> Collections</li>
                        <li><span class="dashicons dashicons-yes-alt"></span> Custom (user created) categories</li>
                        <li><span class="dashicons dashicons-yes-alt"></span> Keywords/tags</li>
                        <li><span class="dashicons dashicons-yes-alt"></span> Colour search</li>
                        <li><span class="dashicons dashicons-yes-alt"></span> Likes</li>
                        <li><span class="dashicons dashicons-yes-alt"></span> Followers</li>
                        <li><span class="dashicons dashicons-yes-alt"></span> User profiles</li>
                        <li><span class="dashicons dashicons-yes-alt"></span> And lots more!</li>
                    </ul>

                    <p>
                        <a href="https://getbutterfly.com/wordpress-plugins/imagepress/" rel="external noopener" class="button button-primary button-hero">Get ImagePress PRO!</a>
                        <a href="https://getbutterfly.com/wordpress-plugins/imagepress/" rel="external noopener" class="button button-secondary button-hero">ImagePress PRO Demo</a>
                    </p>
                </div>
                <div class="ip-card">
                    <h3>What's optional?</h3>

                    <ul class="feature-list--pro--optional">
                        <li><span class="dashicons dashicons-yes-alt"></span> Bulk (media library) upload</li>
                        <li><span class="dashicons dashicons-yes-alt"></span> Image pins (similar to Pinterest)</li>
                        <li><span class="dashicons dashicons-yes-alt"></span> Custom development and support</li>
                        <li><span class="dashicons dashicons-yes-alt"></span> And lots more!</li>
                    </ul>
                </div>
            </div>
            <?php
        } elseif ( $tab === 'configurator_tab' ) {
            if ( isset( $_POST['isGSSubmit'] ) ) {
                if ( ! isset( $_POST['imagepress_settings_nonce'] ) || ! check_admin_referer( 'save_imagepress_settings_action', 'imagepress_settings_nonce' ) ) {
                    wp_die( esc_html__( 'Nonce verification failed. Please try again.', 'image-gallery' ) );
                }

                $ip_updated_options = [
                    'ip_box_ui'          => isset( $_POST['ip_box_ui'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_box_ui'] ) ) : '',
                    'ip_grid_ui'         => isset( $_POST['ip_grid_ui'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_grid_ui'] ) ) : '',
                    'ip_ipw'             => isset( $_POST['ip_ipw'] ) ? intval( $_POST['ip_ipw'] ) : 0,
                    'ip_ipp'             => isset( $_POST['ip_ipp'] ) ? intval( $_POST['ip_ipp'] ) : 0,
                    'ip_order'           => isset( $_POST['ip_order'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_order'] ) ) : '',
                    'ip_orderby'         => isset( $_POST['ip_orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_orderby'] ) ) : '',
                    'ip_slug'            => isset( $_POST['ip_slug'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_slug'] ) ) : '',
                    'ip_image_size'      => isset( $_POST['ip_image_size'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_image_size'] ) ) : '',
                    'ip_title_optional'  => isset( $_POST['ip_title_optional'] ) ? intval( $_POST['ip_title_optional'] ) : 0,
                    'ip_meta_optional'   => isset( $_POST['ip_meta_optional'] ) ? intval( $_POST['ip_meta_optional'] ) : 0,
                    'ip_views_optional'  => isset( $_POST['ip_views_optional'] ) ? intval( $_POST['ip_views_optional'] ) : 0,
                    'ip_comments'        => isset( $_POST['ip_comments'] ) ? intval( $_POST['ip_comments'] ) : 0,
                    'ip_author_optional' => isset( $_POST['ip_author_optional'] ) ? intval( $_POST['ip_author_optional'] ) : 0,
                ];
                $ip_options         = get_option( 'imagepress' );
                $ip_update          = array_merge( $ip_options, $ip_updated_options );
                update_option( 'imagepress', $ip_update );

                echo '<div class="updated notice is-dismissible"><p>Settings updated successfully!</p></div>';
            }
            ?>
            <form method="post" action="">
                <?php wp_nonce_field( 'save_imagepress_settings_action', 'imagepress_settings_nonce' ); ?>

                <h2><?php esc_html_e( 'Grid Configurator', 'image-gallery' ); ?></h2>
                <p>The <b>Grid configurator</b> allows you to select which information will be visible inside the image box.</p>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label>Image box appearance</label></th>
                            <td>
                                <select name="ip_box_ui" id="ip_box_ui">
                                    <option value="default"
                                    <?php
                                    if ( (string) get_imagepress_option( 'ip_box_ui' ) === 'default' ) {
                                        echo ' selected';}
                                    ?>
                                    >Default</option>
                                    <option value="overlay"
                                    <?php
                                    if ( (string) get_imagepress_option( 'ip_box_ui' ) === 'overlay' ) {
                                        echo ' selected';}
                                    ?>
                                    >Overlay</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label>Image grid display</label></th>
                            <td>
                                <select name="ip_grid_ui" id="ip_grid_ui">
                                    <option value="basic"
                                    <?php
                                    if ( (string) get_imagepress_option( 'ip_grid_ui' ) === 'basic' ) {
                                        echo ' selected';}
                                    ?>
                                    >Basic (no styling)</option>
                                    <option value="default"
                                    <?php
                                    if ( (string) get_imagepress_option( 'ip_grid_ui' ) === 'default' ) {
                                        echo ' selected';}
                                    ?>
                                    >Default (equal height containers)</option>
                                    <option value="masonry"
                                    <?php
                                    if ( (string) get_imagepress_option( 'ip_grid_ui' ) === 'masonry' ) {
                                        echo ' selected';}
                                    ?>
                                    >Masonry</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label>Image box details</label></th>
                            <td>
                            <p>
                                <input name="ip_slug" id="slug" type="text" class="regular-text" placeholder="Image slug" value="<?php echo esc_attr( get_imagepress_option( 'ip_slug' ) ); ?>" required> <label for="ip_slug"><b>Image</b> slug</label>
                                <br><small>Use an appropriate slug for your image (e.g. <b>image</b> in <code>domain.com/<b>image</b>/my-image-name/</code>).</small>
                                <br><small>Tip: use a singular term, one word only, lowercase, letters only (examples: image, poster, illustration).</small>
                            </p>
                            <p>
                                <select name="ip_image_size" id="ip_image_size">
                                    <optgroup label="WordPress (default)">
                                        <option value="thumbnail"
                                        <?php
                                        if ( (string) get_imagepress_option( 'ip_image_size' ) === 'thumbnail' ) {
                                            echo ' selected';}
                                        ?>
                                        >Thumbnail</option>
                                        <option value="medium"
                                        <?php
                                        if ( (string) get_imagepress_option( 'ip_image_size' ) === 'medium' ) {
                                            echo ' selected';}
                                        ?>
                                        >Medium</option>
                                    </optgroup>
                                    <optgroup label="ImagePress (default)">
                                        <option value="imagepress_sq_std"
                                        <?php
                                        if ( (string) get_imagepress_option( 'ip_image_size' ) === 'imagepress_sq_std' ) {
                                            echo ' selected';}
                                        ?>
                                        >Standard (Square)</option>
                                        <option value="imagepress_pt_std"
                                        <?php
                                        if ( (string) get_imagepress_option( 'ip_image_size' ) === 'imagepress_pt_std' ) {
                                            echo ' selected';}
                                        ?>
                                        >Standard (Portrait)</option>
                                        <option value="imagepress_ls_std"
                                        <?php
                                        if ( (string) get_imagepress_option( 'ip_image_size' ) === 'imagepress_ls_std' ) {
                                            echo ' selected';}
                                        ?>
                                        >Standard (Landscape)</option>
                                    </optgroup>
                                    <optgroup label="Other registered sizes (use with care)">
                                        <?php
                                        $ip_image_size = get_imagepress_option( 'ip_image_size' );
                                        $thumbsize     = isset( $ip_image_size ) ? esc_attr( $ip_image_size ) : '';
                                        $image_sizes   = ip_return_image_sizes();
                                        foreach ( $image_sizes as $size => $atts ) {
                                            if ( (int) $atts[0] !== 0 && (int) $atts[1] !== 0 ) {
                                                ?>
                                                <option value="<?php echo esc_attr( $size ); ?>" <?php selected( $thumbsize, $size ); ?>><?php echo esc_attr( $size ) . ' - ' . implode( 'x', array_map( 'esc_attr', $atts ) ); ?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </optgroup>
                                </select> <label for="ip_image_size"><b>Image box</b> thumbnail size</label>
                                <br><small>Use <b>thumbnail</b>, adjust the column size to match your thumbnail size and hide the description in order to have uniform sizes</small>
                            </p>
                            <p>
                                <select name="ip_title_optional" id="ip_title_optional">
                                    <option value="0" <?php selected( 0, intval( get_imagepress_option( 'ip_title_optional' ) ) ); ?>>Hide image title</option>
                                    <option value="1" <?php selected( 1, intval( get_imagepress_option( 'ip_title_optional' ) ) ); ?>>Show image title</option>
                                </select>
                                <label for="ip_title_optional">Show/hide image title</label>
                            </p>
                            <p>
                                <select name="ip_meta_optional" id="ip_meta_optional">
                                    <option value="0" <?php selected( 0, intval( get_imagepress_option( 'ip_meta_optional' ) ) ); ?>>Hide image meta</option>
                                    <option value="1" <?php selected( 1, intval( get_imagepress_option( 'ip_meta_optional' ) ) ); ?>>Show image meta</option>
                                </select>
                                <label for="ip_meta_optional">Show/hide the image meta (category/taxonomy)</label>
                            </p>
                            <p>
                                <select name="ip_views_optional" id="ip_views_optional">
                                    <option value="0" <?php selected( 0, intval( get_imagepress_option( 'ip_views_optional' ) ) ); ?>>Hide image views</option>
                                    <option value="1" <?php selected( 1, intval( get_imagepress_option( 'ip_views_optional' ) ) ); ?>>Show image views</option>
                                </select>
                                <label for="ip_views_optional">Show/hide the number of image views</label>
                            </p>
                            <p>
                                <select name="ip_comments" id="ip_comments">
                                    <option value="0" <?php selected( 0, intval( get_imagepress_option( 'ip_comments' ) ) ); ?>>Hide image comments</option>
                                    <option value="1" <?php selected( 1, intval( get_imagepress_option( 'ip_comments' ) ) ); ?>>Show image comments</option>
                                </select>
                                <label for="ip_comments">Show/hide the number of image comments</label>
                            </p>
                            <p>
                                <select name="ip_author_optional" id="ip_author_optional">
                                    <option value="0" <?php selected( 0, intval( get_imagepress_option( 'ip_author_optional' ) ) ); ?>>Hide image author</option>
                                    <option value="1" <?php selected( 1, intval( get_imagepress_option( 'ip_author_optional' ) ) ); ?>>Show image author</option>
                                </select>
                                <label for="ip_author_optional">Show/hide the author name and link</label>
                            </p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>
                <h2>Grid Settings</h2>
                <p>These settings apply globally for the image and author grid.</p>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label>Image grid details</label></th>
                            <td>
                                <input name="ip_ipw" id="ip_ipw" type="number" value="<?php echo intval( get_imagepress_option( 'ip_ipw' ) ); ?>" min="1" max="1024">
                                <label for="ip_ipw">Images per row (0-32)</label>
                                <br><small>Number of images per grid row.</small>
                                <br>

                                <input name="ip_ipp" id="ip_ipp" type="number" value="<?php echo intval( get_imagepress_option( 'ip_ipp' ) ); ?>" min="1" max="65536">
                                <label for="ip_ipp">Images per page (0-256)</label>
                                <br><small>How many images per page you want to display using the <code>[imagepress-loop]</code> shortcode.</small>

                                <p>
                                    <label for="ip_order">Sort images</label>
                                    <select name="ip_order" id="ip_order">
                                        <option value="ASC" <?php selected( 'ASC', get_imagepress_option( 'ip_order' ) ); ?>>ASC</option>
                                        <option value="DESC" <?php selected( 'DESC', get_imagepress_option( 'ip_order' ) ); ?>>DESC</option>
                                    </select> <label for="ip_orderby">by</label> <select name="ip_orderby" id="ip_orderby">
                                        <option value="none" <?php selected( 'none', get_imagepress_option( 'ip_orderby' ) ); ?>>none</option>
                                        <option value="ID" <?php selected( 'ID', get_imagepress_option( 'ip_orderby' ) ); ?>>ID</option>
                                        <option value="author" <?php selected( 'author', get_imagepress_option( 'ip_orderby' ) ); ?>>author</option>
                                        <option value="title" <?php selected( 'title', get_imagepress_option( 'ip_orderby' ) ); ?>>title</option>
                                        <option value="name" <?php selected( 'name', get_imagepress_option( 'ip_orderby' ) ); ?>>name</option>
                                        <option value="date" <?php selected( 'date', get_imagepress_option( 'ip_orderby' ) ); ?>>date</option>
                                        <option value="rand" <?php selected( 'rand', get_imagepress_option( 'ip_orderby' ) ); ?>>rand</option>
                                        <option value="comment_count" <?php selected( 'comment_count', get_imagepress_option( 'ip_orderby' ) ); ?>>comment_count</option>
                                    </select>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>
                <p><input type="submit" name="isGSSubmit" value="Save Changes" class="button-primary"></p>
            </form>
            <?php
        } elseif ( $tab === 'settings_tab' ) {
            if ( isset( $_POST['isGSSubmit'] ) ) {
                if ( ! isset( $_POST['imagepress_settings_nonce'] ) || ! check_admin_referer( 'save_imagepress_settings_action', 'imagepress_settings_nonce' ) ) {
                    wp_die( esc_html__( 'Nonce verification failed. Please try again.', 'image-gallery' ) );
                }

                $ip_updated_options = [
                    'ip_moderate'               => isset( $_POST['ip_moderate'] ) ? intval( $_POST['ip_moderate'] ) : 0,
                    'ip_registration'           => isset( $_POST['ip_registration'] ) ? intval( $_POST['ip_registration'] ) : 0,
                    'ip_click_behaviour'        => isset( $_POST['ip_click_behaviour'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_click_behaviour'] ) ) : '',
                    'ip_cat_moderation_include' => isset( $_POST['ip_cat_moderation_include'] ) ? intval( $_POST['ip_cat_moderation_include'] ) : 0,
                    'ip_upload_redirection'     => isset( $_POST['ip_upload_redirection'] ) ? sanitize_url( wp_unslash( $_POST['ip_upload_redirection'] ) ) : '',
                    'ip_delete_redirection'     => isset( $_POST['ip_delete_redirection'] ) ? sanitize_url( wp_unslash( $_POST['ip_delete_redirection'] ) ) : '',
                    'ip_notification_email'     => isset( $_POST['ip_notification_email'] ) ? sanitize_email( wp_unslash( $_POST['ip_notification_email'] ) ) : '',
                    'approvednotification'      => isset( $_POST['approvednotification'] ) ? sanitize_text_field( wp_unslash( $_POST['approvednotification'] ) ) : '',
                    'declinednotification'      => isset( $_POST['declinednotification'] ) ? sanitize_text_field( wp_unslash( $_POST['declinednotification'] ) ) : '',
                ];
                $ip_options         = get_option( 'imagepress' );
                $ip_update          = array_merge( $ip_options, $ip_updated_options );
                update_option( 'imagepress', $ip_update );

                echo '<div class="updated notice is-dismissible"><p>Settings updated successfully!</p></div>';
            }
            ?>
            <form method="post" action="">
                <?php wp_nonce_field( 'save_imagepress_settings_action', 'imagepress_settings_nonce' ); ?>

                <h2>General Settings</h2>
                <p>These settings apply globally for all ImagePress users.</p>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="ip_registration">User registration</label></th>
                            <td>
                                <select name="ip_registration" id="ip_registration">
                                    <option value="0" <?php selected( 0, intval( get_imagepress_option( 'ip_registration' ) ) ); ?>>Require user registration (recommended)</option>
                                    <option value="1" <?php selected( 1, intval( get_imagepress_option( 'ip_registration' ) ) ); ?>>Do not require user registration</option>
                                </select>
                                <br><small>Require users to be registered and logged in to upload images (recommended).</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ip_click_behaviour">Image behaviour</label></th>
                            <td>
                                <select name="ip_click_behaviour" id="ip_click_behaviour">
                                    <option value="media" <?php selected( 'media', get_imagepress_option( 'ip_click_behaviour' ) ); ?>>Open media (image)</option>
                                    <option value="custom" <?php selected( 'custom', get_imagepress_option( 'ip_click_behaviour' ) ); ?>>Open image page</option>
                                </select>
                                <br><small>What to open when clicking on an image (single image or custom post template).</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ip_moderate">Image moderation</label></th>
                            <td>
                                <select name="ip_moderate" id="ip_moderate">
                                    <option value="0" <?php selected( 0, intval( get_imagepress_option( 'ip_moderate' ) ) ); ?>>Moderate all images (recommended)</option>
                                    <option value="1" <?php selected( 1, intval( get_imagepress_option( 'ip_moderate' ) ) ); ?>>Do not moderate images</option>
                                </select>
                                <br><small>Moderate all submitted images (recommended).</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ip_cat_moderation_include">Moderate entries in this category</label></th>
                            <td>
                                <input type="number" name="ip_cat_moderation_include" id="ip_cat_moderation_include" value="<?php echo intval( get_imagepress_option( 'ip_cat_moderation_include' ) ); ?>">
                                <br><small>Always moderate entries in this category (use category ID).</small>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>
                <h2>Redirection</h2>
                <p>Optionally redirect users to various pages after image submission/removal. Examples are: a thank you page, a confirmation page, a payment page, a newsletter page or another call to action.</p>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="ip_upload_redirection">Upload redirect</label></th>
                            <td>
                                <input type="url" name="ip_upload_redirection" id="ip_upload_redirection" placeholder="https://" class="regular-text" value="<?php echo esc_url( get_imagepress_option( 'ip_upload_redirection' ) ); ?>">
                                <br><small>Redirect users to this page after image upload (optional, leave blank to disable).</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ip_delete_redirection">Delete redirect</label></th>
                            <td>
                                <input type="url" name="ip_delete_redirection" id="ip_delete_redirection" placeholder="https://" class="regular-text" value="<?php echo esc_url( get_imagepress_option( 'ip_delete_redirection' ) ); ?>">
                                <br><small>Redirect users to this page after image deletion (optional, leave blank to disable).</small>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>
                <h2>Email Settings</h2>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="ip_notification_email">Administrator email<br><small>(used for new image notification)</small></label></th>
                            <td>
                                <input type="text" name="ip_notification_email" id="ip_notification_email" value="<?php echo esc_attr( get_imagepress_option( 'ip_notification_email' ) ); ?>" class="regular-text">
                                <br><small>The administrator will receive an email notification each time a new image is uploaded.</small>
                                <br><small>Separate multiple addresses with comma.</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label>Email Settings</label></th>
                            <td>
                                <p>
                                    <input type="checkbox" id="approvednotification" name="approvednotification" value="yes" <?php checked( 'yes', get_imagepress_option( 'approvednotification' ) ); ?>> <label for="approvednotification">Notify author when image is approved</label>
                                    <br>
                                    <input type="checkbox" id="declinednotification" name="declinednotification" value="yes" <?php checked( 'yes', get_imagepress_option( 'declinednotification' ) ); ?>> <label for="declinednotification">Notify author when image is rejected</label>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>
                <p><input type="submit" name="isGSSubmit" value="Save Changes" class="button-primary"></p>
            </form>
            <?php
        } elseif ( $tab === 'label_tab' ) {
            if ( isset( $_POST['isGSSubmit'] ) ) {
                if ( ! isset( $_POST['imagepress_settings_nonce'] ) || ! check_admin_referer( 'save_imagepress_settings_action', 'imagepress_settings_nonce' ) ) {
                    wp_die( esc_html__( 'Nonce verification failed. Please try again.', 'image-gallery' ) );
                }

                $ip_updated_options = [
                    'ip_caption_label'        => isset( $_POST['ip_caption_label'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_caption_label'] ) ) : '',
                    'ip_category_label'       => isset( $_POST['ip_category_label'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_category_label'] ) ) : '',
                    'ip_description_label'    => isset( $_POST['ip_description_label'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_description_label'] ) ) : '',
                    'ip_upload_label'         => isset( $_POST['ip_upload_label'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_upload_label'] ) ) : '',
                    'ip_image_label'          => isset( $_POST['ip_image_label'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_image_label'] ) ) : '',
                    'ip_upload_success_title' => isset( $_POST['ip_upload_success_title'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_upload_success_title'] ) ) : '',
                    'ip_upload_success'       => isset( $_POST['ip_upload_success'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_upload_success'] ) ) : '',
                ];
                $ip_options         = get_option( 'imagepress' );
                $ip_update          = array_merge( $ip_options, $ip_updated_options );
                update_option( 'imagepress', $ip_update );

                echo '<div class="updated notice is-dismissible"><p>Settings updated successfully!</p></div>';
            }
            ?>
            <form method="post" action="">
                <?php wp_nonce_field( 'save_imagepress_settings_action', 'imagepress_settings_nonce' ); ?>

                <h2><?php esc_html_e( 'Label Settings', 'image-gallery' ); ?></h2>
                <p><?php esc_html_e( 'Configure, set or translate any of ImagePress labels. Leave a label blank to disable/hide it.', 'image-gallery' ); ?></p>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="ip_caption_label">Image caption label<br><small>Leave blank to disable</small></label></th>
                            <td>
                                <input type="text" name="ip_caption_label" id="ip_caption_label" value="<?php echo esc_html( get_imagepress_option( 'ip_caption_label' ) ); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ip_category_label">Image category label<br><small>(dropdown)</small></label></th>
                            <td>
                                <input type="text" name="ip_category_label" id="ip_category_label" value="<?php echo esc_html( get_imagepress_option( 'ip_category_label' ) ); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ip_description_label">Image description label<br><small>(textarea)<br>Leave blank to disable</small></label></th>
                            <td>
                                <input type="text" name="ip_description_label" id="ip_description_label" value="<?php echo esc_html( get_imagepress_option( 'ip_description_label' ) ); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ip_upload_label">Image upload button label<br><small>(button)</small></label></th>
                            <td>
                                <input type="text" name="ip_upload_label" id="ip_upload_label" value="<?php echo esc_html( get_imagepress_option( 'ip_upload_label' ) ); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ip_image_label">Image upload selection label<br><small>(link)</small></label></th>
                            <td>
                                <input type="text" name="ip_image_label" id="ip_image_label" value="<?php echo esc_html( get_imagepress_option( 'ip_image_label' ) ); ?>" class="regular-text">
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>
                <h2><?php esc_html_e( 'Image Upload', 'image-gallery' ); ?></h2>
                <p>This text will appear when the image upload is successful. Leave blank to disable.</p>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="ip_upload_success_title">Upload success title</label></th>
                            <td>
                                <input type="text" name="ip_upload_success_title" id="ip_upload_success_title" value="<?php echo esc_html( get_imagepress_option( 'ip_upload_success_title' ) ); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ip_upload_success">Upload success</label></th>
                            <td>
                                <input type="text" name="ip_upload_success" id="ip_upload_success" value="<?php echo esc_html( get_imagepress_option( 'ip_upload_success' ) ); ?>" class="regular-text">
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>
                <p><input type="submit" name="isGSSubmit" value="Save Changes" class="button-primary"></p>
            </form>
            <?php
        } elseif ( $tab === 'upload_tab' ) {
            if ( isset( $_POST['isGSSubmit'] ) ) {
                if ( ! isset( $_POST['imagepress_settings_nonce'] ) || ! check_admin_referer( 'save_imagepress_settings_action', 'imagepress_settings_nonce' ) ) {
                    wp_die( esc_html__( 'Nonce verification failed. Please try again.', 'image-gallery' ) );
                }

                $ip_updated_options = [
                    'ip_upload_tos'                  => isset( $_POST['ip_upload_tos'] ) ? intval( $_POST['ip_upload_tos'] ) : 0,
                    'ip_upload_tos_url'              => isset( $_POST['ip_upload_tos_url'] ) ? sanitize_url( wp_unslash( $_POST['ip_upload_tos_url'] ) ) : '',
                    'ip_upload_tos_error'            => isset( $_POST['ip_upload_tos_error'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_upload_tos_error'] ) ) : '',
                    'ip_upload_tos_content'          => isset( $_POST['ip_upload_tos_content'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_upload_tos_content'] ) ) : '',
                    'ip_upload_size'                 => isset( $_POST['ip_upload_size'] ) ? intval( $_POST['ip_upload_size'] ) : 0,
                    'ip_global_upload_limit'         => isset( $_POST['ip_global_upload_limit'] ) ? intval( $_POST['ip_global_upload_limit'] ) : 0,
                    'ip_global_upload_limit_message' => isset( $_POST['ip_global_upload_limit_message'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_global_upload_limit_message'] ) ) : '',
                    'ip_cat_exclude'                 => isset( $_POST['ip_cat_exclude'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_cat_exclude'] ) ) : '',
                    'ip_max_quality'                 => isset( $_POST['ip_max_quality'] ) ? intval( $_POST['ip_max_quality'] ) : 0,
                ];
                $ip_options         = get_option( 'imagepress' );
                $ip_update          = array_merge( $ip_options, $ip_updated_options );
                update_option( 'imagepress', $ip_update );

                if ( isset( $_POST['ip_quota_increase'] ) && intval( $_POST['ip_quota_increase'] ) > 0 ) {
                    $ip_users          = get_users();
                    $ip_quota_increase = intval( $_POST['ip_quota_increase'] );

                    foreach ( $ip_users as $user ) {
                        $quota = (int) get_the_author_meta( 'ip_upload_limit', $user->ID );

                        if ( isset( $_POST['ip_quota_action'] ) && sanitize_text_field( wp_unslash( $_POST['ip_quota_action'] ) ) === 'increase' ) {
                            update_user_meta( $user->ID, 'ip_upload_limit', $quota + $ip_quota_increase );
                        } elseif ( isset( $_POST['ip_quota_action'] ) && sanitize_text_field( wp_unslash( $_POST['ip_quota_action'] ) ) === 'decrease' ) {
                            update_user_meta( $user->ID, 'ip_upload_limit', $quota - $ip_quota_increase );
                        } elseif ( isset( $_POST['ip_quota_action'] ) && sanitize_text_field( wp_unslash( $_POST['ip_quota_action'] ) ) === 'set' ) {
                            update_user_meta( $user->ID, 'ip_upload_limit', $ip_quota_increase );
                        }
                    }
                }

                echo '<div class="updated notice is-dismissible"><p>Users quota increased successfully!</p></div>';
                echo '<div class="updated notice is-dismissible"><p>Settings updated successfully!</p></div>';
            }
            ?>
            <form method="post" action="">
                <?php wp_nonce_field( 'save_imagepress_settings_action', 'imagepress_settings_nonce' ); ?>

                <h2><?php esc_html_e( 'Upload Settings', 'image-gallery' ); ?></h2>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="ip_max_quality">Image quality</label></th>
                            <td>
                                <input name="ip_max_quality" id="ip_max_quality" type="number" value="<?php echo intval( get_imagepress_option( 'ip_max_quality' ) ); ?>" min="0" max="100">
                                <br><small>Set image quality when uploading image.</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ip_upload_size">Maximum image upload size<br><small>(in kilobytes)</small></label></th>
                            <td>
                                <input type="number" name="ip_upload_size" id="ip_upload_size" min="0" max="65536" step="1024" value="<?php echo intval( get_imagepress_option( 'ip_upload_size' ) ); ?>">
                                <br><small>Try 4096 for most configurations.</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ip_cat_exclude">Exclude categories</label></th>
                            <td>
                                <input type="text" name="ip_cat_exclude" id="ip_cat_exclude" value="<?php echo esc_attr( get_imagepress_option( 'ip_cat_exclude' ) ); ?>">
                                <br><small>Exclude these categories from the upload form (separate IDs with comma).</small>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label>Terms and conditions of use</label></th>
                            <td>
                                <select name="ip_upload_tos" id="ip_upload_tos">
                                    <option value="1" <?php selected( 1, intval( get_imagepress_option( 'ip_upload_tos' ) ) ); ?>>Enable terms and conditions</option>
                                    <option value="0" <?php selected( 0, intval( get_imagepress_option( 'ip_upload_tos' ) ) ); ?>>Disable terms and conditions</option>
                                </select> <label for="ip_upload_tos">Enable/disable terms and conditions of use</label>
                                <br>
                                <input type="text" name="ip_upload_tos_content" id="ip_upload_tos_content" class="regular-text" value="<?php echo esc_html( get_imagepress_option( 'ip_upload_tos_content' ) ); ?>" placeholder="I agree with the terms and conditions"> <label for="ip_upload_tos_content">Terms and conditions of use body</label>
                                <br>
                                <input type="text" name="ip_upload_tos_error" id="ip_upload_tos_error" class="regular-text" value="<?php echo esc_html( get_imagepress_option( 'ip_upload_tos_error' ) ); ?>" placeholder="Please indicate that you accept the terms and conditions of use"> <label for="ip_upload_tos_error">Terms and conditions of use error</label>
                                <br>
                                <input type="url" name="ip_upload_tos_url" id="ip_upload_tos_url" class="regular-text" value="<?php echo esc_url( get_imagepress_option( 'ip_upload_tos_url' ) ); ?>" placeholder="https://"> <label for="ip_upload_tos_url">Terms and conditions of use URL</label>
                                <br><small>Opens in new tab/window</small>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>
                <h2>Limits and Quotas</h2>
                <p>
                    Set global and per-user upload limits.<br>
                    Set individual limits for each user in their <a href="<?php echo esc_url( admin_url( 'users.php' ) ); ?>">profile editor</a>. Individual limits have higher priority than global limits.
                </p>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="ip_global_upload_limit">Maximum image upload limit</label></th>
                            <td>
                                <input type="number" name="ip_global_upload_limit" id="ip_global_upload_limit" min="0" max="999999" step="1" value="<?php echo intval( get_imagepress_option( 'ip_global_upload_limit' ) ); ?>"> <label for="ip_global_upload_limit">Image upload limit (global, if no other limits are specified)</label>
                                <hr>

                                <p>
                                    <select name="ip_quota_action">
                                        <option value="increase">Increase all users quota by</option>
                                        <option value="decrease">Decrease all users quota by</option>
                                        <option value="set">Set all users quota to</option>
                                    </select> <input name="ip_quota_increase" type="number" min="0" placeholder="0"> images
                                    <br><small>Note that setting a limit higher than the global limit will override it.</small>
                                </p>

                                <hr>
                                <textarea class="large-text" rows="4" id="ip_global_upload_limit_message" name="ip_global_upload_limit_message" placeholder="You have reached the maximum number of images allowed."><?php echo wp_kses_post( get_imagepress_option( 'ip_global_upload_limit_message' ) ); ?></textarea>
                                <br><small>Set a message when maximum number of images is reached.</small>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <hr>

                <p><input type="submit" name="isGSSubmit" value="Save Changes" class="button-primary"></p>
            </form>
            <?php
        }
        ?>
    </div>
    <?php
}
