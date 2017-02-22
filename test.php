<?php
/*
Plugin Name: test 
Description: Plugin + knop "
Text Domain: student
*/
function test_knop()
{
?>
<!DOCTYPE html>
    <html>
        <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script>
            function myFunction() {
    document.getElementById("myTable").deleteRow(3);
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
                    '<tr>                                                                                                                                                                                                                     <td>Dag:                                                                                                                                                                                                                    <select name = "Dag[]" class="">                                                                                                                                                                                                  <option value = "1"> Maandag </option>                                                                                                                                                                                <option value = "2"> Dinsdag </option>                                                                                                                                                                                <option value = "3"> Woensdag </option>                                                                                                                                                                                <option value = "4"> Donderdag </option>                                                                                                                                                                              <option value = "5"> Vrijdag </option>                                                                                                                                                                                <option value = "6"> Zaterdag </option>                                                                                                                                                                                <option value = "7"> Zondag </option>                                                                                                                                                                       </select>                                                                                                                                                                                                       </td></tr>                                                                                                                                                                                                                <tr><td>                                                                                                                                                                                                                      <input type="text" name="[]" class="" value="" />                                                                                                                                                                   </td>                                                                                                                                                                                                                 <td> Maaltijd:                                                                                                                                                                                                                <select name = "[]" class="">                                                                                                                                                                                                  <option value = "1"> 1 </option>                                                                                                                                                                                      <option value = "2"> 2 </option>                                                                                                                                                                                      <option value = "3"> 3 </option>                                                                                                                                                                                      <option value = "4"> 4 </option>                                                                                                                                                                              </select>                                                                                                                                                                                                       </td>                                                                                                                                                                                                                 </td>                                                                                                                                                                                                                 <td>                                                                                                                                                                                                                      <input type="text" name="[]" class="" value=""/>                                                                                                                                                                   </td>                                                                                                                                                                                                                 <td>                                                                                                                                                                                                                   </td>                                                                                                                                                                                                             </tr><br>';
                    
                //$('#newTicket').append(newTicket); 
                     $('.bodyClass').append(newTicket);
                maxAppend++;
                    
            });
        
        
        });
            
            });

        
        </script>
        </head>
        <body>       
            <form action='http://localhost/eetmee/wordpress/' method=''>
                <table id="myTable">
                    <tbody class="bodyClass" >
                <tr>    
                    <button type = "button" id="button1">+</button><br>
                </tr>
                <tr> 
                    <td>Dag: 
                        <select name = "Dag[]" class=""> 
                            <option value = "1" > Maandag </option> 
                            <option value = "2" > Dinsdag </option> 
                            <option value = "3" > Woensdag </option> 
                            <option value = "4" > Donderdag </option>
                            <option value = "5" > Vrijdag </option> 
                            <option value = "6" > Zaterdag </option> 
                            <option value = "7" > Zondag </option> 
                        </select>                                                                                                                                
                    </td> 
                    <td>
                        <input type="text" name="[]" class="" value="" /> 
                    </td> 
                    <td>Test 2:
                        <select name = "[]" class="">
                            <option value = "1" > 1 </option> 
                            <option value = "2" > 2 </option> 
                            <option value = "3" > 3 </option> 
                            <option value = "4" > 4 </option> 
                        </select>
                    </td>
                    <td>
                        <input type="text" name="[]" value="" class="" /> 
                    </td><br>       
                </tr>          
                <tr>
                     <div id='newTicket'></div>
                    <button a href="#?" onclick="myFunction(); return false">-</button>
                </tr> 
            <tfoot>
                <tr>
                       
                    <td><input type='submit' name='submit' value='Volgende'/></td>
                </tr>
            </tfoot>
                    
            </tbody>
            </table>
            </form>
    </body>
</html>
    <?php
}
function test_knop_shortcode()
{
add_shortcode("test_knop","test_knop");                
}
add_action('init','test_knop_shortcode');  
?>