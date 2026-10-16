<?php
/**
 * Application configuration.
 *
 * @package WPIntegrations\Cf7RdStation
 */

defined( 'ABSPATH' ) || exit;

return array(
	'providers' => array(
		WPIntegrations\Cf7RdStation\Providers\AppServiceProvider::class,
		WPIntegrations\Cf7RdStation\Providers\DatabaseServiceProvider::class,
		WPIntegrations\Cf7RdStation\Providers\IntegrationServiceProvider::class,
		WPIntegrations\Cf7RdStation\Providers\RetentionServiceProvider::class,
		WPIntegrations\Cf7RdStation\Providers\RestServiceProvider::class,
		WPIntegrations\Cf7RdStation\Providers\AdminServiceProvider::class,
	),
);
