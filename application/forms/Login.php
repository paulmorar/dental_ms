<?php
class Default_Form_Login extends Zend_Form 
{
	function init()
	{
        // Set the method for the display form to POST
        $this->setMethod('post');
		$this->addAttribs(array('id'=>'formLogin', 'class'=>'formAdd',));
        
        // Add an email element
        $this->addElement(
			'text', 'userEmail', array(
            'attribs'    => array('class'=>'form-control validate[required]','placeholder'=>'Utilizator'),                      
            'required'   => true          
        ));

        // Add an password element
        $this->addElement(
			'password', 'userPassword', array(
            'attribs'    => array('class'=>'form-control validate[required]','placeholder'=>'Parola'),
            'required'   => true
        ));

        $this->addElement(
			'submit', 'submit', array(
            'ignore'    => true,
            'attribs'   => array('class'=>'btn btn-primary'),
            'value'	=> 'Login'
        ));
	}
}
