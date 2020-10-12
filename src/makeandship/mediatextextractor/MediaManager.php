<?php

namespace makeandship\mediatextextractor;

class MediaManager
{

    public function __construct()
    {
    }

    public function initialise_status()
    {
        Util::debug('MediaManager#initialise_status', 'enter');

        $total  = $this->get_files_count(null);
        $status = array(
            'page'      => 1,
            'count'     => 0,
            'total'     => $total,
            'completed' => false,
        );

        Util::debug('MediaManager#initialise_status', 'exit');

        return $status;
    }

    public function get_files_count($blog_id = null)
    {
        Util::debug('MediaManager#get_files_count', 'enter');

        $count = 0;

        if (isset($blog_id)) {
            // target site
            switch_to_blog($blog_id);

            $args  = $this->get_count_file_args();
            $count = intval((new \WP_Query($args))->found_posts);

            // back to the original
            restore_current_blog();
        } else {
            $args  = $this->get_count_file_args();
            $count = intval((new \WP_Query($args))->found_posts);
            Util::debug('MediaManager#get_files_count count: ', $count);
        }

        Util::debug('MediaManager#get_files_count', 'exit');

        return $count;
    }

    public function get_files($blog_id, $page, $per)
    {
        Util::debug('MediaManager#get_files', 'enter');

        if (isset($blog_id)) {
            switch_to_blog($blog_id);
        }

        $args  = $this->get_paginated_file_args($page, $per);
        $posts = get_posts($args);

        // turn posts into files
        $files = array();
        foreach ($posts as $post) {
            if (!is_array($post)) {
                $file = $post->to_array();
            } else {
                $file = $post;
            }
            $id = Util::safely_get_attribute($file, 'ID');
            if ($id) {
                $filepath         = get_attached_file($id);
                $file['filepath'] = $filepath;
            }
            $files[] = $file;
        }

        if (isset($blog_id)) {
            restore_current_blog();
        }

        Util::debug('MediaManager#get_files', 'enter');

        return $files;
    }

    private function get_count_file_args()
    {
        Util::debug('MediaManager#get_count_file_args', 'enter');

        $args = array(
            'post_type'   => 'attachment',
            'post_status' => 'inherit',
            'fields'      => 'count',
        );

        Util::debug('MediaManager#get_count_file_args', 'exit');

        return $args;
    }

    private function get_paginated_file_args($page, $per)
    {
        Util::debug('MediaManager#get_paginated_file_args', 'enter');

        $args = array(
            'post_type'      => 'attachment',
            'posts_per_page' => $per,
            'paged'          => $page,
            'orderby'        => array(
                'post_date' => 'DESC',
            ),
        );

        Util::debug('MediaManager#get_paginated_file_args', 'exit');

        return $args;
    }
}