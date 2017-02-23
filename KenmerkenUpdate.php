<?php
/*
Plugin Name: Student kenmerken wijzigen
Description: Plugin t.b.v. formulier "Wijzig kenmerken"
Text Domain: student
*/
/*
	Hier wordt het "Update kenmerken" formulier afgehandeld.
*/
if (!defined('STUDENT__PLUGIN_DIR')) define('STUDENT__PLUGIN_DIR', plugin_dir_path( __FILE__ ));
require_once(STUDENT__PLUGIN_DIR . "utilities.php");
require_once(STUDENT__PLUGIN_DIR . "KenmerkenUpdateAction.php"); 
add_action('admin_post_update_kenmerken_action', 'KenmerkenUpdateAction::action');
/*
	Shortcode "kenmerken_form". Het "update kenmerken" formulier wordt getoond.
*/           
function update_kenmerken($atts) {
	if (StudentUtilities::get("profile_id")) {
		$_SESSION["profile_id"] = StudentUtilities::get("profile_id");
	}
	if (StudentUtilities::post("profile_id")) {
		$_SESSION["profile_id"] = StudentUtilities::post["profile_id"];
	}
	if (StudentUtilities::get("db_name")) {
		$_SESSION["db_name"] = StudentUtilities::get("db_name");
	}
	$form = "";
	if (StudentUtilities::user_is_privileged(1)) {
		require_once(STUDENT__PLUGIN_DIR . "KenmerkenData.php");
		require_once(STUDENT__PLUGIN_DIR . "KenmerkenView.php");
		$form = KenmerkenView::form($_SESSION["db_name"], $_SESSION["profile_id"], "update_kenmerken_action");
	}
	return $form;
}
add_shortcode("update_kenmerken", "update_kenmerken");
?>
