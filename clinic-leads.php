<?php
/**
 * Plugin Name: Clinic Leads Lab
 * Description: A consultation form used to practice GitHub Flow and CI.
 * Version: 0.2.0
 * Requires at least: 6.5
 * Requires PHP: 8.2
 * License: GPL-2.0-or-later
 * Text Domain: clinic-leads-lab
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/includes/class-phone-validator.php';
require_once __DIR__ . '/includes/class-plugin.php';

add_action( 'init', array( new \ClinicLeadsLab\Plugin(), 'register' ) );
