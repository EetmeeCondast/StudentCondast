<?php
	class Profiel1CreateAction {
/*
	Hier wordt het "update profiel" formulier afgehandeld
*/
	public static function action($atts) {
		$MIN_USER_LEVEL = 1;	/*	Het minimale user level dat de gebruiker moet hebben voor deze functie	*/
		$redirect_to = 'beschikbaarheids-data-gast-van-het-eetadres/';
			require_once(STUDENT__PLUGIN_DIR . "ProfielModel.php");
			$student = new ProfielModel;
			$student->set_data($_POST);
			/*
			Valideer de ingevoerde gegevens. Eventuele fouten komen in het array $errors terecht.
			*/
			StudentUtilities::unset_errors();
			//$errors = $student->validate();
			$errors = 0;
			if (!$errors) {
				/* Geen fouten geconstateerd. */
				if ($student->create()) {
					StudentUtilities::set_student_message("PERSON_CREATED");
				} else {
					StudentUtilities::set_student_message("PERSON_NOT_CREATED");
				}
				/*
				Opvoeren persoon is goed gegaan.
				Ga naar het zoekscherm, maar hernieuw eerst de
				zoekopdracht met de zojuist opgevoerde naam.
				*/
				$_POST['surname'] = $student->surname();
				$redirect_to = 'kenmerken-gast/';
			} else {
				/* Er zijn fouten geconstateerd in de invoer. */
				/* Zorg er voor dat die fouten getoond worden. */
				foreach($errors as $name => $error) {
					StudentUtilities::set_student_message($error, $name);
				}
				/* Zet de fouten in $_SESSION. */
				StudentUtilities::set_errors($redirect_to, $student);
			}
		
		wp_safe_redirect($redirect_to);
		exit;
	}
}
?>