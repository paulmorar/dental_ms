<?php
class Default_Form_EditPassword extends Zend_Form
{
	function editPassword()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'formAccountEditPassword', 'class'=>''));	

		$filters = array(new Zend_Filter_StringTrim(),new Zend_Filter_StripTags());
		$control = new Zend_Form_Element_Hidden('control');
		$control->setValue('editPassword');
		$this->addElement($control);

		$oldPassword = new Zend_Form_Element_Password('oldPassword');
		$oldPassword->setLabel(Zend_Registry::get('translate')->_('admin_administrators_old_password'));
		$validator = new Zend_Validate_StringLength(6,32);
		$validator->setMessage(
			'Parola trebuie sa contina intre 6 si 32 de caractere'
		);
		$oldPassword->addValidator($validator);
		$oldPassword->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'Old password', 'maxlenght'=>'32', 'autocomplete'=>'off', 'oncontextmenu'=>'return false'));
		$oldPassword->setRequired(true);
		$this->addElement($oldPassword);

		$password = new Zend_Form_Element_Password('password');
		$password->setLabel(Zend_Registry::get('translate')->_('admin_administrators_new_password'));
		$password->addValidator(new Zend_Validate_StringLength(6,32));
		$password->setAttribs(array('class'=>'form-control validate[required]', 'placeholder'=>'New password', 'maxlenght'=>'32', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
		$password->setRequired(true);
		$this->addElement($password);

		$retypePassword = new Zend_Form_Element_Password('retypePassword');
		$retypePassword->setLabel(Zend_Registry::get('translate')->_('admin_administrators_retype_new_password'));
		$retypePassword->addValidator(new Zend_Validate_Identical('password'));
		$retypePassword->addValidator(new Zend_Validate_StringLength(6,32));
		$retypePassword->setAttribs(array('class'=>'form-control validate[equals[password]]', 'placeholder'=>'Rewrite new password', 'maxlenght'=>'32', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
		$retypePassword->setRequired(true);
		$retypePassword->setIgnore(true);
		$this->addElement($retypePassword);

		$submit = new Zend_Form_Element_Submit('savePassword');
		$submit->setValue(Zend_Registry::get('translate')->_('apply_password'));
		$submit->setAttribs(array('class'=>'btn btn-primary'));
		$submit->setIgnore(true);
		$this->addElement($submit);
		$this->setElementFilters($filters);
	}
	
	function editUserPassword()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'formAccountEditPassword', 'class'=>''));	

		$filters = array(new Zend_Filter_StringTrim(),new Zend_Filter_StripTags());
		$control = new Zend_Form_Element_Hidden('control');
		$control->setValue('editPassword');
		$this->addElement($control);

		$oldPassword = new Zend_Form_Element_Password('oldPassword');
		$oldPassword->setLabel('Old password');
		$oldPassword->addValidator(new Zend_Validate_StringLength(6,32));
		$oldPassword->setAttribs(array('class'=>'form-control validate[required,minSize[6],maxSize[32]]','minlenght'=>'6', 'maxlenght'=>'32', 'placeholder'=>'Parola veche' ,'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'data-prompt-position'=>'topLeft:0'));
		$oldPassword->setRequired(true);
		$this->addElement($oldPassword);
		
		$password = new Zend_Form_Element_Password('password');
		$password->setLabel(Zend_Registry::get('translate')->_('admin_administrators_new_password'));
		$password->addValidator(new Zend_Validate_StringLength(6,32));
		$password->setAttribs(array('class'=>'form-control validate[required]', 'placeholder'=>'Parola noua', 'maxlenght'=>'32', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
		$password->setRequired(true);
		$this->addElement($password);

		$retypePassword = new Zend_Form_Element_Password('retypePassword');
		$retypePassword->setLabel(Zend_Registry::get('translate')->_('admin_administrators_retype_new_password'));
		$retypePassword->addValidator(new Zend_Validate_Identical('password'));
		$retypePassword->addValidator(new Zend_Validate_StringLength(6,32));
		$retypePassword->setAttribs(array('class'=>'form-control validate[required]', 'placeholder'=>'Rescrie parola noua', 'maxlenght'=>'32', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
		$retypePassword->setRequired(true);
		$retypePassword->setIgnore(true);
		$this->addElement($retypePassword);

		$submit = new Zend_Form_Element_Submit('savePassword');
		$submit->setValue(Zend_Registry::get('translate')->_('apply_password'));
		$submit->setAttribs(array('class'=>'btn btn-primary'));
		$submit->setIgnore(true);
		$this->addElement($submit);
		$this->setElementFilters($filters);
	}
}
?>
