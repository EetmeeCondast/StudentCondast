<?php
	class MailCreateAction {
/*
	Hier wordt het "create mail" formulier afgehandeld
*/
	public static function action($atts) {
		$MIN_USER_LEVEL = 1;	/*	Het minimale user level dat de gebruiker moet hebben voor deze functie	*/
		$redirect_to = '/';
		if (!StudentUtilities::user_is_privileged(1)) {	
			StudentUtilities::set_student_message("STUDENT_NOT_PRIVILEGED");
			$redirect_to = 'update_mail/';
		} else {
			require_once(STUDENT__PLUGIN_DIR . "MailModel.php");
			$mail = new MailModel;
			$mail->set_data($_POST);
			$mail->create();
		}
		wp_safe_redirect($redirect_to);
		exit;
	}
}
?>