<?php
class Default_Form_Pacienti extends Zend_Form
{
	function init()
	{
            $this->setMethod('post');
            $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

            // BEGIN: Name
            $name = new Zend_Form_Element_Text('name');
            $name->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'Numele complet','id'=>'name'));
            $name->setRequired(true);
            $this->addElement($name);
            // END: Name

            $birth_date = new Zend_Form_Element_Text('birth_date');
            $birth_date->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'Data nasterii','id'=>'birth_date'));
            $birth_date->setRequired(true);
            $this->addElement($birth_date);

            $ocupation = new Zend_Form_Element_Text('ocupation');
            $ocupation->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'Ocupatie','id'=>'ocupation'));
            $ocupation->setRequired(true);
            $this->addElement($ocupation);

            $cnp = new Zend_Form_Element_Text('cnp');
            $cnp->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'CNP','id'=>'cnp'));
            $cnp->setRequired(true);
            $this->addElement($cnp);

            $ci = new Zend_Form_Element_Text('ci');
            $ci->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'CI','id'=>'ci'));
            $ci->setRequired(true);
            $this->addElement($ci);

            $phone_number = new Zend_Form_Element_Text('phone_number');
            $phone_number->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'Telefon','id'=>'phone_number'));
            $phone_number->setRequired(true);
            $this->addElement($phone_number);

            $address = new Zend_Form_Element_Text('address');
            $address->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'Adresa','id'=>'address'));
            $address->setRequired(true);
            $this->addElement($address);

            $age = new Zend_Form_Element_Text('age');
            $age->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'Varsta','id'=>'age'));
            $age->setRequired(true);
            $this->addElement($age);

            // BEGIN: Email
            $email = new Zend_Form_Element_Text('email');
            $email->setAttribs(array('class'=>'form-control validate[required,custom[email]]','placeholder'=>'Email','id'=>'email'));
            $validatorEmail = new Zend_Validate_Db_NoRecordExists('users', 'email');
//		$email->addValidator(new Zend_Validate_EmailAddress('users', 'email'));
            $validatorEmail->setExclude(array('field'=>'deleted', 'value'=>'1'));
            $email->addValidator($validatorEmail);
            $email->setRequired(true);
            $this->addElement($email);
            // END: Email

            $password = new Zend_Form_Element_Password('password');
            $password->setAttribs(array('class'=>'form-control validate[required]', 'placeholder'=>'Parola', 'maxlenght'=>'32', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
            $password->setRequired(true);
            $this->addElement($password);

            $retypePassword = new Zend_Form_Element_Password('retypePassword');
            $retypePassword->addValidator(new Zend_Validate_Identical('password'));
            $retypePassword->setAttribs(array('class'=>'form-control validate[equals[password]]', 'placeholder'=>'Rescrie parola', 'maxlenght'=>'32', 'autocomplete'=>'off', 'oncontextmenu'=>'return false', 'ondrop'=>'return false', 'onpaste'=>'return false'));
            $retypePassword->setRequired(true);
            $retypePassword->setIgnore(true);
            $this->addElement($retypePassword);


            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setValue('Adauga');
            $submit->setAttribs(array('class'=>'btn btn-primary'));
            $submit->setIgnore(true);
            $this->addElement($submit);
	}
	function edit(Default_Model_Users $model)
	{
            $this->name->setValue($model->getName());
            $this->name->setLabel('Nume');

            $this->birth_date->setValue(date('Y-m-d',$model->getBirth_date()));
            $this->birth_date->setLabel('Data nasterii');

            $this->age->setValue($model->getAge());
            $this->age->setLabel('Varsta');

            $this->ocupation->setValue($model->getOcupation());
            $this->ocupation->setLabel('Ocupatie');

            $this->address->setValue($model->getAddress());
            $this->address->setLabel('Adresa');

            $this->cnp->setValue($model->getCnp());
            $this->cnp->setLabel('CNP');

            $this->ci->setValue($model->getCi());
            $this->ci->setLabel('CI');

            $this->phone_number->setValue($model->getPhone_number());
            $this->phone_number->setLabel('Telefon');

            $this->email->setValue($model->getEmail());
            $this->email->setLabel('Email');

            $this->email->setAttribs(array('class'=>'form-control'));
            $emailValidateDbNotExists 		= $this->email->getValidator('Zend_Validate_Db_NoRecordExists');
            $emailValidateDbNotExists->setExclude(array('field'=>'email', 'value'=>$model->getEmail()));

            $this->submit->setValue('Salveaza modificarile');

            $this->removeElement('password');
            $this->removeElement('retypePassword');
	}
}