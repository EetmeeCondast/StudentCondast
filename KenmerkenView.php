<?php
class KenmerkenView {
	public static function form($db, $profile_id, $action) {
		$kenmerkendata = new KenmerkenData($db);
		$submit_button_text = "";
		switch($action) {
			case "create_kenmerken_action":
				$submit_button_text = __('CREATE', 'student');
				break;
			case "update_kenmerken_action":
				$submit_button_text = __('UPDATE', 'student');
				break;
		}
		$form = "
	<form class='kenmerken-form' method='post' action='" . esc_url(admin_url('admin-post.php')) . "'>
		<input type='hidden' name='action' value='" . $action . "'>
		<input type='hidden' name='profile_id' value='" .$profile_id . "'>";
		$form .= $kenmerkendata->show_qualities($profile_id);
		$form .= "
		<div style='clear: both;'></div>
		<input type='submit' value='" . $submit_button_text . "'>
	</form>";
		return $form;
	}
}

?>
