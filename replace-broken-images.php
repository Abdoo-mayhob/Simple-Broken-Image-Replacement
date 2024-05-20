<?php
/**
 *
 * Plugin Name: Replace Borken Images
 * Plugin URI: https://github.com/Abdoo-mayhob/Estimated-Reading-Time
 * Description: Replace Broken Images on your front end with a set image using super lite JS (With No .htaccess Edits).
 * Version: 1.0.0
 * Author: Abdoo
 * Author URI: https://abdoo.me
 * License: GPL2
 * Text Domain: replace-broken-images
 * Domain Path: /languages
 *
 * ===================================================================
 * 
 * Copyright 2024  Abdullatif Al-Mayhob, Abdoo abdoo.mayhob@gmail.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * ===================================================================
 * 
 * TODO:
 * - Add Customization filters and hooks
 */

 // If this file is called directly, abort.
defined('ABSPATH') or die;

// Load Translation Files
add_action('admin_init', function() {
    load_plugin_textdomain('replace-broken-images', false, dirname(plugin_basename(__FILE__)) . '/languages');
});


add_action('init', function(){
    ReplaceBorkenImages::I();
});


/**
 * Main Class.
 */
class ReplaceBorkenImages {

    public const SETTINGS_NAME = 'rbi_plugin';
    public const PLUGIN_VERSION = '1.0.0';
    public $PLUGIN_URI;

    // Plugin Settings and Default Values (Used when options not set yet)
    public $settings = [];
    public const SETTINGS_DEFAULT = [
        'img_url' => 'http://meissa.test/wp-content/uploads/Logo_White.png',
    ];

    // Refers to a single instance of this class
	private static $instance = null;

    /**
	 * Creates or returns a single instance of this class
	 *
	 * @return ReplaceBorkenImages a single instance of this class.
	 */
    public static function I() {
        self::$instance = self::$instance ?? new self();
        return self::$instance;
    }

    public function __construct() {
        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        
        // Load Plugin Settings 
        $this->settings = get_option(self::SETTINGS_NAME, self::SETTINGS_DEFAULT);
        $this->PLUGIN_URI = plugin_dir_url(__FILE__);
    }

    // --------------------------------------------------------------------------------------
    // Enqueue Scripts
    public function enqueue_scripts() {
        wp_enqueue_script('replace-broken-images',  $this->PLUGIN_URI . 'rbi.js', [], self::PLUGIN_VERSION);
    }
    // --------------------------------------------------------------------------------------
    // Admin Menu
    public function admin_menu() {
        add_options_page(
            __('Simple Broken Image Replacement', 'replace-broken-images'), 
            __('Broken Image Replacement', 'replace-broken-images'),
            'manage_options', 'replace-broken-images', [$this, 'view_admin']);
    }

    public function view_admin() {
        require_once __DIR__ . '/admin.php';
    }

    // --------------------------------------------------------------------------------------
    // Helpers
    public function test_url($url) {
        $headers = get_headers($url, 1);
    
        if(!$headers) {
            echo "Unable to fetch headers for the URL: $url";
            return;
        }
    
        // Check the HTTP response status
        if(strpos($headers[0], '200') !== false) {
            echo "The URL responded with a 200 status code.\n";
        } else {
            echo "The URL did not respond with a 200 status code.\n";
        }
    
        // Check if the URL is an image file
        $content_type = $headers['Content-Type'];
        $image_types = ['image/jpeg', 'image/png', 'image/svg+xml', 'image/webp'];
    
        if(in_array($content_type, $image_types)) {
            echo "The URL points to an image file.\n";
        } else {
            echo "The URL does not point to an image file.\n";
        }
    
        // Echo the file size in kB if it's an image
        if(in_array($content_type, $image_types)) {
            $file_size = filesize($url) / 1024; // convert from bytes to kilobytes
            echo "The image file size is $file_size kB.\n";
        }
    }
    

}
