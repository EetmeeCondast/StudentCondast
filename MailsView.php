<?php
class MailsView {

	public static function form($mails) {
		$form = "
<fieldset style='border: 1px solid silver;'>
<legend>" . __('EXISTING_MAILS', 'student') . "</legend>";
		while ($event = $mails->next_mail()) {
			$form .= "
<form method='post' action='" . get_site_url() . "/update_mail'>
	<input type='hidden' name='event' value='" . $event["event"] . "'>
	<button class='no_button'>" . $event["event"] . " " . $event["email_subject"] . " 
	</button>
</form>";
		}
$form .= "	
</fieldset>";
		return $form;
	}
}

?>