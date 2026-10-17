<?php
/**
 * Plugin Name: Clinic Leads Lab
 * Description: A consultation form used to practice GitHub Flow and CI.
 * Version: 0.1.0
 * Requires at least: 6.5
 * Requires PHP: 8.2
 * License: GPL-2.0-or-later
 * Text Domain: clinic-leads-lab
 */

namespace ClinicLeadsLab;

defined( 'ABSPATH' ) || exit;

final class Plugin {

	public function register(): void {
		add_shortcode( 'clinic_leads', array( $this, 'render' ) );
	}

	public function render(): string {
		$errors  = array();
		$success = false;
		$name    = '';
		$phone   = '';

		if ( 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) && '1' === $this->post_value( 'cll_submit' ) ) {
			$name  = $this->post_value( 'cll_name' );
			$phone = $this->post_value( 'cll_phone' );

			if ( ! wp_verify_nonce( $this->post_value( 'cll_nonce' ), 'cll_consultation' ) ) {
				$errors[] = __( 'The form expired. Reload the page and try again.', 'clinic-leads-lab' );
			} else {
				$name_length = preg_match_all( '/./us', $name );
				if ( '' === $name || false === $name_length || $name_length > 80 ) {
					$errors[] = __( 'Enter your name (up to 80 characters).', 'clinic-leads-lab' );
				}

				if ( '' === $phone || strlen( $phone ) > 128 ) {
					$errors[] = __( 'Enter your mobile number.', 'clinic-leads-lab' );
				}

				$success = array() === $errors;
			}
		}

		$form_id = wp_unique_id( 'cll-form-' );

		ob_start();
		?>
		<section class="clinic-leads-lab" aria-labelledby="<?php echo esc_attr( $form_id ); ?>-title">
			<h2 id="<?php echo esc_attr( $form_id ); ?>-title"><?php esc_html_e( 'Request a consultation', 'clinic-leads-lab' ); ?></h2>

			<?php if ( $success ) : ?>
				<p role="status"><?php esc_html_e( 'Thank you! Your demo request passed validation. No information was saved or sent.', 'clinic-leads-lab' ); ?></p>
			<?php endif; ?>

			<?php if ( array() !== $errors ) : ?>
				<ul role="alert">
					<?php foreach ( $errors as $error ) : ?>
						<li><?php echo esc_html( $error ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<form method="post">
				<p>
					<label for="<?php echo esc_attr( $form_id ); ?>-name"><?php esc_html_e( 'Name', 'clinic-leads-lab' ); ?></label>
					<input id="<?php echo esc_attr( $form_id ); ?>-name" type="text" name="cll_name" autocomplete="name" maxlength="80" required value="<?php echo esc_attr( $success ? '' : $name ); ?>">
				</p>
				<p>
					<label for="<?php echo esc_attr( $form_id ); ?>-phone"><?php esc_html_e( 'Mobile number', 'clinic-leads-lab' ); ?></label>
					<input id="<?php echo esc_attr( $form_id ); ?>-phone" type="tel" name="cll_phone" autocomplete="tel" inputmode="tel" maxlength="64" required value="<?php echo esc_attr( $success ? '' : $phone ); ?>">
				</p>
				<?php wp_nonce_field( 'cll_consultation', 'cll_nonce', false ); ?>
				<input type="hidden" name="cll_submit" value="1">
				<button type="submit"><?php esc_html_e( 'Send demo request', 'clinic-leads-lab' ); ?></button>
			</form>
		</section>
		<?php
		return (string) ob_get_clean();
	}

	private function post_value( string $key ): string {
		if ( ! isset( $_POST[ $key ] ) || ! is_string( $_POST[ $key ] ) ) {
			return '';
		}

		return sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
	}
}

add_action( 'init', array( new Plugin(), 'register' ) );
