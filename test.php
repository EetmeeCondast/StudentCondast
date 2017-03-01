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
        <meta charset="utf-8">
        <title>A Simple Page with CKEditor</title>
        <!-- Make sure the path to CKEditor is correct. -->
        <script type="text/javascript" src="//cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>
    </head>
    <body>
        <form>
            <textarea name="editor1" id="editor1" rows="10" cols="80">
                This is my textarea to be replaced with CKEditor.
            </textarea>
            <script type="text/javascript" >
                // Replace the <textarea id="editor1"> with a CKEditor
                // instance, using default configuration.
                CKEDITOR.replace( 'editor1' );
            </script>
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