<?php
/**
 * Plugin Name: Studio Divi variables lock fix
 * Description: WP Studio's SQLite driver answers GET_LOCK() with '1=1', so Divi 5 treats every global variables save as locked. Local Studio site only; delete this file to undo.
 */
if ( defined( 'DB_ENGINE' ) && 'sqlite' === DB_ENGINE ) {
	add_filter( 'et_builder_5_global_variables_got_lock', static fn() => '1' );
}
