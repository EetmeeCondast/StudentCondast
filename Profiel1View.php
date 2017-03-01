<?php
class Profiel1View {
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
        <script type="text/javascript" src="//cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>
        <script>
            
  
           function myFunction(){
            $(document).ready(function() {
   $(".bodyClass").find("tr:gt(6)").remove();
});
           }
        
       
        $(document).ready(function(){
               $("#button1").click(function(){
                $("ol").append("<li><select name='dag'></select> <select name='maaltijd'></select><button value='delete'</li>");
            });
       
    $(function(){
        
        
                var maxAppend = 0;
                $("#button1").click(function(){
                    if (maxAppend >= 250) return;
                    var newTicket = 
                    '<tr>                                                                                                                                                                                                                     <td>Mealtype: <br>                                                                                                                                                                                                                   <select name = "Mealtype[]" class="">                                                                                                                                                                                                  <option value = "1"> Middageten </option>                                                                                                                                                                              <option value = "2"> Avondeten </option>                                                                                                                                                                                                                                                                                                                                                                                    </select>                                                                                                                                                                                                    </td>                                                                                                                                                                                                            </tr><tr>                                                                                                                                                                                                                         <td>Startdate:                                                                                                                                                                                                            <input type="date" name="startdate" class="" value=""/>                                                                                                                                                             </td> </tr><tr>                                                                                                                                                                                                                <td>Enddate:                                                                                                                                                                                                              <input type="date" name="enddate" class="" value=""/>                                                                                                                                                               </td></tr><tr>                                                                                                                                                                                                                 <td>Periodicity:                                                                                                                                                                                                          <input type="number" name="periodicity" class="" min="0" value=""/>                                                                                                                                                 </td></tr><tr>                                                                                                                                                                                                                 <td>Days: <br>                                                                                                                                                                                                            <input type="checkbox" name="day" value="Monday">Ma                                                                                                                                                                    <input type="checkbox" name="day" value="Tuesday">Di                                                                                                                                                                  <input type="checkbox" name="day" value="Wednesday">Wo                                                                                                                                                                <input type="checkbox" name="day" value="Thursday">Do                                                                                                                                                                  <input type="checkbox" name="day" value="Friday">Vr                                                                                                                                                                    <input type="checkbox" name="day" value="Saturday">Za                                                                                                                                                                  <input type="checkbox" name="day" value="Sunday">Zo                                                                                                                                                                  </td></tr>                                                                                                                                                                                                                                                                                                                                                                                                                             <br>';
                       //$('#newTicket').append(newTicket); 
                     $('.bodyClass').append(newTicket);
                maxAppend++;
                    
            });
           
        });
     });
        
              function myFunction1(){
            $(document).ready(function() {
   $(".bodyClass3").find("tr:gt(5)").remove();
});
           }
            $(document).ready(function(){
                    $("#button2").click(function(){
                        $("ol").append("<li><select name='dag'></select> <select name='maaltijd'></select><button value='delete'</li>");
            });
       
    $(function(){
        
        
                var maxAppend = 0;
                $("#button2").click(function(){
                    if (maxAppend >= 250) return;
                    var newMember = 
                    '<tr>                                                                                                                                                                                                                     <td>Firstname: <br>                                                                                                                                                                                                              <input type="text" name="" value="" placeholder="Vul hier u voornaam in">                                                                                                                                   </td>                                                                                                                                                                                                              </tr>                                                                                                                                                                                                                  <tr>                                                                                                                                                                                                                     <td>Infix:<br>                                                                                                                                                                                                                    <input type="text" name="" value="" placeholder="Vul hier u tussenvoegsel in"/>                                                                                                                             </td>                                                                                                                                                                                                              </tr>                                                                                                                                                                                                                  <tr>                                                                                                                                                                                                                     <td>Surname:<br>                                                                                                                                                                                                                  <input type="text" name="" value="" placeholder="Vul hier u achternaam in"/>                                                                                                                               </td>                                                                                                                                                                                                              </tr>                                                                                                                                                                                                                  <tr>                                                                                                                                                                                                                     <td>Birthdate:<br>                                                                                                                                                                                                                <input type="date" name="" value=""/>                                                                                                                                                                       </td>                                                                                                                                                                                                              </tr>                                                                                                                                                                                                                  <tr>                                                                                                                                                                                                                     <td>Introducee: <br>                                                                                                                                                                                                              <select name="Introducee" class="">                                                                                                                                                                                           <option value="">Hoofd</option>                                                                                                                                                                                       <option value="">Familie</option>                                                                                                                                                                                     <option value="">Huisgenoot</option>                                                                                                                                                                                   <option value="">Introducee</option>                                                                                                                                                                 </td></tr>                                                                                                                                                                                                                                                                                                                                                                                                                             <br>';
                    
                //$('#newTicket').append(newTicket); 
                     $('.bodyClass3').append(newMember);
                maxAppend++;
                    
            });
            $(".bodyClass3").delegate(".delete", "click", function(){
                $(this).parent().parent().remove();
                maxAppend--;
                
            });
        
        
        });
            
            });

        
        </script>
        </head>
        <body>       
            <form action='http://localhost/eetmee/wordpress/' method=''>
                <table id="myTable">
                    <tbody class="bodyClass" >
                        <td><button type = "button" id="button1">+</button></td>
                 <tr> 
                    <td>Mealtype:<br> 
                        <select name = "Mealtype[]" class=""> 
                            <option value = "1" > Middageten </option> 
                            <option value = "2" > Avondeten </option> 
                        </select>                                                                                         
                    </td>
                </tr>
                <tr>
                    <td>Startdate:
                        <input type="date" name="startdate" class="" value="" /> 
                    </td>
                </tr>
                <tr>
                    <td>Enddate:
                        <input type="date" name="enddate" class="" value="" /> 
                    </td> 
                </tr>
                <tr>
                    <td>Periodicity:
                        <input type="number" name="periodicity" class="" min="0" value=""/>
                    </td>       
                </tr>
                <tr> 
                    <td>Days:<br> 
                        <input type="checkbox" name="day" value="Monday">Ma
                        <input type="checkbox" name="day" value="Tuesday">Di
                        <input type="checkbox" name="day" value="Wednesday">Wo
                        <input type="checkbox" name="day" value="Thursday">Do
                        <input type="checkbox" name="day" value="Friday">Vr
                        <input type="checkbox" name="day" value="Saturday">Za
                        <input type="checkbox" name="day" value="Sunday">Zo
                    </td>
                </tr>  
                
                     <div id='newTicket'></div>
           
            </tbody>
            <tbody class="bodyClass2">
                    <td><button a href="#?" onclick="myFunction(); return false">-</button></td><br><br>
            </tbody>
            <tbody class="bodyClass3">
                <td><button type = "button" id="button2">+</button></td>
                 <tr>
                     <td>Firstname:<br>
                        <input type="text" name="" value="" placeholder="Vul hier u voornaam in">
                     </td>
                </tr>
                <tr>
                    <td>Infix:<br>
                        <input type="text" name="" value="" placeholder="Vul hier u tussenvoegsel in">
                    </td>
                </tr>
                <tr>
                    <td>Surname:<br>
                        <input type="text" name="" value="" placeholder="Vul hier u achternaam in">
                    </td>
                </tr>
                <tr>
                    <td>Birthdate:<br>
                        <input type="date" name="" value="">
                    </td>
                </tr>
                <tr>
                    <td>Introducee:<br>
                        <select name = "Introducee" class="">
                                <option value = "1"> Hoofd </option>
                                <option value = "2"> Familie </option>
                                <option value = "3"> Huisgenoot </option>
                                <option value = "4"> Introducee </option>
                        </select>   
                    </td>
                </tr>
                    <div id="newMember"></div>
            </tbody>
            <tbody class="bodyClass4">
                    <td><button a href="#?" onclick="myFunction1(); return false">-</button></td><br><br>
            </tbody>
            <tfoot>
            <tbody class="bodyClass5">   
                <td>Opmerkingen:
                       <textarea name="Opmerkingen" id="Opmerkingen" rows="10" cols="80">
                       </textarea>
            <script type="text/javascript" >
                // Replace the <textarea id="editor1"> with a CKEditor
                // instance, using default configuration.
                CKEDITOR.replace( 'Opmerkingen' );
            </script>
                </td>
            </tbody>    
                <tr>   
                    <td><input type='submit' name='submit' value='Volgende'/></td>
                </tr>
            </tfoot>        
            </table>
            </form>
    </body>
</html>
<?php

    }
}
?>