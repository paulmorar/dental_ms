<?php
class Default_Form_Subscriber extends Zend_Form
{
    function init()
    {
        $this->setMethod('post');
        $this->addAttribs(array('id'=>'addSubscriber', 'class'=>''));
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $email = new Zend_Form_Element_Text('email');
        $email->setAttribs(array('class'=>'form-control validate[required,custom[email]]','placeholder'=>'Adresa e-mail','id'=>'email'));
        $validatorEmail = new Zend_Validate_Db_NoRecordExists('newsletter_subscribers', 'email');
//        $email->addValidator(new Zend_Validate_EmailAddress('newsletter_subscribers', 'email')); 
//        $validatorEmail->setExclude(array('field'=>'deleted', 'value'=>'1'));
        $email->addValidator($validatorEmail);
        $email->setRequired(true);
        $this->addElement($email);
  
        $submit = new Zend_Form_Element_Submit('submit_subscriber');
        $submit->setValue('Adauga abonat');
        $submit->setAttribs(array('class'=>'btn btn-primary'));
        $submit->setIgnore(true);
        $this->addElement($submit);
    }
}