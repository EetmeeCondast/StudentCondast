<?php
class ProfielView {
	public static function form(&$profile_data, $action, $validation_errors) {
		$form = "
<fieldset style='border: 1px solid silver;'>
<legend>" . __('BESCHIKBAARHEID PROFIEL1', 'student') . "</legend>
	<form method='post' action='" . esc_url(admin_url('admin-post.php')) . "'>
		<input type='hidden' name='action' value='" . $action . "'>
		<input type='hidden' name='profile_id' value='" . $profile_data->profile_id() . "'>";
//	Leeg beschikbaarheidsblok
		$form .= "
		<fieldset style='border: 2px solid green;'>
		<table id='availability_node' style='border: 4px inset Turquoise;'>
			<tr>
				<td>
					" . __("ACTIVATE", "student") . "	
				</td>
				<td>
					<input class='availability_activate' type='checkbox' name='availability[0][activate]'>
				</td>
				<td>
					" . __("MEALTYPE", "student") . "	
				</td>
				<td>
					<select class='availability_mealtype' name='availability[0][mealtype]'>
						<option value=''></option>
						<option value='SUPPER'>" . __("SUPPER", "student") . "</option>
						<option value='LUNCH'>" . __("LUNCH", "student") . "</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					" . __("STARTDATE", "student") . "	
				</td>
				<td>
					<input class='availability_startdate' type='date' name='availability[0][startdate]' value='" . 
						date("Y-m-d") . "'>
				</td>
				<td>
					" . __("ENDDATE", "student") . "	
				</td>
				<td>
					<input class='availability_enddate' type='date' name='availability[0][enddate]' value='" . 
						date("Y-m-d", strtotime('+5 years')) . "'>
				</td>
			</tr>
			<tr>
				<td>
					" . __("MEALSPERPERIOD", "student") . "	
				</td>
				<td>
					<input class='availability_mealsperperiod' type='number' name='availability[0][mealsperperiod]' value='0'>
				</td>
				<td>
					" . __("PERIODICITY", "student") . "	
				</td>
				<td>
					<select class='availability_periodicity' name='availability[0][periodicity]'>
						<option value='1'>" . __("PER_YEAR", "student") . "</option>
						<option value='12'>" . __("PER_MONTH", "student") . "</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan=3>
					<input class='availability_days' type='checkbox' name='availability[0][days]' value='MO'>" . 
						__("MO_", "student") . "
					<input class='availability_days' type='checkbox' name='availability[0][days]' value='TU'>" . 
						__("TU_", "student") . "
					<input class='availability_days' type='checkbox' name='availability[0][days]' value='WE'>" . 
						__("WE_", "student") . "
					<input class='availability_days' type='checkbox' name='availability[0][days]' value='TH'>" . 
						__("TH_", "student") . "
					<input class='availability_days' type='checkbox' name='availability[0][days]' value='FR'>" . 
						__("FR_", "student") . "
					<input class='availability_days' type='checkbox' name='availability[0][days]' value='SA'>" . 
						__("SA_", "student") . "
					<input class='availability_days' type='checkbox' name='availability[0][days]' value='SU'>" .
						__("SU_", "student") . "
				</td>
				<td>
					<button class='add_delete_button' style='float: right' onClick='javascript:add_node(\"availability_node\"); return false;'>NEW</button>
				</td>
			</tr>
		</table>
		</fieldset>";
			$js_array_availability_data = json_encode($profile_data->availability());
		$form .= "
<script type='text/javascript'>
	append_data('availability', " . $js_array_availability_data . ");
</script>";
//	Leeg members blok
		$form .= "
		<fieldset style='border: 2px solid green;'>
		<table id='members_node' style='border: 4px inset Turquoise;'>
			<tr>
				<td>
					" . __("FIRSTNAME", "student") . "	
				</td>
				<td>
					<input class='members_firstname' type='text' name='members[0][firstname]'>	
				</td>
				<td>
					" . __("PREFIX", "student") . "	
				</td>
				<td>
					<input class='members_prefix' type='text' name='members[0][prefix]'>	
				</td>
				<td>
					" . __("SURNAME", "student") . "	
				</td>
				<td>
					<input class='members_surname' type='text' name='members[0][surname]'>	
				</td>
			</tr>
			<tr>
				<td>
					" . __("NATURE", "student") . "	
				</td>
				<td>
					<select class='members_nature' name='members[0][nature]'>
						<option value=''></option>
						<option value='FUNCTION'>" . __("FUNCTION", "student") . "</option>
						<option value='STUDENT'>" . __("STUDENT", "student") . "</option>
						<option value='RETIRED'>" . __("RETIRED", "student") . "</option>
					</select>
				</td>
				<td>
					" . __("DESCRIPTION", "student") . "	
				</td>
				<td>
					<input class='members_description' type='text' name='members[0][description]'>	
				</td>
				<td>
					" . __("HOBBIES", "student") . "	
				</td>
				<td>
					<input class='members_hobbies' type='text' name='members[0][hobbies]'>	
				</td>
			</tr>
			<tr>
				<td>
					" . __("BIRTHDATE", "student") . "	
				</td>
				<td>
					<input class='members_birthdate' type='date' name='members[0][birthdate]' value='1970-01-01'>	
				</td>
				<td>
					" . __("MEMBERTYPE", "student") . "	
				</td>
				<td>
					<select class='members_membertype' name='members[0][membertype]'>
						<option value=''></option>
						<option value='MAIN'>" . __("MAIN_MEMBERTYPE", "student") . "</option>
						<option value='ROOM_MATE'>" . __("ROOM_MATE", "student") . "</option>
						<option value='TAG_ALONG'>" . __("TAG_ALONG", "student") . "</option>
						<option value='FAMILY'>" . __("FAMILY", "student") . "</option>
						<option value='INTERN'>" . __("INTERN", "student") . "</option>
					</select>
				</td>
				<td>
				</td>
				<td>
					<button class='add_delete_button' style='float: right' onClick='javascript:add_node(\"members_node\"); return false;'>NEW</button>
				</td>
			</tr>
		</table>
		</fieldset>";
		$js_array_group_members = json_encode($profile_data->group_members());
		$form .= "
<script type='text/javascript'>
	append_data('members', " . $js_array_group_members . ");
</script>";
		$form .= "
	</form>
</fieldset";
		echo $form;
	}
	private static function selected(&$profile_data, $name, $value) {
		$selected = "";
		switch($name) {
			case "gender":
				if($value == $profile_data->gender()) {
					$selected = "selected";
				}
				break;
			case "title":
				if($value == $profile_data->title()) {
					$selected = "selected";
				}
				break;
			case "nature":
				if($value == $profile_data->nature()) {
					$selected = "selected";
				}
				break;
		}
		return $selected;
	}
	private static function checked(&$profile_data, $name) {
		$checked = "";
		switch($name) {
			case "upas":
				if("true" == $profile_data->upas()) {
					$checked = "checked";
				}
				break;
		}
		return $checked;
	}
}
?>
