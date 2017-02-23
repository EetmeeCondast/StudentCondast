<?php
/*
Plugin Name: Mail formulier wijzig mail
Description: Plugin t.b.v. formulier "Mail wijzigen"
Text Domain: student
*/
if (!defined('STUDENT__PLUGIN_DIR')) {
	define('STUDENT__PLUGIN_DIR', plugin_dir_path( __FILE__ ));
}
require_once(STUDENT__PLUGIN_DIR . "utilities.php");
/*
	Hier wordt het "Wijzig email" formulier afgehandeld.
*/
require_once(STUDENT__PLUGIN_DIR . "MailUpdateAction.php"); 
add_action('admin_post_update_mail_action', 'MailUpdateAction::action');
/*
	Shortcode "mail_form".
*/
function mail_update_form($atts) {
	$event_id = 0;
	if (isset($_POST)) {
		if (isset($_POST["event"])) {
			$event_id = $_POST["event"];
		}
	}
	$form = "";
	if (StudentUtilities::user_is_privileged(1)) {
		require_once(STUDENT__PLUGIN_DIR . "MailView.php");
		require_once(STUDENT__PLUGIN_DIR . "MailModel.php");
		$event = new MailModel;
		$event->from_database($event_id);
		$form = MailView::form($event, "update_mail_action");
	}
	return $form;
}
add_shortcode("mail_update_form", "mail_update_form");
?>