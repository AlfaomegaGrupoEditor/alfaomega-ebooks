<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Alfaomega_Ebooks
 * @subpackage Alfaomega_Ebooks/includes
 * @author     Livan Rodriguez <livan2r@gmail.com>
 */
class Alfaomega_Ebooks_Deactivator {

	/**
	 * Deactivates the plugin.
	 *
	 * This method is called during the plugin's deactivation. It flushes the rewrite rules and unregisters the post type.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate(): void
    {
        // Flushes the rewrite rules
        flush_rewrite_rules();

        // Unregisters the post type
        unregister_post_type( ALFAOMEGA_EBOOKS_POST_TYPE );
	}

}
