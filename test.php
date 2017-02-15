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
        $(document).ready(function(){
               $("#button1").click(function(){
                $("ol").append("<li><select name='dag'></select> <select name='maaltijd'></select><button value='delete'</li>");
            });
        
            
    $(function(){
        
        function changeValues(){
            //alert("values changed");
            var sumMeals = 0;
            $('.maaltijd-prijs').each(function(i, obj) {
                sumMeals += $(this).val()*1;
            });
            var sumTickets = 0;
            $('.price').each(function(i, obj) {
                sumTickets += $(this).val()*1;
            });
            
            document.getElementById("totaal_tickets").value = sumTickets;
            document.getElementById("totaal_maaltijden").value = sumMeals;
            
            
            document.getElementById("totaal_prijs").value = (sumMeals + sumTickets);
        }
        
                var maxAppend = 0;
                $("#button1").click(function(){
                    if (maxAppend >= 250) return;
                    var newTicket = 
                    '<tr>                                                                                                                                                                                                                     <td>Dag:                                                                                                                                                                                                                    <select name = "Dag[]" class="">                                                                                                                                                                                                  <option value = "1"> Maandag </option>                                                                                                                                                                                <option value = "2"> Dinsdag </option>                                                                                                                                                                                <option value = "3"> Woensdag </option>                                                                                                                                                                                <option value = "4"> Donderdag </option>                                                                                                                                                                              <option value = "5"> Vrijdag </option>                                                                                                                                                                                <option value = "6"> Zaterdag </option>                                                                                                                                                                                <option value = "7"> Zondag </option>                                                                                                                                                                       </select>                                                                                                                                                                                                       </td>                                                                                                                                                                                                                 <td>                                                                                                                                                                                                                      <input type="text" name="[]" class="" value="" />                                                                                                                                                                   </td>                                                                                                                                                                                                                 <td> Maaltijd:                                                                                                                                                                                                                <select name = "[]" class="">                                                                                                                                                                                                  <option value = "1"> 1 </option>                                                                                                                                                                                      <option value = "2"> 2 </option>                                                                                                                                                                                      <option value = "3"> 3 </option>                                                                                                                                                                                      <option value = "4"> 4 </option>                                                                                                                                                                              </select>                                                                                                                                                                                                       </td>                                                                                                                                                                                                                 </td>                                                                                                                                                                                                                 <td>                                                                                                                                                                                                                      <input type="text" name="[]" class="" value=""/>                                                                                                                                                                   </td>                                                                                                                                                                                                                 <td>                                                                                                                                                                                                                      <button class = "delete" type = "button" >-</button>                                                                                                                                                               </td>                                                                                                                                                                                                             </tr><br>';
                    
                //$('#newTicket').append(newTicket); 
                     $('.bodyClass').append(newTicket);
                maxAppend++;
                    changeValues();
            });
            $(".bodyClass").delegate(".delete", "click", function(){
                $(this).parent().parent().remove();
                maxAppend--;
                changeValues();
            });
        
           $(".bodyClass").delegate(".dag", "change", function(){
                var newTicket = $(this).parent().parent();
                var prijs = newTicket.find(".dag option:selected").attr("ticket-prijs");
                newTicket.find(".price").val(prijs);
               changeValues();
           }); 
        
         $(".bodyClass").delegate(".maaltijd","change", function(){
                var newTicket = $(this).parent().parent();
                var prijs = newTicket.find(".maaltijd option:selected").attr("maaltijd-prijs");
                newTicket.find(".maaltijd-prijs").val(prijs);
             changeValues();
           }); 
        
        
        });
            
            });

        
        </script>
        </head>
        <body>       
            <form action='http://localhost/eetmee/wordpress/' method=''>
                <table>
                    <tbody class="bodyClass" >
                <tr>    
                    <button type = "button" id="button1">+</button><br>
                </tr>
                <tr>
                    <td>Test 1</td>
                    <td>
                        <input type="text" name="" value="" placeholder="Test" />
                    </td><br>
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
                    <td>gdgdgdg
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