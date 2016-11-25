# student
For this moment just a WordPress plugin for inserting forms via shortcodes and handling them.
Includes use of MySQL database and internationalization.
  
/wp-content/plugins/student/  
&nbsp;&nbsp;PersonFind.php  
&nbsp;&nbsp;PersonFindAction.php  
&nbsp;&nbsp;PersonCreate.php  
&nbsp;&nbsp;PersonCreateAction.php  
&nbsp;&nbsp;PersonUpdate.php  
&nbsp;&nbsp;PersonUpdateAction.php  
&nbsp;&nbsp;PersonLookupModel.php  
&nbsp;&nbsp;PersonLookupView.php  
&nbsp;&nbsp;PersonModel.php  
&nbsp;&nbsp;PersonView.php  
&nbsp;&nbsp;PersonMessages.php  
&nbsp;&nbsp;utilities.php  
&nbsp;&nbsp;form.js  
&nbsp;&nbsp;style.css  
  
/wp-content/languages/  
&nbsp;&nbsp;student-en_US.po  
&nbsp;&nbsp;student-nl_NL.po  
  
Internationalization:
Only works with WordPress >= 4.6.1. .po files must be compiled into .mo files. Resulting .mo files must be added to /wp-content/languages/plugins/ directory. If needed: in wp-config.php: define ('WPLANG', 'nl_NL');
  

