<?php
/**
 * Validate Iranian mobile number structure without WordPress dependencies.
 */

namespace ClinicLeadsLab;

final class PhoneValidator {

	/**
	 * Return a local ASCII number, or null for an unsupported number.
	 *
	 * This checks format only, not ownership or whether a number is active.
	 */
	public static function normalize( string $phone ): ?string {
		if ( strlen( $phone ) > 128 ) {
			return null;
		}

		$phone = strtr(
			$phone,
			array(
				'۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
				'۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
				'٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
				'٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
			)
		);

		$phone = preg_replace( '/[\s\p{Z}()\-]+/u', '', $phone );
		if ( null === $phone ) {
			return null;
		}

		if ( str_starts_with( $phone, '+98' ) ) {
			$phone = '0' . substr( $phone, 3 );
		} elseif ( str_starts_with( $phone, '0098' ) ) {
			$phone = '0' . substr( $phone, 4 );
		} elseif ( str_starts_with( $phone, '98' ) ) {
			$phone = '0' . substr( $phone, 2 );
		}

		return 1 === preg_match( '/\A09[0-9]{9}\z/', $phone ) ? $phone : null;
	}
}
