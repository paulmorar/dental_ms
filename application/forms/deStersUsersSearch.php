<?php
class Default_Form_UsersSearch extends Zend_Form
{
	function init()
	{
		$this->setMethod('post');
		$this->addAttribs(array('id'=>'filterForm', 'class'=>''));
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
	
		$action = new Zend_Form_Element_Hidden('action');
		$action->setValue('search');
		$this->addElement($action);
		
// BEGIN: Name
		$name = new Zend_Form_Element_Text('nameSearch');
		$name->setAttribs(array('class'=>'form-control','placeholder'=>'Nume','id'=>'nameSearch'));
		$name->setRequired(true);
		$this->addElement($name);
// END: Name

// BEGIN: Email
		$email = new Zend_Form_Element_Text('emailSearch');
		$email->setAttribs(array('class'=>'form-control','placeholder'=>'Email','id'=>'emailSearch'));
		$email->setRequired(true);
		$this->addElement($email);
// END: Email

//BEGIN:Level
		$idLevel = new Zend_Form_Element_Select('idRoleSearch');
		
		$options= array(''=>'Level');		
		$levels = new Default_Model_Role();
		$select = $levels->getMapper()->getDbTable()->select()				
												->where('NOT deleted')
												->order('id DESC');
		$result = $levels->fetchAll($select);
		if(NULL != $result)
		{
			foreach($result as $value){
				$options[$value->getId()] = $value->getName();
			}
		}
		$idLevel->addMultiOptions($options);
		$idLevel->addValidator(new Zend_Validate_InArray(array_keys($options)));
		$idLevel->setAttribs(array('class'=>'form-control','id'=>'idRoleSearch'));
		$idLevel->setRequired(false);
		$this->addElement($idLevel);
//END:Level
		
		$add = new Zend_Form_Element_Submit('search');
		$add->setValue('Cauta');
		$add->setAttribs(array('class'=>'btn btn-primary'));
		$add->setIgnore(true);
		$this->addElement($add);
	}
}