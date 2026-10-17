<?php
/**
 * Behavioral tests. Run with: php tests/run.php
 */

if ( 'cli' !== PHP_SAPI ) {
	exit;
}

require_once __DIR__ . '/../includes/class-phone-validator.php';

use ClinicLeadsLab\PhoneValidator;

$passed = 0;
$failed = 0;

function check( string $label, bool $condition ): void {
	global $passed, $failed;
	if ( $condition ) {
		++$passed;
		echo "PASS: {$label}\n";
	} else {
		++$failed;
		fwrite( STDERR, "FAIL: {$label}\n" );
	}
}

$accepted = array(
	'local ASCII'           => '09123456789',
	'Persian digits'        => '۰۹۱۲۳۴۵۶۷۸۹',
	'Arabic-Indic digits'   => '٠٩١٢٣٤٥٦٧٨٩',
	'plus country code'     => '+989123456789',
	'bare country code'     => '989123456789',
	'00 country code'       => '00989123456789',
	'Persian international' => '+۹۸۹۱۲۳۴۵۶۷۸۹',
	'Arabic international'  => '٠٠٩٨٩١٢٣٤٥٦٧٨٩',
	'spaces and hyphens'    => ' 0912 345-6789 ',
	'parentheses'           => '+98 (912) 345-6789',
	'nonbreaking space'     => "0912\u{00A0}3456789",
	'mixed digit scripts'   => '۰9١۲34۵6۷۸9',
);

foreach ( $accepted as $label => $input ) {
	check( 'accept ' . $label, '09123456789' === PhoneValidator::normalize( $input ) );
}

$rejected = array(
	'empty'                     => '',
	'whitespace only'           => '   ',
	'too short'                 => '0912345678',
	'too long'                  => '091234567890',
	'landline'                  => '02123456789',
	'missing local prefix'      => '9123456789',
	'wrong country'             => '+971501234567',
	'country with local zero'   => '+9809123456789',
	'letters'                   => '0912abc6789',
	'HTML'                      => '<script>09123456789</script>',
	'extension'                 => '09123456789 ext 1',
	'unexpected punctuation'    => '0912/3456789',
	'Unicode fullwidth digits'  => '０９１２３４５６７８９',
	'embedded bidi character'   => "0912\u{202E}3456789",
	'invalid UTF-8'             => "0912\xFF3456789",
	'excessive input'           => str_repeat( ' ', 129 ) . '09123456789',
	'trailing second number'    => '09123456789 09123456789',
);

foreach ( $rejected as $label => $input ) {
	check( 'reject ' . $label, null === PhoneValidator::normalize( $input ) );
}

// Minimal WordPress substitutes exercise form wiring, not WordPress core.
require_once __DIR__ . '/wp-stubs.php';
require_once __DIR__ . '/../clinic-leads.php';

check( 'plugin registers its init hook', isset( $GLOBALS['cll_actions']['init'] ) );
call_user_func( $GLOBALS['cll_actions']['init'] );
$render = $GLOBALS['cll_shortcodes']['clinic_leads'] ?? null;
check( 'plugin registers the shortcode', is_callable( $render ) );
if ( ! is_callable( $render ) ) {
	exit( 1 );
}

function render_form( callable $render, string $method, array $post ): string {
	$_SERVER['REQUEST_METHOD'] = $method;
	$_POST                    = $post;
	ob_start();
	$html   = $render();
	$leaked = ob_get_clean();
	check( 'shortcode returns its markup without leaking output', '' === $leaked );
	return $html;
}

$html = render_form( $render, 'GET', array() );
check( 'GET displays the form', str_contains( $html, 'name="cll_phone"' ) );
check( 'GET has no success message', ! str_contains( $html, 'role="status"' ) );
check( 'form contains its nonce', str_contains( $html, 'name="cll_nonce"' ) );

$post = array( 'cll_submit' => '1', 'cll_name' => 'Demo Visitor', 'cll_phone' => '۰۹۱۲۳۴۵۶۷۸۹', 'cll_nonce' => 'valid-test-nonce' );
$html = render_form( $render, 'POST', $post );
check( 'valid Persian mobile reaches confirmation', str_contains( $html, 'role="status"' ) );
check( 'success clears the name field', ! str_contains( $html, 'value="Demo Visitor"' ) );

$bad               = $post;
$bad['cll_phone']  = '123';
$bad['cll_name']   = 'Demo "Visitor"';
$html              = render_form( $render, 'POST', $bad );
check( 'invalid mobile shows an error', str_contains( $html, 'role="alert"' ) );
check( 'invalid mobile cannot reach confirmation', ! str_contains( $html, 'role="status"' ) );
check( 'retained input is attribute-escaped', str_contains( $html, 'value="Demo &quot;Visitor&quot;"' ) );

$bad              = $post;
$bad['cll_nonce'] = 'invalid-test-nonce';
$html             = render_form( $render, 'POST', $bad );
check( 'invalid nonce rejects submission', str_contains( $html, 'form expired' ) && ! str_contains( $html, 'role="status"' ) );

$bad = $post;
unset( $bad['cll_nonce'] );
$html = render_form( $render, 'POST', $bad );
check( 'missing nonce rejects submission', ! str_contains( $html, 'role="status"' ) );

$bad             = $post;
$bad['cll_name'] = '   ';
$html            = render_form( $render, 'POST', $bad );
check( 'empty name rejects submission', ! str_contains( $html, 'role="status"' ) );

$bad             = $post;
$bad['cll_name'] = str_repeat( 'a', 81 );
$html            = render_form( $render, 'POST', $bad );
check( 'overlong name rejects submission', ! str_contains( $html, 'role="status"' ) );

$bad             = $post;
$bad['cll_name'] = str_repeat( 'م', 80 );
$html            = render_form( $render, 'POST', $bad );
check( '80-character Persian name is accepted', str_contains( $html, 'role="status"' ) );

foreach ( array( 'cll_name', 'cll_phone', 'cll_nonce', 'cll_submit' ) as $field ) {
	$bad           = $post;
	$bad[ $field ] = array( 'unexpected' );
	$html          = render_form( $render, 'POST', $bad );
	check( 'array input is rejected for ' . $field, ! str_contains( $html, 'role="status"' ) );
}

$html = render_form( $render, 'GET', $post );
check( 'GET cannot submit the form', ! str_contains( $html, 'role="status"' ) );

echo "\n{$passed} passed, {$failed} failed.\n";
exit( $failed > 0 ? 1 : 0 );
