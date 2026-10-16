<?php
/**
 * Registers built-in triggers/actions and boots available triggers.
 *
 * @package WPIntegrations\Cf7RdStation
 */

namespace WPIntegrations\Cf7RdStation\Providers;

use WPIntegrations\Cf7RdStation\Core\ServiceProvider;
use WPIntegrations\Cf7RdStation\Integrations\Actions\RdStation\RdStationAction;
use WPIntegrations\Cf7RdStation\Integrations\Registry;
use WPIntegrations\Cf7RdStation\Integrations\Triggers\ContactForm7\ContactForm7Trigger;

defined( 'ABSPATH' ) || exit;

final class IntegrationServiceProvider extends ServiceProvider {

	public function boot(): void {
		$registry = $this->app->make( Registry::class );

		$registry->add_trigger( $this->app->make( ContactForm7Trigger::class ) );
		$registry->add_action( $this->app->make( RdStationAction::class ) );

		/**
		 * Add-ons register additional triggers and actions here.
		 *
		 * @param Registry $registry The integration registry.
		 */
		do_action( 'wpi_cf7rds_register_integrations', $registry );

		add_action(
			'init',
			static function () use ( $registry ) {
				foreach ( $registry->triggers() as $trigger ) {
					if ( $trigger->available() ) {
						$trigger->register();
					}
				}
			}
		);
	}
}
