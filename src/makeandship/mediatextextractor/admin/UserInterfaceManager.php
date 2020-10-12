<?php

namespace makeandship\mediatextextractor\admin;

use makeandship\mediatextextractor\Constants;

class UserInterfaceManager
{
    const MENU_SPECIFICATION = array(
        'page_icon'     => 'icon-themes',
        'page_title'    => 'Media Text Extractor',
        'menu_title'    => 'Media Text Extractor',
        'menu_icon'     => 'dashicons-search',
        'page_slug'     => 'media-text-extractor',
        'page_cap'      => 'manage_options',
        'page_type'     => 'menu',
        'page_parent'   => '',
        'page_position' => 100,
    );

    function __construct($version, $db_version, $plugin)
    {
        $this->version    = $version;
        $this->db_version = $db_version;
        $this->plugin     = $plugin;
    }

    function initialise_options()
    {
        add_option(Constants::VERSION, $this->version);
        add_option(Constants::DB_VERSION, $this->db_version);
    }

    function initialise_settings()
    {

    }

    function initialise_menu()
    {
        $this->menu = add_options_page(
            UserInterfaceManager::MENU_SPECIFICATION['page_title'],
            UserInterfaceManager::MENU_SPECIFICATION['menu_title'],
            UserInterfaceManager::MENU_SPECIFICATION['page_cap'],
            UserInterfaceManager::MENU_SPECIFICATION['page_slug'],
            array(&$this, 'render_settings_page')
        );
    }

    function render_settings_page()
    {
        include 'Settings.php';
    }

    function render_field($type, $name, $attributes)
    {
        $html = null;
        switch ($type) {
            case 'text':
                $html = [
                    '<input type="text" name="' . $name . '"',
                ];

                foreach ($attributes as $attr_name => $attr_value) {
                    $html[] = ' ' . $attr_name . '="' . $attr_value . '"';
                }

                $html[] = '>';

                $html = implode($html);
                break;
        }

        return $html;
    }

    function enqueue_scripts()
    {
        $scripts = plugins_url() . '/plugin-media-text-extractor/' . 'js/main.js';
        $styles  = plugins_url() . '/plugin-media-text-extractor/' . 'css/style.css';

        wp_register_style('media-text-extractor', $styles, null, '0.0.1');
        wp_enqueue_style('media-text-extractor');
        wp_register_script('media-text-extractor', $scripts, array('jquery'), '0.0.1a');
        wp_enqueue_script('media-text-extractor');

        wp_localize_script('media-text-extractor', 'mediaTextExtractorManager', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
        ));
    }
}