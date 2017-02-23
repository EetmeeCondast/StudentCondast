<?php
/*
De StudentUtilities class
*/
add_action('init', 'StudentUtilities::sess_start', 0);
add_action('wp_enqueue_scripts', 'StudentUtilities::form_css', 11);
add_action('wp_enqueue_scripts', 'StudentUtilities::form_js');

class StudentUtilities {
	/*
		Voeg eigen stylesheet toe
	*/
	public static function form_css() {
		$css_file_name = "style.css";
		if (StudentUtilities::get("db_name")) {
			$css_file_name = StudentUtilities::get("db_name") . "_style.css";
		}
		wp_register_style('student-style', plugins_url($css_file_name, __FILE__), array(), '1.1.10');
		wp_enqueue_style( 'student-style');
	}
	/*
		Voeg eigen javascript(s) toe
	*/
	public static function form_js() {
		wp_register_script('student-js', plugins_url('form.js', __FILE__), array(), '1.0.3');
		wp_enqueue_script( 'student-js');
		/* ckeditor script */
		wp_register_script('ckeditor-js', plugins_url('ckeditor/ckeditor.js', __FILE__), array(), '1.0.1');
		wp_enqueue_script( 'ckeditor-js');
	}
	/*
		Functie om form submits van niet ingelogde gebruikers af te handelen
	*/
	public static function no_privilege() {
		self::set_student_message("STUDENT_NOT_LOGGED_ON");
		wp_safe_redirect($_SERVER['HTTP_REFERER']);
		exit;
	}
	/*
		Zet boodschap in $_SESSION["student_messages"]
	*/
	public static function set_student_message($message, $name = null) {
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
		Unset $_SESSION["posted_data"] (validation errors).
	*/
	public static function unset_errors() {
		if (isset($_SESSION["posted_data"])) {
			$_SESSION["posted_data"] = array();
			unset($_SESSION["posted_data"]);
		}
	}
	/*
		Set $_SESSION["posted_data"] (validation errors).
	*/
	public static function set_errors($src, &$student) {
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
	public static function sess_start() {
		if (!session_id()) {
			session_start();
		}
		else {
			if (isset($_SESSION["posted_data"])) {
				/*
				Als je van formulier A naar formulier B gaat moeten eventuele
				validatiefouten gewist worden.
				*/
				if ($_SERVER['REQUEST_URI'] != $_SESSION["posted_data"]["SRC"]) {
					self::unset_errors();
				}
			}
		}
	}
	/*
		Controleer of gebruiker mag doen wat hij wil doen.
		Dit is een tijdelijke oplossing.
	*/
	public static function user_is_privileged($min_user_level) {
		if (!is_user_logged_in()) {
			return false;
		}
		$user_level = wp_get_current_user()->__get('wp_user_level');
		if ($user_level < $min_user_level) {
			return false;
		}
		return true;
	}
	public static function post($key) {
		if (!isset($_POST)) {
			return false;
		}
		if (!isset($_POST[$key])) {
			return false;
		}
		return $_POST[$key];
	}
	public static function get($key) {
		if (!isset($_GET)) {
			return false;
		}
		if (!isset($_GET[$key])) {
			return false;
		}
		return $_GET[$key];
	}
}
