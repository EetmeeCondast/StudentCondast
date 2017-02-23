<?php
/*
Plugin Name: Mail formulier
Description: Plugin t.b.v. formulier "Mail aanmaken/wijzigen"
Text Domain: student
*/
if (!defined('STUDENT__PLUGIN_DIR')) {
	define('STUDENT__PLUGIN_DIR', plugin_dir_path( __FILE__ ));
}
require_once(STUDENT__PLUGIN_DIR . "utilities.php");
/*
	Shortcode "mail_form".
*/
function mail_form($atts) {
	$form = "";
	if (StudentUtilities::user_is_privileged(1)) {
		require_once(STUDENT__PLUGIN_DIR . "MailView.php");
		$form = MailView::form();
	}
	return $form;
}
add_shortcode("mail_form", "mail_form");
?>