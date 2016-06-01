<?php
class Default_Form_Albume extends Zend_Form
{
    function init()
    { 
            $this->setMethod('post');
            $this->addAttribs(array('id'=>'clienti'));
            $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

            $nume = new Zend_Form_Element_Text('nume');
            $nume->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'Nume album','id'=>'album'));
            $this->addElement($nume);

            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setValue('Adauga client');
            $submit->setAttribs(array('class'=>'btn btn-primary'));
            $submit->setIgnore(true);
            $this->addElement($submit);
    }
    function edit(Default_Model_Albume $model)
    {
            $this->nume->setValue($model->getNume());
            $this->nume->setLabel('Nume');

            $this->submit->setValue('Salveaza modificarile');

    }
}