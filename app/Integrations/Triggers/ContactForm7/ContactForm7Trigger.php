<?php
/**
 * Contact Form 7 trigger.
 *
 * @package WPIntegrations\Cf7RdStation
 */

namespace WPIntegrations\Cf7RdStation\Integrations\Triggers\ContactForm7;

use WPIntegrations\Cf7RdStation\Integrations\FlowRunner;
use WPIntegrations\Cf7RdStation\Integrations\Trigger;

defined( 'ABSPATH' ) || exit;

final class ContactForm7Trigger extends Trigger {

	/** @var ContactForm7FormScanner */
	private $scanner;

	public function __construct( FlowRunner $runner, ContactForm7FormScanner $scanner ) {
		parent::__construct( $runner );
		$this->scanner = $scanner;
	}

	public function key(): string {
		return 'cf7';
	}

	public function label(): string {
		return __( 'Contact Form 7', 'wpintegrations-connector-for-cf7-rd-station' );
	}

	public function available(): bool {
		return defined( 'WPCF7_VERSION' );
	}

	public function sources( bool $refresh = false ): array {
		return $this->scanner->forms( $refresh );
	}

	public function register(): void {
		// Both fire only for a valid, non-spam, non-aborted submission. Hooking
		// the failure too means a site whose mail is broken still gets its lead.
		add_action( 'wpcf7_mail_sent', array( $this, 'on_submit' ) );
		add_action( 'wpcf7_mail_failed', array( $this, 'on_submit' ) );

		// Forms changed, so rediscover them on the next request.
		add_action( 'wpcf7_after_save', array( ContactForm7FormScanner::class, 'forget' ) );
		add_action( 'save_post', array( ContactForm7FormScanner::class, 'forget' ) );
		add_action( 'deleted_post', array( ContactForm7FormScanner::class, 'forget' ) );
	}

	/**
	 * @param object $contact_form \WPCF7_ContactForm.
	 */
	public function on_submit( $contact_form ): void {
		if ( ! is_object( $contact_form ) || ! method_exists( $contact_form, 'id' ) || ! class_exists( 'WPCF7_Submission' ) ) {
			return;
		}

		$submission = \WPCF7_Submission::get_instance();
		if ( ! $submission ) {
			return;
		}

		// The post id is canonical: unlike the title it is unique and survives
		// renames, and unlike the shortcode hash it exists on every CF7 version.
		$source = (string) $contact_form->id();

		$fields = array();
		foreach ( (array) $submission->get_posted_data() as $id => $value ) {
			$id = sanitize_text_field( (string) $id );
			// CF7's own hidden inputs are underscore-prefixed.
			if ( '' === $id || 0 === strpos( $id, '_' ) ) {
				continue;
			}
			$fields[ $id ] = is_array( $value )
				? array_map( 'sanitize_text_field', $value )
				: sanitize_text_field( (string) $value );
		}

		if ( ! $fields ) {
			return;
		}

		$this->remember_shape( $source, array_keys( $fields ), (string) $contact_form->title() );
		$this->dispatch( $source, $fields, $this->request_meta( $submission ) );
	}

	/**
	 * Metadata only available at submission time. RD Station's own tracking
	 * cookies preserve lead attribution like a native form; the time and user
	 * are captured now because delivery runs later.
	 *
	 * @param \WPCF7_Submission $submission The current submission.
	 */
	private function request_meta( $submission ): array {
		// CF7 records the page the form was on; the referer covers older builds.
		$page_url = (string) $submission->get_meta( 'url' );
		if ( '' === $page_url && wp_get_referer() ) {
			$page_url = (string) wp_get_referer();
		}

		$meta = array(
			'page_url'     => esc_url_raw( $page_url ),
			'submitted_at' => gmdate( 'Y-m-d H:i:s' ),
		);

		// CF7 submits over REST, where the user is resolved from its own meta.
		$user_id = (int) $submission->get_meta( 'current_user_id' );
		$user    = $user_id ? get_userdata( $user_id ) : wp_get_current_user();
		if ( $user && $user->exists() ) {
			$meta['user_email'] = sanitize_email( (string) $user->user_email );
			$meta['user_name']  = sanitize_text_field( (string) $user->display_name );
		}

		// RD Station's tracker keeps a JSON blob in `rdtrk`; the conversion API
		// wants only the visitor UUID inside it.
		$rdtrk = '';
		if ( isset( $_COOKIE['rdtrk'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only tracking cookie, no state change.
			$rdtrk = sanitize_text_field( wp_unslash( $_COOKIE['rdtrk'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		} elseif ( isset( $_COOKIE['_rdtrk'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$rdtrk = sanitize_text_field( wp_unslash( $_COOKIE['_rdtrk'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
		if ( preg_match( '/[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}/i', $rdtrk, $match ) ) {
			$meta['client_tracking_id'] = $match[0];
		}
		// PHP stores the `__trf.src` cookie under `__trf_src`.
		if ( isset( $_COOKIE['__trf_src'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$meta['traffic_source'] = sanitize_text_field( wp_unslash( $_COOKIE['__trf_src'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		return $meta;
	}
}
