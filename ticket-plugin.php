<?php
    ob_start();
    /*
    Plugin Name: ticket plugin
    Version: 1.0
    Author: Mitchell van der Woude
    Author URI: https://github.com/Mitchfire1997
    Description: Dit wordt een ticket form
    Text Domain: Test.nl
    */

    function form_ticket_shortcode()
    {
        date_default_timezone_set("Europe/Amsterdam");
        global $wpdb;  
        session_start();
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
                    '<tr>                                                                                                                                                                                                                       <td>Type: <select name = "dag[]" class="dag"> <option value = "1" ticket-prijs = "45" > Vrijdag </option> <option value = "2" ticket-prijs = "60"  > Zaterdag </option> <option value = "3" ticket-prijs = "30"> Zondag </option> <option value = "5" ticket-prijs = "80"> Weekend </option><option value = "4" ticket-prijs = "100"> Passe-partout </option> </select>                                                                                                                                           </td> <td><input type="text" name="prijs-ticket[]" class="price" value="45" /> </td>                                                                                                                                                                                                                        <td> Maaltijd: <select name = "maaltijd[]" class="maaltijd"><option value = "1" maaltijd-prijs = "0"> geen </option> <option value = "2" maaltijd-prijs = "20" > Lunch </option> <option value = "3" maaltijd-prijs = "30"> Diner </option> <option value = "4" maaltijd-prijs = "50"> Lunch en Diner </option> </select>               </td>  </td> <td><input type="text" name="prijs-maaltijd[]" class="maaltijd-prijs" value="0" readonly/> </td>                                                                                                                                                                                                                <td> <button class = "delete" type = "button" > Verwijder Ticket </button></td>                                                                                                                                                                                                   </tr><br>';
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
            <form action='http://localhost/conferentie/wordpress/ticket' method='post'>
                <table>
                    <tbody class="bodyClass" >
                <tr>    
                    <button type = "button" id="button1">Ticket toevoegen</button><br>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><input type="text" name="email" value="<?php echo $_SESSION['email']; ?>" placeholder="geef hier je emailadres" readonly/></td><br>
                </tr>
                <tr>                                                                                                                                                                                                                      <td>Type: <select name = "dag[]" class="dag"> <option value = "1" ticket-prijs = "45" > Vrijdag </option> <option value = "2" ticket-prijs = "60" > Zaterdag </option> <option value = "3" ticket-prijs = "30"> Zondag </option> <option value = "5" ticket-prijs = "80">Weekend </option><option value = "4" ticket-prijs = "100"> Passe-partout </option> </select>                                                                                                                                </td> 
               <td><input type="text" name="prijs-ticket[]" class="price" value="45" /> </td>                                                                                                                                                                  <td> Maaltijd: <select name = "maaltijd[]" class="maaltijd"><option value = "1" maaltijd-prijs = "0"> geen </option> <option value = "2" maaltijd-prijs = "20" > Lunch </option> <option value = "3" maaltijd-prijs = "30"> Diner </option> <option value = "4" maaltijd-prijs = "50">                Lunch en Diner </option> </select></td>  </td> <td><input type="text" name="prijs-maaltijd[]" value="0" class="maaltijd-prijs" /> </td><br>       
            </tr>          
                <tr>
                     <div id='newTicket'></div>
                </tr> 
                <tfoot>
                    <tr>
                        <td>Totaal Tickets:</td><td>Totaal Maaltijd:</td><td>Totaal prijs:</td>                        
                    </tr>
                    <tr>
                        <td><input type="text" id="totaal_tickets" name="totaal_tickets" value="45" readonly/></td>
                        <td><input type="text" id="totaal_maaltijden" name="totaal_maaltijden" value="0" readonly/></td>
                        <td><input type="text" id="totaal_prijs" name="totaal_prijs" value="45" readonly/></td>
                    </tr>
                <tr>
                <td>Betaalmethode<select name="betaalmethode"><option value="IDEAL">IDEAL</option>
                                                              <option value="PAYPAL">PAYPAL</option>
                                                              <option value="CREDITCARD">CREDITCARD</option>
                    </select>
                </td>
            </tr>  
        <tr>
        <td><input type='submit' name='submit' value='Volgende'/></td>
        </tr>
                    </tfoot>
                </tbody>
            </table>
        </body>
</html>

     <?php
        
if(isset($_POST["submit"]))
        {   
    
    $user = $wpdb->get_var("SELECT MAX(id) FROM user");
    $name = $wpdb->get_var("SELECT voornaam, tussenvoegsel, achternaam FROM user where id = $user");
    $tussenvoegsel = $wpdb->get_var("SELECT tussenvoegsel, achternaam FROM user where id = $user");
    $achternaam = $wpdb->get_var("SELECT achternaam FROM user where id = $user");
    $totalePrijs = $wpdb->get_var("SELECT prijs_totaal FROM reservering where user = $user");
      
    $wpdb->insert( 
    'reservering', 
    array( 
        'user' => $user,
        'betaalmethode' => $_POST["betaalmethode"],
        'prijs_totaal' => $_POST["totaal_prijs"]
    )
    
);
    
    
   $newTicket = [];    
    for($i = 0;  $i< count($_POST["dag"]); $i++){
    
    
        

    $reservering = $wpdb->get_var("SELECT MAX(id) FROM reservering"); 
    
     $wpdb->insert( 
    'ticket', 
    array( 
        'streepjescode' => md5(uniqid(rand(), true)), 
        'reservering' => $reservering,
        'soort' => $_POST["dag"][$i]
    )
    
);
    }
    
    $newMaaltijd = [];    
    for($i = 0;  $i< count($_POST["maaltijd"]); $i++){
     $wpdb->insert( 
    'maaltijd', 
    array( 
        'streepjescode' => md5(uniqid(rand(), true)), 
        'reservering' => $reservering,
        'soort' => $_POST["maaltijd"][$i]
    )
    
);
    
    }
      ?>
<!DOCTYPE html>
<html>
<body>

<?php 
        


$to  = "$_GET(email)"; 


// subject
$subject = 'Order Tickets Conferentie Games';

// message
$message = '
<html>
<head>
  <title>Welcome to the conferentie of games.</title>
</head>
<body>
 Message for the mail of tickets.   <br>
 ReserveringID: '.$user.' <br>
 Name: '.$name.' '.$tussenvoegsel.' '.$achternaam.' <br>
 Ticket(s):  <br>
 Meal(s):      <br>
 Barcode Ticket:   <br> 
 Barcode Meal: <br>
 Totale Prijs: '.$totalePrijs.'<br>
 </body>
</html>
';

// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
$headers .= 'To: '.$_SESSION["email"].' <'.$_SESSION["email"].'>' . "\r\n";
$headers .= 'From: Conferentie <conferentie1234@gmail.com>' . "\r\n";

        mail($to, $subject, $message, $headers);
        
       
?>

</body>
</html>
<?php
       $output = "<table>
                    <tr>
                        <td>Ticket:</td>
                        <td>Prijs:</td>
                    </tr>
                    <tr>
                        <td>Vrijdag</td>
                        <td>€45</td>
                    </tr>
                    <tr>
                        <td>Zaterdag</td>
                        <td>€60</td>
                    </tr>
                    <tr>  
                       
                        <td>Zondag</td>
                        <td>€30</td>
                    </tr>
                    <tr>  
                        
                        <td>Weekend</td>
                        <td>€80</td>
                    </tr>
                    <tr>  
                       
                        <td>Passe-partout</td>
                        <td>€100</td>
                    </tr>
                   <tr>
                        <td></td>
                   </tr>
                    </form>
                    </table>";
        
        return $output;
    

    }
        

    }
       

    function form_ticket_register_shortcode()
    {
        add_shortcode('form-ticket','form_ticket_shortcode');
    }

    add_action('init', 'form_ticket_register_shortcode');
?>
