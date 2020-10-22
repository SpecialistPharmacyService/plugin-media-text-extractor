<?php

namespace makeandship\mediatextextractor;

use makeandship\mediatextextractor\admin\UserInterfaceManager;
use makeandship\mediatextextractor\settings\SettingsManager;
use makeandship\mediatextextractor\Util;

class MediaTextExtractorManager
{
    public function __construct()
    {
        Util::debug('MediaTextExtractorManager#__construct', 'enter');

        $this->ui = new UserInterfaceManager(Constants::VERSION, Constants::DB_VERSION, $this);

        $this->initialise_plugin_hooks();

        Util::debug('MediaTextExtractorManager#__construct', 'exit');
    }

    public function initialise_plugin_hooks()
    {
        Util::debug('MediaTextExtractorManager#initialise_plugin_hooks', 'enter');

        // wordpress initialisation
        add_action('admin_init', array($this, 'initialise'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_menu', array($this, 'initialise_menu'));

        // posts
        add_action('add_attachment', array(&$this, 'add_attachment'), 10, 1); // after acf fields save - 15

        // plugin
        add_action('wp_ajax_analyse_media', array(&$this, 'analyse_media'));
        add_action('wp_ajax_resume_analysing_media', array(&$this, 'resume_analysing_media'));

        Util::debug('MediaTextExtractorManager#initialise_plugin_hooks', 'exit');
    }

    /**
     * ---------------------
     * Business Logic
     * ---------------------
     */
    public function noop()
    {
        Util::debug('MediaTextExtractorManager#noop', 'enter');
        Util::debug('MediaTextExtractorManager#noop', 'enter');
    }

    /**
     * ---------------------
     * Plugin Initialisation
     * ---------------------
     */
    public function initialise()
    {
        Util::debug('MediaTextExtractorManager#initialise', 'enter');

        //$this->ui->initialise_settings();

        Util::debug('MediaTextExtractorManager#initialise', 'exit');
    }

    public function initialise_settings()
    {
        Util::debug('MediaTextExtractorManager#initialise_settings', 'enter');

        $this->can_extract = SettingsManager::get_instance()->is_extraction_supported();

        Util::debug('MediaTextExtractorManager#initialise_settings', 'exit');
    }

    public function initialise_menu()
    {
        Util::debug('MediaTextExtractorManager#initialise_menu', 'enter');

        // show a menu
        $this->ui->initialise_menu();

        Util::debug('MediaTextExtractorManager#initialise_menu', 'exit');

    }

    public function admin_enqueue_scripts()
    {
        Util::debug('MediaTextExtractorManager#admin_enqueue_scripts', 'enter');

        // add custom css and js
        $this->ui->enqueue_scripts();

        Util::debug('MediaTextExtractorManager#admin_enqueue_scripts', 'exit');
    }

    public function activate()
    {
        Util::debug('MediaTextExtractorManager#activate', 'enter');
        $this->initialise_settings();
        Util::debug('MediaTextExtractorManager#activate', 'exit');
    }

    public function analyse_media()
    {
        Util::debug('MediaTextExtractorManager#analyse_media', 'enter');

        $fresh = isset($_POST['fresh']) ? ($_POST['fresh'] === 'true') : false;

        $manager = new ExtractionManager();

        $status = $manager->extract($fresh);

        $response = array(
            'message' => 'Analysis complete',
            'status'  => $status,
        );

        $json = json_encode($response);
        die($json);

        Util::debug('MediaTextExtractorManager#analyse_media', 'exit');
    }

    public function resume_analysing_media()
    {
        Util::debug('MediaTextExtractorManager#resume_analysing_media', 'enter');

        $response = array(
            'message' => 'Analysis complete',
            'status'  => $status,
        );

        $json = json_encode($response);
        die($json);

        Util::debug('MediaTextExtractorManager#resume_analysing_media', 'exit');
    }

    public function deactivate()
    {
        Util::debug('MediaTextExtractorManager#deactivate', 'enter');
        Util::debug('MediaTextExtractorManager#deactivate', 'exit');
    }

    /**
     * -------------------
     * Extraction Lifecycle
     * -------------------
     */

    /**
     *
     */
    public function add_attachment($post_id)
    {
        // get the post to index
        if (is_object($post_id)) {
            $post = $post_id;
        } else {
            $post = get_post($post_id, ARRAY_A);

            $id = Util::safely_get_attribute($post, 'ID');
            if ($id) {
                $filepath         = get_attached_file($id);
                $post['filepath'] = $filepath;
            }
        }

        // can't index empty posts
        if ($post == null) {
            return;
        }

        $post_type = Util::safely_get_attribute($post, 'post_type');
        if (!$post_type === 'attachment') {
            return;
        }

        // extract
        $manager = new ExtractionManager();
        $manager->extract_text_from_file($post);

    }
}