<?php
/*
Plugin Name: Mail formulier nieuwe mail
Description: Plugin t.b.v. formulier "Mail aanmaken"
Text Domain: student
*/
if (!defined('STUDENT__PLUGIN_DIR')) {
	define('STUDENT__PLUGIN_DIR', plugin_dir_path( __FILE__ ));
}
require_once(STUDENT__PLUGIN_DIR . "utilities.php");
/*
	Hier wordt het "Invoeren nieuwe email" formulier afgehandeld.
*/
require_once(STUDENT__PLUGIN_DIR . "MailCreateAction.php"); 
add_action('admin_post_create_mail_action', 'MailCreateAction::action');
/*
	Shortcode "mail_form".
*/
function mail_create_form($atts) {
	$form = "";
	if (StudentUtilities::user_is_privileged(1)) {
		require_once(STUDENT__PLUGIN_DIR . "MailModel.php");
		$mail = new MailModel;
		require_once(STUDENT__PLUGIN_DIR . "MailView.php");
		$form = MailView::form($mail, "create_mail_action");
	}
	return $form;
}
add_shortcode("mail_create_form", "mail_create_form");
?>