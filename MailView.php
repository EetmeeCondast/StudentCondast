<?php
class MailView {
	public static function form($mail_data, $action) {
		$submit_button_text = "";
		switch($action) {
			case "create_mail_action":
				$submit_button_text = __('CREATE', 'student');
				break;
			case "update_mail_action":
				$submit_button_text = __('UPDATE', 'student');
				break;
		}
		$form = "
<fieldset style='border: 1px solid silver;'>
<legend>" . __('MAIL', 'student') . "</legend>
	<form method='post' action='" . esc_url(admin_url('admin-post.php')) . "'>
		<input type='hidden' name='action' value='" . $action . "'>
		<input type='text' name='event' value='" . $mail_data->event() . "'>
		<input type='text' name='email_subject' value='" . $mail_data->email_subject() . "'>
		<textarea id='ckeditor' name='email_text' rows='20'>" . $mail_data->email_text() . "</textarea>
			<script>
				CKEDITOR.replace('ckeditor', { language: 'nl' });
			</script>
		</textarea>
		<input type='submit' value='" . $submit_button_text . "'>
	</form>
</fieldset>";
		return $form;
	}
}

?>
