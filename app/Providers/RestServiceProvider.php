<?php
/**
 * Registers REST routes from routes/api.php.
 *
 * @package WPIntegrations\Cf7RdStation
 */

namespace WPIntegrations\Cf7RdStation\Providers;

use WPIntegrations\Cf7RdStation\Core\ServiceProvider;
use WPIntegrations\Cf7RdStation\Http\Router;

defined( 'ABSPATH' ) || exit;

final class RestServiceProvider extends ServiceProvider {

	public function boot(): void {
		add_action(
			'rest_api_init',
			function () {
				$routes = require WPI_CF7RDS_PATH . 'routes/api.php';
				$routes( new Router( $this->app ) );
			}
		);
	}
}
