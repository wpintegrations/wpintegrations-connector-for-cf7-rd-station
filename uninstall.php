<?php
/**
 * Removes tables, options and cron events when the plugin is deleted.
 *
 * @package WPIntegrations\Cf7RdStation
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Both editions share this data. Bail on an installed Pro copy, not just an
 * active one: a bulk delete can remove this plugin while Pro is momentarily
 * deactivated, taking Pro's tokens and integrations with it.
 */
$wpi_cf7rds_pro = 'wpintegrations-connector-for-cf7-rd-station-pro/wpintegrations-connector-for-cf7-rd-station-pro.php';

$wpi_cf7rds_active = (array) get_option( 'active_plugins', array() );
$wpi_cf7rds_shared = in_array( $wpi_cf7rds_pro, $wpi_cf7rds_active, true )
	|| ( is_multisite() && array_key_exists( $wpi_cf7rds_pro, (array) get_site_option( 'active_sitewide_plugins', array() ) ) )
	|| file_exists( WP_PLUGIN_DIR . '/' . $wpi_cf7rds_pro );

if ( $wpi_cf7rds_shared ) {
	return;
}

global $wpdb;

// Plugin tables. Names are constants, not user input.
foreach ( array( 'wpi_cf7rds_flows', 'wpi_cf7rds_logs', 'wpi_cf7rds_jobs' ) as $wpi_cf7rds_table ) {
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.NoCaching -- uninstall cleanup of plugin-owned tables.
	$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $wpdb->prefix . $wpi_cf7rds_table ) );
}

// Options.
delete_option( 'wpi_cf7rds_settings' );
delete_option( 'wpi_cf7rds_version' );
delete_option( 'wpi_cf7rds_migrations' );
delete_option( 'wpi_cf7rds_recent_shapes' );

// Transients: the form-scan cache, plus RD Station field lists swept by prefix.
delete_transient( 'wpi_cf7rds_cf7_forms' );
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- uninstall cleanup.
$wpdb->query(
	"DELETE FROM {$wpdb->options}
	 WHERE option_name LIKE '\_transient\_wpi\_cf7rds\_rd\_fields\_%'
	    OR option_name LIKE '\_transient\_timeout\_wpi\_cf7rds\_rd\_fields\_%'"
);

// Cron events.
wp_clear_scheduled_hook( 'wpi_cf7rds_queue_tick' );
wp_clear_scheduled_hook( 'wpi_cf7rds_daily_purge' );
