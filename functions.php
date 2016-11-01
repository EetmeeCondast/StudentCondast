<?php
/*
Plugin Name: Student Applicatie Plugin
Description: Plugin t.b.v. student applicatie formulieren.
Text Domain: student
*/
define('STUDENT__PLUGIN_DIR', plugin_dir_path( __FILE__ ));

include(STUDENT__PLUGIN_DIR . "PersonModel.php");
include(STUDENT__PLUGIN_DIR . "PersonView.php");

/*
	Voeg eigen stylesheet toe
*/
function form_css() {
	wp_register_style('student-style', plugins_url('style.css', __FILE__), array(), '1.0.1');
	wp_enqueue_style( 'student-style');
}
add_action('wp_enqueue_scripts', 'form_css');
/*
	Voeg eigen javascript toe
*/
function form_js() {
	wp_register_script('student-js', plugins_url('form.js', __FILE__), array(), '1.0.1');
	wp_enqueue_script( 'student-js');
}
add_action('wp_enqueue_scripts', 'form_js');
/*
	Shortcode "lookup_form". Het "lookup person" formulier wordt getoond.
*/
function lookup_form($atts) {
	include(STUDENT__PLUGIN_DIR . "PersonLookupView.php");
	$search_result = null;
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
			set_student_message("NOBODY_FOUND", 'student');
		}
	} else {
		set_student_message("NOBODY_FOUND", 'student');
	}
	$form = PersonLookupView::form($search_result);
	return $form;
}
add_shortcode("lookup_form", "lookup_form");
/*
 *	Hier wordt het "update person" formulier afgehandeld
 */
function lookup_form_action($atts) {
	$redirect_to = '/lookup-person/';
	$_SESSION["lookup_result"] = array();
	if (!user_is_privileged(1)) {	
		set_student_message("STUDENT_NOT_PRIVILEGED");
	} else {
		include(STUDENT__PLUGIN_DIR . "PersonLookupModel.php");
		$persons = new PersonLookupModel;
		if ($persons->from_database($_POST["surname"])) {
			/*
			Zet de gevonden personen in $_SESSION["lookup_result"].
			*/
			$_SESSION["lookup_result"] = $persons->get_data();
		}
		/*	
		Zet de string waarop gezocht is in $_SESSION["lookup_result"]["search_name"].
		*/
		$_SESSION["lookup_result"]["search_name"] = $_POST["surname"];
	}
	wp_safe_redirect($redirect_to);
	exit;
}
add_action('admin_post_nopriv_lookup_form_action', 'no_privilege');
add_action('admin_post_lookup_form_action', 'lookup_form_action');
/*
	Shortcode "create_form". Het "create person" formulier wordt getoond.
*/
function create_form($atts) {
	$validation_errors = null;
	if (isset($_SESSION["posted_data"])) {
		$validation_errors = $_SESSION["student_messages"];
	}
	$form = "";
	if (user_is_privileged(1)) {
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
/*
	Shortcode "update_form". Het "update person" formulier wordt getoond.
*/
function update_form($atts) {
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
	if (user_is_privileged(1)) {
		$student = new PersonModel;
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
				set_student_message("PERSON_ID_NOT_FOUND");
			}
		}
		$form = PersonView::form($student, "update_form_action", $validation_errors);
	}
	return $form;
}
add_shortcode("update_form", "update_form");
/*
 *	Hier wordt het "update person" formulier afgehandeld
 */
function create_form_action($atts) {
	$redirect_to = '/create_person/';
	if (!user_is_privileged(1)) {	
		set_student_message("STUDENT_NOT_PRIVILEGED");
	} else {
		$student = new PersonModel;
		$student->set_data($_POST);
		/*
		Valideer de ingevoerde gegevens. Eventuele fouten komen in het array $errors terecht.
		*/
		unset_errors();
		$errors = $student->validate();
		if (!$errors) {
			/* Geen fouten geconstateerd. */
			if ($ff = $student->create()) {
				set_student_message("PERSON_CREATED");
			} else {
				set_student_message("PERSON_NOT_CREATED");
			}
			/*
			Opvoeren persoon is goed gegaan.
			Ga naar het zoekscherm, maar hernieuw eerst de
			zoekopdracht met de zojuist opgevoerde naam.
			*/
			$_POST['surname'] = $student->surname();
			lookup_form_action($atts);
			$redirect_to = '/lookup-person/';
		} else {
			/* Er zijn fouten geconstateerd in de invoer. */
			/* Zorg er voor dat die fouten getoond worden. */
			foreach($errors as $name => $error) {
				set_student_message($error, $name);
			}
			/* Zet de fouten in $_SESSION. */
			set_errors($redirect_to, $student);
		}
	}
	wp_safe_redirect($redirect_to);
	exit;
}
add_action('admin_post_nopriv_create_form_action', 'no_privilege');
add_action('admin_post_create_form_action', 'create_form_action');
/*
 *	Hier wordt het "update person" formulier afgehandeld
 */
function update_form_action($atts) {
	$redirect_to = '/update_person/';
	if (!user_is_privileged(1)) {	
		set_student_message("STUDENT_NOT_PRIVILEGED");
	} else {
		$student = new PersonModel;
		$student->set_data($_POST);
		/*
		Valideer de ingevoerde gegevens. Eventuele fouten komen in het array $errors terecht.
		*/
		unset_errors();
		$errors = $student->validate();
		if (!$errors) {
			/* Geen fouten geconstateerd. */
			if ($student->update()) {
				set_student_message("PERSON_CHANGED");
			} else {
				set_student_message("PERSON_NOT_CHANGED");
			}			
			/*
			Muteren persoon is goed gegaan.
			Ga naar het zoekscherm, maar hernieuw eerste de
			zoekopdracht (gegevens zijn gewijzigd, dus mogelijk
			ook de zoekresultaten!).
			*/
			$_POST['surname'] = $_SESSION["lookup_result"]["search_name"];
			lookup_form_action($atts);
			$redirect_to = '/lookup-person/';
		} else {
			/* Er zijn fouten geconstateerd in de invoer. */
			/* Zorg er voor dat die fouten getoond worden. */
			foreach($errors as $name => $error) {
				set_student_message($error, $name);
			}
			/* Zet de fouten in $_SESSION. */
			set_errors($redirect_to, $student);
		}
	}
	wp_safe_redirect($redirect_to);
	exit;
}
add_action('admin_post_nopriv_update_form_action', 'no_privilege');
add_action('admin_post_update_form_action', 'update_form_action');
/*
	Functie om form submits van niet ingelogde gebruikers af te handelen
*/
function no_privilege() {
	set_student_message("STUDENT_NOT_LOGGED_ON");
	wp_safe_redirect($_SERVER['HTTP_REFERER']);
	exit;
}
/*
	Zet boodschap in $_SESSION["student_messages"]
*/
function set_student_message($message, $name = null) {
	if (!isset($_SESSION["student_messages"])) {
		$_SESSION["student_messages"] = array();
	}
	if (!$name) {
		$_SESSION["student_messages"][] = $message;
	} else {
		$_SESSION["student_messages"][$name] = $message;
	}
}
/*
	Shortcode "get_student_messages". De boodschapppen in $_SESSION["student_messages"] worden getoond.
*/
function get_student_messages() {
	$messages = "";
	if (isset($_SESSION["student_messages"])) {
		$messages = PersonView::messages($_SESSION["student_messages"]);
	}
	//	Verwijder na vertoning
	$_SESSION["student_messages"] = array();
	return $messages;
}
add_shortcode("get_student_messages", "get_student_messages");
/*
	Unset $_SESSION["posted_data"] (validation errors).
*/
function unset_errors() {
	if (isset($_SESSION["posted_data"])) {
		$_SESSION["posted_data"] = array();
		unset($_SESSION["posted_data"]);
	}
}
/*
	Set $_SESSION["posted_data"] (validation errors).
*/
function set_errors($src, &$student) {
	/*
	$_SESSION["posted_data"]["SRC"] wordt gebruikt in de functie sess_start()
	om er voor te zorgen dat fouten die bij het valideren geconstateerd zijn
	niet overgaan van het ene naar het andere formulier.
	*/
	$_SESSION["posted_data"] = $student->get_data();
	$_SESSION["posted_data"]["SRC"] = $src;
}
/*
	$_SESSION werkt niet in WordPress.
	Om boodschappen door te geven is het toch wel handig.
*/
function sess_start() {
	if (!session_id()) {
		session_start();
		if (isset($_SESSION["posted_data"])) {
			/*
			Als je van formulier A naar formulier B gaat moeten eventuele
			validatiefouten gewist worden.
			*/
			if ($_SERVER['REQUEST_URI'] != $_SESSION["posted_data"]["SRC"]) {
				unset_errors();
			}
		}
	}
}
/*
	Controleer of gebruiker mag doen wat hij wil doen.
	Dit is een tijdelijke oplossing.
*/
function user_is_privileged($min_user_level) {
	if (!is_user_logged_in()) {
		return false;
	}
	$user_level = wp_get_current_user()->__get('wp_user_level');
	if ($user_level < $min_user_level) {
		return false;
	}
	return true;
}
add_action('init', 'sess_start');
?>