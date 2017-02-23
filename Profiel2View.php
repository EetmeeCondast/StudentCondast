<?php
class Profiel2View {
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
?>
 <!DOCTYPE html>
    <html>
        <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script>
        $(document).ready(function(){
               $("#button1").click(function(){
                $("ol").append("<li><select name='introducee'></select><button value='delete'</li>");
            });
            console.log("hi");
            
            $(function(){
            
             var maxAppend = 0;
                $("#button1").click(function(){
                    if (maxAppend >= 250) return;
                    var newProf = 
                        
"<label for 'firstname'>Voornaam: </label> <input type='text' name='firstname' id='firstname'  value=" + <?php echo "'".$person_data->firstname()."'"; ?> + ">  <label for 'surname'>Achternaam: </label> <input type='text' name='surname' id='surname'  value="+ <?php echo "'" .$person_data->surname(). "'"; ?> +"> <label for 'birthdate'>" + <?php echo "'" . __('BIRTHDATE', 'student')."'"  ; ?>+ ": </label>   <input type='date' name='birthdate' id='birthdate' required value=" + <?php echo "'" . $person_data->birthdate() . "'"; ?> + " min=" + <?php echo "'". date('Y-m-d', strtotime('-200 years'))."'"; ?> + "' max=" + <?php echo "'". date('Y-m-d', (time())). "'";?> + "'> <label for 'introducee'>" + <?php echo "'" .__('INTRODUCEE', 'student')."'" ; ?> + ": </label> <select name='introducee' id='introducee'><option value=''>Hoofd</option> <option value=''>Familie</option> <option value=''>Huisgenoot</option> <option value=''>Introducee</option> </select>                                                                                                                                                                            <button class = 'delete' type = 'button' >-</button>                                                                                                                                                                                               <br>";
                //$('#newTicket').append(newTicket); 
                     $('#newProf').append(newProf);
                maxAppend++;
                    
            });
           $("#newProf").delegate(".delete", "click", function(){
               $(this).parent().remove();
               maxAppend--;
            });
        });
        });
              
            </script>
        </head>
            
<?php
		$form = "
<fieldset style='border: 1px solid silver;'>
<legend>" . __('BESCHIKBAARHEID PROFIEL2', 'student') . "</legend>
	<form method='post' action='" . esc_url(admin_url('admin-post.php')) . "'>
		<input type='hidden' name='action' value='".$action."'>
		<input type='hidden' name='profile_id' value='" . $person_data->profile_id() . "'>
		<label for 'mealtype'>" . __('MEALTYPE', 'student') . ": </label>
		<input type='text' name='mealtype' id='mealtype' required value='" . $person_data->mealtype() . "'>
		<label for 'startdate'>" . __('STARTDATE', 'student') . ": </label>
		<input type='text' name='startdate' id='startdate' value='" . $person_data->startdate() . "'>
		<label for 'enddate'>" . __('ENDDATE', 'student') . ": </label>
		<input type='text' name='enddate' id='enddate' value='" . $person_data->enddate() . "'>
		<label for 'periodicity'>" . __('PERIODICITY', 'student') . ": </label>
		<input type='text' name='periodicity' id='periodicity' required value='" . $person_data->periodicity() . "'>
        <label for 'days'>" . __('DAYS', 'student') . ": </label>
		<input type='text' name='days' id='days' required value='" . $person_data->days() . "'>
        <label for 'activate'>" . __('ACTIVATE', 'student') . ": </label>
        <input type='checkbox' name='activate' id='activate'  value='" . $person_data->activate() . "'>
        <label for 'delete'>" . __('DELETE', 'student') . ": </label>
		<input type='checkbox' name='delete' id='delete'  value=''>
        </fieldset>
        <fieldset style='border: 1px solid silver;'>
        <legend>" . __('MAALTIJDEN', 'student') . "</legend>
		<input type='checkbox' name='alles' id='alles' value='alles' checked>
        <label for 'alles'>" . __('alles', 'student') . " </label><br>
		<input type='checkbox' name='vlees' id='vlees'  value='vlees' >
        <label for 'vlees'>" . __('vlees', 'student') . " </label><br>
		<input type='checkbox' name='alleen_geen_vlees' id='alleen_geen_vlees' value='alleen_geen_vlees'>
        <label for 'alleen_geen_vlees'>" . __('alleen geen vlees', 'student') . " </label><br>
		<input type='checkbox' name='vegetarisch' id='vegetarisch' value='vegetarisch'>
        <label for 'vegetarisch'>" . __('vegetarisch', 'student') . "</label><br>
		<input type='checkbox' name='veganistisch' id='veganistisch' value='veganistisch'>
        <label for 'veganistisch'>" . __('veganistisch', 'student') . "</label><br>
		<input type='checkbox' name='halal' id='halal' value='halal'>
        <label for 'halal'>" . __('halal', 'student') . " </label><br>
		<input type='checkbox' name='kosher' id='kosher' value='kosher'>
        <label for 'kosher'>" . __('kosher', 'student') . " </label><br>
        <fieldset style='border: 1px solid silver;'>
        <legend>" . __('SPECIALE DIEET WENSEN,', 'student') . "</legend>
		<input type='checkbox' name='speciaal_dieet_mogelijk' id='speciaal_dieet_mogelijk' value='speciaal_dieet_mogelijk'>
        <label for 'speciaal_dieet_mogelijk'>" . __('Speciaal dieet mogelijk', 'student') . "</label><br>
		<input type='checkbox' name='geen_alcohol' id='geen_alcohol'   value='geen_alcohol'>
        <label for 'geen_alcohol'>" . __('Geen Alcohol', 'student') . "</label><br>
        <fieldset style='border: 1px solid silver;'>
        <legend>" . __('BIJDRAGE,', 'student') . "</legend>
		<input type='checkbox' name='gewenst_bedrag' id='gewenst_bedrag'  value='gewenst_bedrag'>
        <label for 'Gewenst Bedrag'>" . __('Gewenst Bedrag', 'student') . "</label>
        <input type='text' name='bedrag' id='bedrag' placeholder='0.00' value='bedrag'>
        </fieldset>
        </fieldset>
        </fieldset>
        <fieldset style='border: 1px solid silver;'>
        <legend>" . __('MEMBERS,', 'student') . "</legend>
        <fieldset style='border: 10px solid silver;'><div id='newProf'></div></fieldset>
        <label for 'firstname'>" . __('FIRSTNAME', 'student') . ": </label>
		<input type='text' name='firstname' id='firstname'  value='". $person_data->firstname() ."'> 
        <label for 'surname'>" . __('SURNAME', 'student') . ": </label>
		<input type='text' name='surname' id='surname'  value='". $person_data->surname() ."'>
        <label for 'birthdate'>" . __('BIRTHDATE', 'student') . ": </label>
		<input type='date' name='birthdate' id='birthdate' required value='" . $person_data->birthdate() . "'
			min='" . date('Y-m-d', strtotime('-200 years')) . "'
			max='" . date('Y-m-d', (time())) . "'>
        <label class='test' for 'introducee'>" . __('INTRODUCEE', 'student') . ": </label>
        <select name='introducee' id='introducee'>
            <option value='Hoofd'>Hoofd</option>
            <option value='Familie'>Familie</option>
            <option value='Huisgenoot'>Huisgenoot</option>
            <option value='Introducee'>Introducee</option>
        </select> 
        </fieldset>
        <button type ='button' id='button1'>+</button><br>
        </fieldset>
		<div style='clear: both;'></div>
		<input type='submit' value='" . $submit_button_text . "'>
	</form>";
        if ($validation_errors) {
			$js_array_errors = json_encode($validation_errors);
			$form .= "
<script type='text/javascript'>
	MarkValidationErrors(" . $js_array_errors . ");
</script>";
		}

		
		echo $form;
    
    }
}

?>