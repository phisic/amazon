<?php

class WatchForm extends CFormModel
{
	public $firstname;
	public $email;

	public function rules()
	{
		return array(
			array('email, firstname', 'required'),
			array('email', 'email'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'firstname'=>'First name',
		);
	}
}
