<?php

namespace makeandship\mediatextextractor;

use makeandship\logging\Log;
use makeandship\mediatextextractor\admin\UserInterfaceManager;
use makeandship\mediatextextractor\settings\SettingsManager;
use makeandship\mediatextextractor\Util;

class MediaTextExtractorManager
{
    public function __construct()
    {
        Log::debug('MediaTextExtractorManager#__construct: enter');

        $this->ui = new UserInterfaceManager(Constants::VERSION, Constants::DB_VERSION, $this);

        $this->initialise_plugin_hooks();

        Log::debug('MediaTextExtractorManager#__construct: exit');
    }

    public function initialise_plugin_hooks()
    {
        Log::debug('MediaTextExtractorManager#initialise_plugin_hooks: enter');

        // wordpress initialisation
        add_action('admin_init', array($this, 'initialise'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_menu', array($this, 'initialise_menu'));

        // posts
        add_action('add_attachment', array(&$this, 'add_attachment'), 10, 1); // after acf fields save - 15, acf_elasticsearch - 20
        add_action('enable-media-replace-upload-done', array(&$this, 'media_replace'), 10, 3); // after acf fields save - 15, acf_elasticsearch - 20

        // plugin
        add_action('wp_ajax_analyse_media', array(&$this, 'analyse_media'));
        add_action('wp_ajax_resume_analysing_media', array(&$this, 'resume_analysing_media'));
        add_action('wp_ajax_analyse_individual_media', array(&$this, 'analyse_individual_media'));

        Log::debug('MediaTextExtractorManager#initialise_plugin_hooks: exit');
    }

    /**
     * ---------------------
     * Business Logic
     * ---------------------
     */
    public function noop()
    {
        Log::debug('MediaTextExtractorManager#noop: enter');
        Log::debug('MediaTextExtractorManager#noop: enter');
    }

    /**
     * ---------------------
     * Plugin Initialisation
     * ---------------------
     */
    public function initialise()
    {
        Log::debug('MediaTextExtractorManager#initialise: enter');

        //$this->ui->initialise_settings();

        Log::debug('MediaTextExtractorManager#initialise: exit');
    }

    public function initialise_settings()
    {
        Log::debug('MediaTextExtractorManager#initialise_settings: enter');

        $this->can_extract = SettingsManager::get_instance()->is_extraction_supported();

        Log::debug('MediaTextExtractorManager#initialise_settings: exit');
    }

    public function initialise_menu()
    {
        Log::debug('MediaTextExtractorManager#initialise_menu: enter');

        // show a menu
        $this->ui->initialise_menu();

        Log::debug('MediaTextExtractorManager#initialise_menu: exit');

    }

    public function admin_enqueue_scripts()
    {
        Log::debug('MediaTextExtractorManager#admin_enqueue_scripts: enter');

        // add custom css and js
        $this->ui->enqueue_scripts();

        Log::debug('MediaTextExtractorManager#admin_enqueue_scripts: exit');
    }

    public function activate()
    {
        Log::debug('MediaTextExtractorManager#activate: enter');
        $this->initialise_settings();
        Log::debug('MediaTextExtractorManager#activate: exit');
    }

    public function analyse_media()
    {
        Log::debug('MediaTextExtractorManager#analyse_media: enter');

        $fresh = isset($_POST['fresh']) ? ($_POST['fresh'] === 'true') : false;

        $manager = new ExtractionManager();

        $status = $manager->extract($fresh);

        $response = array(
            'message' => 'Analysis complete',
            'status'  => $status,
        );

        $json = json_encode($response);
        die($json);

        Log::debug('MediaTextExtractorManager#analyse_media: exit');
    }

    public function resume_analysing_media()
    {
        Log::debug('MediaTextExtractorManager#resume_analysing_media: enter');

        $response = array(
            'message' => 'Analysis complete',
            'status'  => $status,
        );

        $json = json_encode($response);
        die($json);

        Log::debug('MediaTextExtractorManager#resume_analysing_media: exit');
    }

    public function analyse_individual_media()
    {
        Log::debug('MediaTextExtractorManager#analyse_individual_media: enter');

        $id = Util::safely_get_attribute($_POST, 'id');
        if ($id) {
            $manager = new ExtractionManager();
            $status  = $manager->extract_individual($id);

            $response = array(
                'message' => 'Individual analysis complete',
                'status'  => $status,
            );

            $json = json_encode($response);
            die($json);
        }

        Log::debug('MediaTextExtractorManager#analyse_individual_media: exit');
    }

    public function deactivate()
    {
        Log::debug('MediaTextExtractorManager#deactivate: enter');
        Log::debug('MediaTextExtractorManager#deactivate: exit');
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
        Log::debug('MediaTextExtractorManager#add_attachment: enter');
        Log::debug('MediaTextExtractorManager#add_attachment: post_id: ' . $post_id);

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
        Log::debug('MediaTextExtractorManager#add_attachment: exit');
    }

    /**
     *
     */
    public function media_replace($target_url, $source_url, $post_id)
    {
        Log::debug('MediaTextExtractorManager#media_replace: enter');
        Log::debug('MediaTextExtractorManager#media_replace: post_id: ' . $post_id);

        $this->add_attachment($post_id);
    }
}
