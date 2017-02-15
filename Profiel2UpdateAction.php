<?php
	class Profiel2UpdateAction {
/*
	Hier wordt het "update profiel2" formulier afgehandeld
*/
	public static function action($atts) {
		$MIN_USER_LEVEL = 1;	/*	Het minimale user level dat de gebruiker moet hebben voor deze functie	*/
		$redirect_to = 'update_person/';
		if (!StudentUtilities::user_is_privileged($MIN_USER_LEVEL)) {	
			StudentUtilities::set_student_message("STUDENT_NOT_PRIVILEGED");
			$redirect_to = 'lookup_person/';
		} else {
			require_once(STUDENT__PLUGIN_DIR . "ProfielModel.php");
			$student = new ProfielModel;
			$student->set_data($_POST);
			/*
			Valideer de ingevoerde gegevens. Eventuele fouten komen in het array $errors terecht.
			*/
			StudentUtilities::unset_errors();
			$errors = $student->validate();
			if (!$errors) {
				/* Geen fouten geconstateerd. */
				if ($student->update()) {
					StudentUtilities::set_student_message("PERSON_CHANGED");
				} else {
					StudentUtilities::set_student_message("PERSON_NOT_CHANGED");
				}			
				/*
				Muteren persoon is goed gegaan.
				Ga naar het zoekscherm, maar hernieuw eerste de
				zoekopdracht (gegevens zijn gewijzigd, dus mogelijk
				ook de zoekresultaten!).
				*/
				$_POST['surname'] = $_SESSION["lookup_result"]["search_name"];
				Profiel2FindAction::action($atts);
				$redirect_to = 'lookup-person/';
			} else {
				/* Er zijn fouten geconstateerd in de invoer. */
				/* Zorg er voor dat die fouten getoond worden. */
				foreach($errors as $name => $error) {
					StudentUtilities::set_student_message($error, $name);
				}
				/* Zet de fouten in $_SESSION. */
				StudentUtilities::set_errors($redirect_to, $student);
			}
		}
		wp_safe_redirect($redirect_to);
		exit;
	}
}