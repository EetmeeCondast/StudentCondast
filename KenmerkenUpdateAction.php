<?php
	class KenmerkenUpdateAction {
/*
	Hier wordt het "update kenmerken" formulier afgehandeld
*/
	public static function action($atts) {
		$MIN_USER_LEVEL = 1;	/*	Het minimale user level dat de gebruiker moet hebben voor deze functie	*/
		$redirect_to = 'kenmerken';
		if (!StudentUtilities::user_is_privileged(1)) {	
			StudentUtilities::set_student_message("STUDENT_NOT_PRIVILEGED");
			$redirect_to = '/';
		} else {
			require_once(STUDENT__PLUGIN_DIR . "KenmerkenData.php");
			$kenmerken = new KenmerkenData($_SESSION["db_name"]);
			$kenmerken->update_qualities();
		}
		wp_safe_redirect($redirect_to);
		exit;
	}
}
?>
