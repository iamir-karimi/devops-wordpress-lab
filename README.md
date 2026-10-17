# Clinic Leads Lab

A small WordPress plugin for learning GitHub Flow, automated checks, and pull requests.

## Scope

- Display a name and mobile form with `[clinic_leads]`.
- Validate a WordPress nonce and required fields on the server.
- Show a demo confirmation without saving data, sending email, or calling a CRM.
- Keep application code, comments, and Git messages in English.

Version 0.2.0 adds Iranian mobile validation. `main` contains the 0.1.0 baseline until the feature pull request is merged.

## Mobile validation

The validator accepts local `09xxxxxxxxx` numbers and the equivalent `+98`, `0098`, or bare `98` country-code forms. ASCII, Persian, and Arabic-Indic digits can be mixed. Spaces, parentheses, and hyphens are allowed as formatting separators.

Accepted input is normalized to an 11-digit local ASCII number, for example `09123456789`. The form uses the validator to decide whether to display its demo confirmation; it does not persist the normalized result.

Numbers with an unsupported country code, a wrong length, a landline prefix, letters, or unexpected punctuation are rejected. Validation checks structure only; it does not check number ownership, active service, or carrier allocation.

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

## Automated checks

Run locally with PHP 8.2 or newer:

```bash
git ls-files -z -- '*.php' | xargs -0 -r -n 1 php -l
php -d error_reporting=E_ALL -d display_errors=1 tests/run.php
```

The shell syntax-check command is for Bash (including Git Bash on Windows). The `php tests/run.php` command can also run in PowerShell when PHP is in PATH.

The workflow in `.github/workflows/php-check.yml` runs on pushes to `main` and `feature/**`, and pull requests targeting `main`. It runs syntax checks and behavioral tests on PHP 8.2, 8.3, and 8.4. A failed test returns exit code 1.

Tests cover number normalization, rejected input, shortcode registration, confirmation and error handling, output escaping, nonce rejection, and malformed array inputs. WordPress functions are replaced with minimal test substitutes for the form smoke tests. These tests do not boot a real WordPress installation or a browser.

## Manual WordPress check

After activating the plugin on a test site, verify the following on the front end:

- An empty required field is rejected.
- `۰۹۱۲۳۴۵۶۷۸۹` and `+98 (912) 345-6789` show the demo confirmation.
- `02123456789` and `123` show an error without a confirmation.
- An expired or invalid nonce rejects the submission.
- The form fits the active theme on desktop and mobile.

GitHub Actions is separate from this manual WordPress check. Its passing status alone does not verify theme styling or deployment.

## License

GPL-2.0-or-later. See https://www.gnu.org/licenses/old-licenses/gpl-2.0.html.
