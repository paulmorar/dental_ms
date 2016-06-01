<?php
class Default_Form_Agents extends Zend_Form
{
	function init()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'addAgent', 'class'=>''));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
	
		// BEGIN: Agent
		$agent = new Zend_Form_Element_Text('agent');
		$agent->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'Nume si prenume','id'=>'agent'));
		$agent->setRequired(true);
		$this->addElement($agent);
		// END: Agent
         
                $car = new Zend_Form_Element_Select('car');
                $options = [''=>'Selecteaza autoturismul','1'=>'MM-01-DPO','2'=>'MM-02-DPO','3'=>'MM-03-DPO','4'=>'Autoturism personal'];
                $car->addMultiOptions($options);
                $car->addValidator(new Zend_Validate_InArray(array_keys($options)));
                $car->setAttribs(array('class'=>'form-control validate[required]','id'=>'car'));
		$car->setRequired(true);
		$this->addElement($car);                                     
                
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setValue('Adauga agent');
		$submit->setAttribs(array('class'=>'btn btn-primary'));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}
	function edit(Default_Model_Agents $model)
	{
		$this->agent->setValue($model->getAgent());
		$this->agent->setLabel('Agent');
		
		$this->car->setValue($model->getCar());
		$this->car->setLabel('Autoturism');

		$this->submit->setValue('Salveaza modificarile');
	}
}