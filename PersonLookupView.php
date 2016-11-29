<?php
class PersonLookupView {

	public static function form($search_result) {
		$search_name = "";
		if ($search_result) {
			$search_name = $search_result["search_name"];
		}
		$form = "
<fieldset style='border: 1px solid silver;'>
<legend>" . __('LOOKUP_PERSON', 'student') . "</legend>
<form method='post' action='" . esc_url(admin_url('admin-post.php')) . "'>
	<input type='hidden' name='action' value='" . "lookup_form_action" . "'>
	<label for 'surname'>" . __('SURNAME', 'student') . ": </label>
	<input type='text' name='surname' id='surname' required value='" . $search_name . "'>
	<div style='clear: both;'></div>
	<input type='submit' value='" . __('SEARCH', 'student') . "'>
</form>";
	if ($search_result) {
		$form .= self::result($search_result);
	}
	$form .= "
</fieldset>";
		return $form;
	}
	private static function result($search_result) {
		$form = "<div style='clear: both;'></div>";
		foreach($search_result as $key => $data) {
			if (is_array($data)) {
				$form .= "
<form method='post' action='" . get_site_url() . "/update_person'>
	<input type='hidden' name='person_id' value='" . $key . "'>
	<button class='no_button'>" .
		($data["title"] ? __(sprintf("%s", $data["title"]), 'student') : "") . " " . 
		$data["calling_name"] . " " . 
		$data["prefix"] . " " . 
		$data["surname"] . ", " . 
		date('d-m-Y', strtotime($data["birthdate"])) . "
	</button>
</form>";
			}
		}
		$form .= "<div style='clear: both;'></div>";
		return $form;
	}
}

?>