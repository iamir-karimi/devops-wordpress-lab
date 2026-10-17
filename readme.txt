=== Clinic Leads Lab ===
Requires at least: 6.5
Requires PHP: 8.2
Stable tag: 0.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

A small demo consultation form for practicing GitHub Flow and CI.

== Description ==

Use [clinic_leads] on a local or staging page. The form validates its nonce
and required fields, including Iranian mobile format. Persian and Arabic-Indic
digits are supported. It does not store or send submitted data.

== Installation ==

1. Copy the project into wp-content/plugins/clinic-leads-lab/.
2. Activate Clinic Leads Lab.
3. Add [clinic_leads] to a test page.

== Changelog ==

= 0.2.0 =
* Validate Iranian mobile format and normalize supported country prefixes.
* Support Persian and Arabic-Indic digits.
* Add automated syntax checks and behavioral tests.

= 0.1.0 =
* Add the demo shortcode and basic form checks.
