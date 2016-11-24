<?php
/*
Plugin Name: Student vinden op naam plugin
Description: Plugin t.b.v. formulier "zoek student op naam"
Text Domain: student
*/
/*
	Hier wordt het "Zoek student op naam" formulier afgehandeld
*/
//session_start();

if (!defined('STUDENT__PLUGIN_DIR')) define('STUDENT__PLUGIN_DIR', plugin_dir_path( __FILE__ ));
require_once(STUDENT__PLUGIN_DIR . "utilities.php");

//add_action('init', 'StudentUtilities::sess_start', 1);
//add_action('wp_enqueue_scripts', 'StudentUtilities::form_css');
//add_action('wp_enqueue_scripts', 'StudentUtilities::form_js');

require_once(STUDENT__PLUGIN_DIR . "PersonFindAction.php"); 
add_action('admin_post_lookup_form_action', 'PersonFindAction::action');
/*
	Shortcode "lookup_form". Het "Zoek student op naam" formulier wordt getoond.
*/
function lookup_form($atts) {
	$MIN_USER_LEVEL = 1;	/*	Het minimale user level dat de gebruiker moet hebben voor deze functie	*/
	$search_result = null;
	require_once(STUDENT__PLUGIN_DIR . "PersonLookupView.php");
	if (!StudentUtilities::user_is_privileged($MIN_USER_LEVEL)) {	
		StudentUtilities::set_student_message("STUDENT_NOT_PRIVILEGED");
	} else {
		if (isset($_SESSION["lookup_result"])) {
			/*
			Het resultaat van een zoekopdracht wordt door de lookup_form_action() functie
			in $_SESSION bewaard. Laat dat resltaat nu zien.
			*/
			$search_result = $_SESSION["lookup_result"];
			if (sizeof($_SESSION["lookup_result"]) <= 1) {
				/*
				$_SESSION["lookup_result"] moet in elk geval de zoekstring bevatten,
				$_SESSION["lookup_result"]["search_name"] bevat die string.
				*/
				StudentUtilities::set_student_message("NOBODY_FOUND", 'student');
			}
		} else {
			StudentUtilities::set_student_message("NOBODY_FOUND", 'student');
		}
	}
	$form = PersonLookupView::form($search_result);
	return $form;
}
add_shortcode("lookup_form", "lookup_form");
?>
