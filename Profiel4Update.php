<?php
/*
Plugin Name: profiel4 persoonsgegevens wijzigen
Description: Plugin t.b.v. formulier "Wijzig persoonsgegevens"
Text Domain: student
*/
/*
	Hier wordt het "Update persoonsgegevens" formulier afgehandeld.
*/
//session_start();

if (!defined('STUDENT__PLUGIN_DIR')) define('STUDENT__PLUGIN_DIR', plugin_dir_path( __FILE__ ));
require_once(STUDENT__PLUGIN_DIR . "utilities.php");

//add_action('init', 'StudentUtilities::sess_start', 1);
//add_action('wp_enqueue_scripts', 'StudentUtilities::form_css');
//add_action('wp_enqueue_scripts', 'StudentUtilities::form_js');

require_once(STUDENT__PLUGIN_DIR . "Profiel4UpdateAction.php"); 
add_action('admin_post_update_form_action', 'Profiel3UpdateAction::action');
/*
	Shortcode "update_form". Het "update person" formulier wordt getoond.
*/
function update4_form($atts) {
	$person_id = 0;
	if (isset($_POST)) {
		if (isset($_POST["person_id"])) {
			$person_id = $_POST["person_id"];
		}
	}
	$validation_errors = null;
	if (isset($_SESSION["posted_data"])) {
		/*
		De create_form_action() functie heeft fouten in de invoer geconstateerd.
		Zorg er voor dat die getoond worden.
		*/
		$validation_errors = $_SESSION["student_messages"];
	}
	$form = "";
	if (StudentUtilities::user_is_privileged(1)) {
		require_once(STUDENT__PLUGIN_DIR . "ProfielModel.php");
		require_once(STUDENT__PLUGIN_DIR . "Profiel4View.php");
		$student = new ProfielModel;
		/*
		We kunnen hier terecht komen nadat een poging om een persooon te wijzigen mislukt is
		vanwege ongeldige invoer (validate() in update_form_action function gaf fouten).
		In dat geval moeten de in $_SESSION["posted_data"] opgeslagen gegevens in het formulierm gezet worden.
		Anders moeten de gegevens uit de database komen.
		*/
		if ($validation_errors) {
			$student->set_data($_SESSION["posted_data"]);
		} else {
			if (!$student->from_database($person_id)) {
				StudentUtilities::set_student_message("PERSON_ID_NOT_FOUND");
			}
		}
		$form = Profiel4View::form($student, "update_form_action", $validation_errors);
	}
	return $form;
}
add_shortcode("update4_form", "update4_form");
?>
