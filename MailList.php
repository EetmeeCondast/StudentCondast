<?php
/*
Plugin Name: Plugin lijst met mails
Description: Plugin t.b.v. formulier "Toon alle bestaande mails"
Text Domain: student
*/
/*
	Hier wordt het "Toon alle bestaande mails" formulier afgehandeld
*/
if (!defined('STUDENT__PLUGIN_DIR')) define('STUDENT__PLUGIN_DIR', plugin_dir_path( __FILE__ ));
require_once(STUDENT__PLUGIN_DIR . "utilities.php");
/*
	Shortcode "mails_form". Het "Toon alle bestaande mails" formulier wordt getoond.
*/
function  mails_form($atts) {
	$MIN_USER_LEVEL = 1;	/*	Het minimale user level dat de gebruiker moet hebben voor deze functie	*/
	$mails = null;
	require_once(STUDENT__PLUGIN_DIR . "MailsView.php");
	if (!StudentUtilities::user_is_privileged($MIN_USER_LEVEL)) {	
		StudentUtilities::set_student_message("STUDENT_NOT_PRIVILEGED");
	} else {
		require_once(STUDENT__PLUGIN_DIR . "MailModel.php");
		$mails = new MailModel;
	}
	if ($mails) {
		$form = MailsView::form($mails);
		return $form;
	}
}
add_shortcode("mails_form", "mails_form");
?>
