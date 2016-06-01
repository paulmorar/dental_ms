<?php
class Default_Form_TipComanda extends Zend_Form
{
    function init()
    { 
            $this->setMethod('post');
            $this->addAttribs(array('id'=>'clienti'));
            $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

            $nume = new Zend_Form_Element_Text('nume');
            $nume->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'Tip comanda','id'=>'album'));
            $this->addElement($nume);

            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setValue('Adauga tip comanda');
            $submit->setAttribs(array('class'=>'btn btn-primary'));
            $submit->setIgnore(true);
            $this->addElement($submit);
    }
    function edit(Default_Model_TipComanda $model)
    {
            $this->nume->setValue($model->getNume());
            $this->nume->setLabel('Tip comanda');

            $this->submit->setValue('Salveaza modificarile');

    }
}