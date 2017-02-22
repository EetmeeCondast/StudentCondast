<?php
/*
Plugin Name: Student toon (fout)boodschappen
Description: Plugin t.b.v. Student formulieren
Text Domain: student
*/
if (!defined('STUDENT__PLUGIN_DIR')) define('STUDENT__PLUGIN_DIR', plugin_dir_path( __FILE__ ));
require_once(STUDENT__PLUGIN_DIR . "utilities.php");

function get_student_messages() {
	require_once(STUDENT__PLUGIN_DIR . "PersonView.php");
	$messages = "";
	if (isset($_SESSION["student_messages"])) {
		$messages = PersonView::messages($_SESSION["student_messages"]);
	}
	//	Verwijder na vertoning
	$_SESSION["student_messages"] = array();
	return $messages;
}
add_shortcode("get_student_messages", "get_student_messages");
