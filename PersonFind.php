<?php
/*
Plugin Name: Student vinden plugin
Description: Plugin t.b.v. student applicatie formulieren.
Text Domain: student
*/

function PersonFind_shortcode(){
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
}
function Personfind_register_shortcode()
{
    add_shortcode("lookup_form", "lookup_form");
}
    add_action('init´,´Personfind_register_shortcode');
?>