<?php
/**
 * Plugin Name: Easy Digital Downloads Categories Display
 * Description: Displays Easy Digital Downloads categories with images in a grid layout using a shortcode.
 * Version: 1.0
 * Author: Pushkar singh
 * License: GPL2+
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class EDDEasyCategoryDisplay {

    private $meta_key = 'download_term_image';

    public function __construct() {
        add_action('init', array($this, 'register_edd_categories_shortcode'));
        add_action('wp_head', array($this, 'edd_categories_styles'));
    }

    public function display_edd_categories() {
        // Get all EDD Download Categories
        $terms = get_terms(array(
            'taxonomy' => 'download_category',
            'hide_empty' => false,
        ));
        
        // Check if there are any categories
        if (!empty($terms) && !is_wp_error($terms)) {
            $output = '<div class="edd-categories-grid">';
            
            // Loop through each category and output its name, image, and link
            foreach ($terms as $term) {
                $term_link = get_term_link($term);
                $image_id = get_term_meta($term->term_id, $this->meta_key, true);
                $image_url = wp_get_attachment_url($image_id);

                // If no image URL was found, use a placeholder image
                if (empty($image_url)) {
                    $image_url = 'https://via.placeholder.com/150';
                }

                $output .= '<div class="edd-category-box">';
                $output .= '<a href="' . esc_url($term_link) . '">';
                $output .= '<div class="edd-category-image" style="background-image: url(' . esc_url($image_url) . ');"></div>';
                $output .= '<h3>' . esc_html($term->name) . '</h3>';
                $output .= '</a>';
                $output .= '</div>';
            }
            
            $output .= '</div>';
        } else {
            $output = '<p>No categories found.</p>';
        }
        
        return $output;
    }

    // Register the shortcode
    public function register_edd_categories_shortcode() {
        add_shortcode('edd_categories', array($this, 'display_edd_categories'));
    }

    // Add custom styles for the category display
    public function edd_categories_styles() {
        echo '
        <style>
        .edd-categories-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .edd-category-box {
            width: calc(33.333% - 20px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            text-align: center;
            transition: transform 0.3s;
        }
        .edd-category-box:hover {
            transform: translateY(-10px);
        }
        .edd-category-box a {
            color: inherit;
            text-decoration: none;
        }
        .edd-category-image {
            width: 100%;
            padding-bottom: 100%;
            background-size: cover;
            background-position: center;
        }
        .edd-category-box h3 {
            margin: 0;
            padding: 15px;
            background: #f9f9f9;
            font-size: 1.2em;
        }
        </style>
        ';
    }
}

new EDDEasyCategoryDisplay();
