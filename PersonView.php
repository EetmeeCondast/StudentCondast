<?php
class PersonView {

	public static function form(&$person_data, $action, $validation_errors) {
		if ($validation_errors) {
			$js_array_errors = json_encode($validation_errors);
		}
		$submit_button_text = "";
		switch($action) {
			case "create_form_action":
				$submit_button_text = __('CREATE', 'student');
				break;
			case "update_form_action":
				$submit_button_text = __('UPDATE', 'student');
				break;
		}
		$form = "
<fieldset style='border: 1px solid silver;'>
<legend>" . __('UPDATE_PERSON', 'student') . "</legend>
<form method='post' action='" . esc_url(admin_url('admin-post.php')) . "'>
		<input type='hidden' name='action' value='" . $action . "'>
		<input type='hidden' name='person_id' value='" . $person_data->person_id() . "'>
		<label for 'calling_name'>" . __('CALLING_NAME', 'student') . ": </label>
		<input type='text' name='calling_name' id='calling_name' required value='" . $person_data->calling_name() . "'>
		<label for 'firstname'>" . __('FIRSTNAME', 'student') . ": </label>
		<input type='text' name='firstname' id='firstname' value='" . $person_data->firstname() . "'>
		<label for 'prefix'>" . __('PREFIX', 'student') . ": </label>
		<input type='text' name='prefix' id='prefix' value='" . $person_data->prefix() . "'>
		<label for 'surname'>" . __('SURNAME', 'student') . ": </label>
		<input type='text' name='surname' id='surname' required value='" . $person_data->surname() . "'>
		<label for 'gender'>" . __('GENDER', 'student') . ": </label>
		<select name='gender' id='gender'>
			<option " . self::selected($person_data, "gender", "FEMALE") . 
				" value='FEMALE'>" . __('FEMALE', 'student') . "</option>
			<option " . self::selected($person_data, "gender", "ANDROGYNOUS") . 
				" value='ANDROGYNOUS'>" . __('ANDROGYNOUS', 'student') . "</option>
			<option " . self::selected($person_data, "gender", "MALE") . 
				" value='MALE'>" . __('MALE', 'student') . "</option>
			<option " . self::selected($person_data, "gender", "UNKNOWN") . 
				" value='UNKNOWN'>" . __('UNKNOWN', 'student') . "</option>
		</select>
		<label for 'title'>" . __('TITLE', 'student') . ": </label>
		<select name='title' id='title'>
			<option " . self::selected($person_data, "title", "MISSUS") . 
				" value='MISSUS'>" . __('MISSUS', 'student') . "</option>
			<option " . self::selected($person_data, "title", "title_mej") . 
				" value='title_mej'>" . __('title_mej', 'student') . "</option>
			<option " . self::selected($person_data, "title", "MISTER") . 
				" value='MISTER'>" . __('MISTER', 'student') . "</option>
			<option " . self::selected($person_data, "title", "title_dr") . 
				" value='title_dr'>" . __('title_dr', 'student') . "</option>
			<option " . self::selected($person_data, "title", "UNKNOWN") . 
				" value='UNKNOWN'></option>
		</select>
		<div style='clear: both;'></div>
		<label for 'birthdate'>" . __('BIRTHDATE', 'student') . ": </label>
		<input type='date' name='birthdate' id='birthdate' required value='" . $person_data->birthdate() . "'
			min='" . date('Y-m-d', strtotime('-200 years')) . "'
			max='" . date('Y-m-d', (time())) . "'>
		<div style='clear: both;'></div>
		<label for 'nature'>" . __('NATURE', 'student') . ": </label>
		<select name='nature' id='nature'>
			<option " . self::selected($person_data, "nature", "STUDENT") . 
				" value='STUDENT'>" . __('STUDENT', 'student') . "</option>
			<option " . self::selected($person_data, "nature", "FUNCTION") . 
				" value='FUNCTION'>" . __('FUNCTION', 'student') . "</option>
			<option " . self::selected($person_data, "nature", "RETIRED") . 
				" value='RETIRED'>" . __('RETIRED', 'student') . "</option>
			<option " . self::selected($person_data, "nature", "UNKNOWN") . 
				" value='UNKNOWN'>" . __('UNKNOWN', 'student') . "</option>
		</select>
		<label for 'description'>" . __('DESCRIPTION', 'student') . ": </label>
		<textarea name='description' id='description'>" . $person_data->description() . "</textarea>
		<label for 'upas'>" . __('UPAS', 'student') . ": </label>
		<input type='checkbox' " . self::checked($person_data, "upas") . " id='upas' name='upas' value='true'>
		<div style='clear: both;'></div>
		<input type='submit' value='" . $submit_button_text . "'>
	</span>
</form>
</fieldset>";
		if ($validation_errors) {
			$js_array_errors = json_encode($validation_errors);
			$form .= "
<script type='text/javascript'>
	MarkValidationErrors(" . $js_array_errors . ");
</script>";
		}
		return $form;
	}
	public static function messages($messages_array) {
		$messages_string = "";
		if (is_array($messages_array)) {
			foreach($messages_array as $key => $message) {
				$starttag = "<div style='color: green; margin: 0; padding: 0;'>";
				$endtag = "</div>";
				if (!preg_match("/^[0-9]*$/", $key)) {
					$starttag = "<div style='color: red; margin: 0; padding: 0;'>";
				}
				$messages_string .= $starttag . __(sprintf("%s", $message), 'student') . $endtag;
			}
		}
		return $messages_string;
	}
	private static function selected(&$person_data, $name, $value) {
		$selected = "";
		switch($name) {
			case "gender":
				if($value == $person_data->gender()) {
					$selected = "selected";
				}
				break;
			case "title":
				if($value == $person_data->title()) {
					$selected = "selected";
				}
				break;
			case "nature":
				if($value == $person_data->nature()) {
					$selected = "selected";
				}
				break;
		}
		return $selected;
	}
	private static function checked(&$person_data, $name) {
		$checked = "";
		switch($name) {
			case "upas":
				if("true" == $person_data->upas()) {
					$checked = "checked";
				}
				break;
		}
		return $checked;
	}
}

?>