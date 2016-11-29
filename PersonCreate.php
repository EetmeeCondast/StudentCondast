<?php
/*
Plugin Name: Student nieuw persoon opvoeren
Description: Plugin t.b.v. formulier "Invoeren nieuw persoon"
Text Domain: student
*/
define('STUDENT__PLUGIN_DIR', plugin_dir_path( __FILE__ ));
require_once(STUDENT__PLUGIN_DIR . "utilities.php");
/*
	Hier wordt het "Invoeren nieuw persoon" formulier afgehandeld.
*/
require_once(STUDENT__PLUGIN_DIR . "PersonCreateAction.php"); 
add_action('admin_post_create_form_action', 'PersonCreateAction::action');
/*
	Shortcode "create_form". Het "Invoeren nieuw persoon" formulier wordt getoond.
*/
function create_form($atts) {
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
		require_once(STUDENT__PLUGIN_DIR . "PersonModel.php");
		require_once(STUDENT__PLUGIN_DIR . "PersonView.php");
		$student = new PersonModel;
		if ($validation_errors) {
			/*
			We kunnen hier terecht komen nadat een poging om een persooon te registreren mislukt is
			vanwege ongeldige invoer (validate() in create_form_action function gaf fouten).
			In dat geval moeten de in $_SESSION["posted_data"] opgeslagen gegevens in het formulier gezet worden.
			Anders gewoon formulier met default data tonen.
			*/
			$student->set_data($_SESSION["posted_data"]);
		}
		$form = PersonView::form($student, "create_form_action", $validation_errors);
	}
	return $form;
}
add_shortcode("create_form", "create_form");
?>