<?php

$csvfn = "subscribed_people.csv";

require_once("phorms/phorms.php");
require_once("ReCAPTCHA_Widget.class.php");
require_once("ReCAPTCHA_Field.class.php");
require_once("i10n.class.php");

/* Check that the address provided isn't insane */

function sane_address($value) {
	 if (strlen($value) > 255) {
		throw new Phorm_ValidationError(_i10n_get("The address provided was too long"));
	 }
}

class SubscribeForm extends Phorm_Phorm {
	protected function define_fields() {
		$this->name = new Phorm_Field_Text(_i10n_get("Your Name"), 40, 255, array('required'));
		$this->email = new Phorm_Field_Email(_i10n_get("Email Address"), 40, 255, array('required'));
		$this->phone = new Phorm_Field_Text(_i10n_get("Phone Number"), 40, 255);
		$this->school_name = new Phorm_Field_Text(_i10n_get("School Name"), 40, 255, array('required'));
		$this->school_address = new Phorm_Field_TextArea(_i10n_get("School Address"), 5, 40, array('required', 'sane_address'));
		$this->more_teams = new Phorm_Field_CheckBox(_i10n_get("Two teams?", "Would you like to enter two teams if there is free space?"));
		$this->captcha = new Phorm_Field_ReCAPTCHA(_i10n_get("Please enter the text shown in the image below"));

		$this->phone->help_text(_i10n_get("School Extension or Mobile"));
	}
}

$form = new SubscribeForm();

if ($form->bound and $form->is_valid()) {
	if (($csvfile = fopen($csvfn, 'a')) !== False) {
		$data = array($form->name->get_value(), $form->email->get_value(), $form->phone->get_value(),
	                      $form->school_name->get_value(), $form->school_address->get_value(),
		              $form->more_teams->get_value() ? '1':'0');
		fputcsv($csvfile, $data);

		/* Tell us about it */
		$fd = fopen("/tmp/hash-srobo", "a");
		fwrite($fd, $form->school_name->get_value() . " has just signed up to SR2012!\n");
		fclose($fd);
	}
}

?>
