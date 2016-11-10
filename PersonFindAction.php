<?php
class PersonFindAction {
/*
	Hier wordt het "Zoek student op naam" formulier afgehandeld
*/
	public static function action($atts) {
		$MIN_USER_LEVEL = 1;	/*	Het minimale user level dat de gebruiker moet hebben voor deze functie	*/
		$redirect_to = '/lookup-person/';
		$_SESSION["lookup_result"] = array();
		if (!StudentUtilities::user_is_privileged($MIN_USER_LEVEL)) {	
			StudentUtilities::set_student_message("STUDENT_NOT_PRIVILEGED");
		} else {
			require_once(STUDENT__PLUGIN_DIR . "PersonLookupModel.php");
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
}
?>