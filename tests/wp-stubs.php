<?php
/**
 * Minimal WordPress API substitutes for isolated smoke tests.
 */

if ( 'cli' !== PHP_SAPI ) {
	exit;
}

define( 'ABSPATH', __DIR__ . '/' );

function add_action( string $hook, callable $callback ): void {
	$GLOBALS['cll_actions'][ $hook ] = $callback;
}

function add_shortcode( string $tag, callable $callback ): void {
	$GLOBALS['cll_shortcodes'][ $tag ] = $callback;
}

function __( string $text, string $domain ): string {
	return $text;
}

function esc_attr( string $text ): string {
	return htmlspecialchars( $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
}

function esc_html( string $text ): string {
	return esc_attr( $text );
}

function esc_html_e( string $text, string $domain ): void {
	echo esc_html( $text );
}

function wp_unique_id( string $prefix ): string {
	static $id = 0;
	return $prefix . ++$id;
}

function sanitize_text_field( string $text ): string {
	return trim( strip_tags( $text ) );
}

function wp_unslash( string $text ): string {
	return stripslashes( $text );
}

function wp_verify_nonce( string $nonce, string $action ): bool {
	return 'cll_consultation' === $action && 'valid-test-nonce' === $nonce;
}

function wp_nonce_field( string $action, string $name, bool $referer ): void {
	echo '<input type="hidden" name="' . esc_attr( $name ) . '" value="valid-test-nonce">';
}
