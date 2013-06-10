<?php
/**
 * Option management for the plugin
 */
class WPPD_Option {
    /**
     * Returns a list of post types for which the plugin will do something
     * Default: returns the list of all registered post types
     */
    public static function get_post_types() {
        // @TODO: get the list of post types saved in admin
        return get_post_types();
    }
}