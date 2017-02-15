<?php
class Profiel3View {
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
<legend>" . __('Kenmerken', 'student') . "</legend>
	<form method='post' action='" . esc_url(admin_url('admin-post.php')) . "'>
		<input type='hidden' name='action' value='" . $action . "'>
		<input type='hidden' name='profile_id' value='" . $person_data->profile_id() . "'>
		<label for 'roken'>" . __('Roken', 'student') . ": </label>
		<select name='roken' id='roken'>
            <option value='Niet Roken'>Niet Roken</option>
            <option value='In het huis'>In het huis</option>
            <option value='Buiten'>Buiten</option>
        </select>
        </fieldset>
        <fieldset style='border: 1px solid silver;'>
        <legend>" . __('Huisdieren', 'student') . "</legend>
		<label for 'huisdier'>" . __('PERIODICITY', 'student') . ": </label>
		<select name='huisdier' id='huisdier'>
            <option value='Hond'>Hond</option>
            <option value='Kat'>Kat</option>
            <option value='Vis'>Vis</option>
            <option value='Hamster'>Hamster</option>
            <option value='Paard'>Paard</option>
        </select><br>
         <fieldset style='border: 1px solid silver;'>
		<input type='checkbox' name='in_het_huis' id='in_het_huis'  value='in_het_huis' >
        <label for 'in_het_huis'>" . __('In het huis', 'student') . " </label>
         </fieldset>
         <fieldset style='border: 1px solid silver;'>
		<input type='checkbox' name='buiten' id='buiten' value='buiten'>
        <label for 'buiten'>" . __('buiten', 'student') . " </label>
         </fieldset>
        <fieldset style='border: 1px solid silver;'>
		<input type='checkbox' name='klein' id='klein' value='klein'>
        <label for 'klein'>" . __('Klein', 'student') . "</label>
         </fieldset>
          <button type ='button' id='button1'>+</button><br>
        </fieldset>
        <fieldset style='border: 1px solid silver;'>
        <legend>" . __('Allergiën,', 'student') . "</legend>
		<label for 'allergie'>" . __('Allergie', 'student') . ": </label>
		<select name='allergie' id='allergie'>
            <option value='Geen'>Geen</option>
            <option value='Huisdieren'>Huisdieren</option>
            <option value='Voedsel'>Voedsel</option>
            <option value='Medicijnen'>Medicijnen</option>
            <option value='Leefomstandigheden'>Leefomstandigheden</option>
        </select><br><br>
        <button type ='button' id='button1'>+</button><br>
        </fieldset>
        
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