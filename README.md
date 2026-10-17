# Clinic Leads Lab

A small WordPress plugin for learning GitHub Flow, automated checks, and pull requests.

## Scope

- Display a name and mobile form with `[clinic_leads]`.
- Validate a WordPress nonce and required fields on the server.
- Show a demo confirmation without saving data, sending email, or calling a CRM.
- Keep application code, comments, and Git messages in English.

Version 0.1.0 checks required fields only. Iranian mobile validation is the first feature exercise.

## Requirements and installation

Use a local or staging WordPress installation (WordPress 6.5+, PHP 8.2+).

1. Copy this repository to `wp-content/plugins/clinic-leads-lab/`.
2. Activate **Clinic Leads Lab** in WordPress.
3. Put `[clinic_leads]` in a Shortcode block on a test page.
4. Open the page on the front end and submit the form.

Exclude the test page from full-page caching so its form nonce stays current. The page inherits your theme's form styles. Put one consultation form on each test page.

## Learning sequence

1. Bootstrap the plugin on `main`.
2. Create `feature/validate-iran-mobile` for mobile validation.
3. Add automated PHP syntax checks and behavioral tests.
4. Review the checks and open a pull request into `main`.
5. Review and merge the pull request as a separate learning step.

Merging source code does not deploy or activate the plugin on a WordPress site.

## License

GPL-2.0-or-later. See https://www.gnu.org/licenses/old-licenses/gpl-2.0.html.
