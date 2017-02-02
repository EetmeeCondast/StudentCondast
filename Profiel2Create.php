<?php
/*
Plugin Name: Profiel2 nieuw Host opvoeren
Description: Plugin t.b.v. formulier "Invoeren nieuw profiel"
Text Domain: student
*/
if (!defined('STUDENT__PLUGIN_DIR')) define('STUDENT__PLUGIN_DIR', plugin_dir_path( __FILE__ ));
require_once(STUDENT__PLUGIN_DIR . "utilities.php");
/*
	Hier wordt het "Invoeren nieuw persoon" formulier afgehandeld.
*/
require_once(STUDENT__PLUGIN_DIR . "Profiel2CreateAction.php"); 
add_action('admin_post_create_form2_action', 'Profiel2CreateAction::action');
/*
	Shortcode "create_form". Het "Invoeren nieuw persoon" formulier wordt getoond.
*/
function create2_form($atts) {
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
		require_once(STUDENT__PLUGIN_DIR . "Profiel2View.php");
		$student = new ProfielModel;
		if ($validation_errors) {
			/*
			We kunnen hier terecht komen nadat een poging om een persooon te registreren mislukt is
			vanwege ongeldige invoer (validate() in create_form_action function gaf fouten).
			In dat geval moeten de in $_SESSION["posted_data"] opgeslagen gegevens in het formulier gezet worden.
			Anders gewoon formulier met default data tonen.
			*/
			$student->set_data($_SESSION["posted_data"]);
		}
		$form = Profiel2View::form($student, "create_form2_action", $validation_errors);
	}
	return $form;
}
add_shortcode("create2_form", "create2_form");
?>
