<?php
/*
Plugin Name: Custom Fields Display
Description: Allows users to add and display custom fields on the front end of WordPress posts.
Version: 1.0
Author: Tigran Simonyan
*/

// Enqueue scripts and styles
function custom_fields_display_enqueue_scripts() {
    wp_enqueue_style('custom-fields-display-style', plugins_url('css/style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'custom_fields_display_enqueue_scripts');

// Enqueue admin styles
function custom_fields_display_enqueue_admin_styles() {
    wp_enqueue_style('custom-fields-display-admin-style', plugins_url('css/admin-style.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'custom_fields_display_enqueue_admin_styles');


// Add custom fields to post editor
function custom_fields_display_add_custom_fields() {
    add_meta_box('custom_text_field', 'Custom Text Field', 'custom_text_field_callback', 'post', 'normal', 'default');
    add_meta_box('custom_date_field', 'Custom Date Field', 'custom_date_field_callback', 'post', 'normal', 'default');
    add_meta_box('custom_image_field', 'Custom Image Field', 'custom_image_field_callback', 'post', 'normal', 'default');
}
add_action('add_meta_boxes', 'custom_fields_display_add_custom_fields');

// Custom text field callback
function custom_text_field_callback($post) {
    $value = get_post_meta($post->ID, 'custom_text', true);
    echo '<label for="custom_text" class="custom-fields-label">Custom Text:</label>';
    echo '<input type="text" class="custom-fields-input" id="custom_text" name="custom_text" value="' . esc_attr($value) . '" />';
}

// Custom date field callback
function custom_date_field_callback($post) {
    $value = get_post_meta($post->ID, 'custom_date', true);
    echo '<label for="custom_date" class="custom-fields-label">Custom Date:</label>';
    echo '<input type="date" class="custom-fields-input" id="custom_date" name="custom_date" value="' . esc_attr($value) . '" />';
}

// Custom image field callback
function custom_image_field_callback($post) {
    $value = get_post_meta($post->ID, 'custom_image', true);
    echo '<label for="custom_image" class="custom-fields-label">Custom Image:</label>';
    echo '<input type="text" class="custom-fields-input" id="custom_image" name="custom_image" value="' . esc_attr($value) . '" />';
    echo '<button class="upload_image_button button">Upload Image</button>';
}

// Enqueue media uploader script
function custom_fields_display_enqueue_media_uploader() {
    if (is_admin()) {
        wp_enqueue_media();
        wp_enqueue_script('custom-fields-display-media-uploader', plugins_url('js/media-uploader.js', __FILE__), array('jquery'), null, true);
    }
}
add_action('admin_enqueue_scripts', 'custom_fields_display_enqueue_media_uploader');

// Save custom fields data
function custom_fields_display_save_custom_fields($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = ['custom_text', 'custom_date', 'custom_image'];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    // Handle uploaded image
    if (isset($_POST['custom_image'])) {
        update_post_meta($post_id, 'custom_image', $_POST['custom_image']);
    }
}
add_action('save_post', 'custom_fields_display_save_custom_fields');

// Display custom fields on the front end
function display_custom_fields($content) {
    global $post;

    $custom_text = get_post_meta($post->ID, 'custom_text', true);
    $custom_date = get_post_meta($post->ID, 'custom_date', true);
    $custom_image = get_post_meta($post->ID, 'custom_image', true);

    $output = '<div class="custom-fields">';
    $output .= '<p>Custom Text: ' . $custom_text . '</p>';
    $output .= '<p>Custom Date: ' . $custom_date . '</p>';
    $output .= '<img src="' . $custom_image . '" alt="Custom Image">';
    $output .= '</div>';

    return $content . $output;
}
add_filter('the_content', 'display_custom_fields');

?>
