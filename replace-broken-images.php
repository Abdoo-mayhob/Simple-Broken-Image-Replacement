<?php

/**
 *
 * Plugin Name: Replace Broken Images
 * Plugin URI: https://github.com/Abdoo-mayhob/Estimated-Reading-Time
 * Description: Replace Broken Images on your front end with a set image using super lite JS (With No .htaccess Edits).
 * Version: 2.0.0
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
add_action('admin_init', function () {
    load_plugin_textdomain('replace-broken-images', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

// Initialize the plugin
add_action('init', function () {
    ReplaceBrokenImages::I();
});

/**
 * Main Class.
 */
class ReplaceBrokenImages
{

    public const SETTINGS_NAME = 'rbi_plugin';
    public const PLUGIN_VERSION = '2.0.0';
    public $PLUGIN_URI = '';
    private static $instance = null;

    // Plugin Settings and Default Values (Used when options not set yet)
    public $settings = [];
    public const SETTINGS_DEFAULT = [
        'img_url' => 'http://meissa.test/wp-content/uploads/Logo_White.png',
    ];

    /**
     * Creates or returns a single instance of this class
     *
     * @return ReplaceBorkenImages a single instance of this class.
     */
    public static function I()
    {
        self::$instance = self::$instance ?? new self();
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct()
    {
        add_action('admin_menu', [$this, 'adminMenu']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
        $this->settings = get_option(self::SETTINGS_NAME, self::SETTINGS_DEFAULT);
        $this->PLUGIN_URI = plugin_dir_url(__FILE__);
    }

    /**
     * Enqueue Scripts
     */

     public function enqueueAdminScripts($hook) {
        if ($hook !== 'settings_page_replace-broken-images') {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_script('replace-broken-images-admin', $this->PLUGIN_URI . 'replace-broken-images-admin.js', ['jquery'], self::PLUGIN_VERSION, true);
    }

    public function enqueueScripts()
    {
        wp_enqueue_script('replace-broken-images', $this->PLUGIN_URI . 'rbi.js', [], self::PLUGIN_VERSION, true);

        // Localize the script with the img_url setting
        wp_localize_script('replace-broken-images', 'my_script_data', [
            'img_url' => $this->settings['img_url']
        ]);
    }

    /**
     * Admin Menu
     */
    public function adminMenu()
    {
        add_options_page(
            __('Simple Broken Image Replacement', 'replace-broken-images'),
            __('Broken Image Replacement', 'replace-broken-images'),
            'manage_options',
            'replace-broken-images',
            [$this, 'viewAdmin']
        );
    }

    /**
     * View Admin
     */
    public function viewAdmin()
    {
        require_once __DIR__ . '/admin.php';
    }

    /**
     * Test URL
     *
     * @param string $url
     */
    public function testURL($url)
    {
        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            echo esc_html("Unable to fetch URL: " . $response->get_error_message());
            return;
        }

        $http_code = wp_remote_retrieve_response_code($response);
        echo esc_html($http_code == 200 ? "The URL responded with a 200 status code." : "The URL did not respond with a 200 status code. Response code: $http_code");

        $content_type = wp_remote_retrieve_header($response, 'content-type');
        $image_types = ['image/jpeg', 'image/png', 'image/svg+xml', 'image/webp'];

        echo esc_html(in_array($content_type, $image_types) ? "The URL points to an image file." : "The URL does not point to an image file.");

        $content_length = wp_remote_retrieve_header($response, 'content-length');
        if ($content_length) {
            $file_size = $content_length / 1024; // Convert from bytes to kilobytes
            echo esc_html("The image file size is $file_size kB.");
        }
    }
}
