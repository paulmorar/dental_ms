<?php
class Default_Form_ForgotPassword extends Zend_Form 
{
	function init()
	{
        // Set the method for the display form to POST
        $this->setMethod('post');
		$this->addAttribs(array('id'=>'formLogin', 'class'=>''));
        
        // Add an email element
        $this->addElement(
			'text', 'email', array(
            'required'   => true,          
            'attribs'    => array('class'=>'form-control required','id'=>'email','placeholder'=>'Email address'),            
        ));

        $this->addElement(
			'submit', 'submit', array(
            'ignore'   => true,
            'attribs'    => array('class'=>'btn btn-primary'),
            'label'    => 'Submit',
			'value'		=> 'Submit'
        ));

	}
}