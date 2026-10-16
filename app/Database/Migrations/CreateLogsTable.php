<?php
/**
 * Delivery log: one row per attempted action execution.
 *
 * @package WPIntegrations\Cf7RdStation
 */

namespace WPIntegrations\Cf7RdStation\Database\Migrations;

use WPIntegrations\Cf7RdStation\Database\Migration;

defined( 'ABSPATH' ) || exit;

final class CreateLogsTable extends Migration {

	public function up(): void {
		$this->create_table(
			'wpi_cf7rds_logs',
			'id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			flow_id bigint(20) unsigned NOT NULL,
			job_id bigint(20) unsigned NULL,
			level varchar(20) NOT NULL DEFAULT "info",
			message varchar(500) NOT NULL DEFAULT "",
			request longtext NULL,
			response longtext NULL,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY flow_created (flow_id,created_at),
			KEY level (level)'
		);
	}
}
