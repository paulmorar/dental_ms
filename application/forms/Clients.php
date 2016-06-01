<?php
class Default_Form_Clients extends Zend_Form
{
    function init()
    { 
            $this->setMethod('post');
            $this->addAttribs(array('id'=>'clienti'));
            $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

            $nume = new Zend_Form_Element_Text('nume');
            $nume->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'Nume client','id'=>'client'));
            $this->addElement($nume);
            
            //BEGIN:judet
            $nivel1 = new Zend_Form_Element_Select('nivel1');

            $options= array(''=>'Selecteaza judetul');		
            $nivele = new Default_Model_Nivel1();
            $select = $nivele->getMapper()->getDbTable()->select()				
                                ->order('name ASC');
            $result = $nivele->fetchAll($select);
            
            if(NULL != $result)
            {
                foreach($result as $value){
                    $options[$value->getId_nivel1()] = $value->getName();
                }
            }
            $nivel1->addMultiOptions($options);
            $nivel1->setAttribs(array('class'=>'form-control validate[required]','id'=>'nivel1'));
            $this->addElement($nivel1);
            //END:judet
            //
            //BEGIN:oras
            $nivel2 = new Zend_Form_Element_Select('nivel2');
            $optionsLoc= array('0'=>'Selecteaza localitatea');
            $nivel2->addMultiOptions($optionsLoc);
            $nivel2->setRegisterInArrayValidator(false);
            $nivel2->setAttribs(array('class'=>'form-control','id'=>'nivel2','name'=>'nivel2'));
            $this->addElement($nivel2);
            //END:oras
            
            $telefon = new Zend_Form_Element_Text('telefon');
            $telefon->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'Telefon','id'=>'telefon'));
            $this->addElement($telefon);

            $email = new Zend_Form_Element_Text('email');
            $email->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'Email','id'=>'email'));
            $this->addElement($email);

            $adresa = new Zend_Form_Element_Text('adresa');
            $adresa->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'Adresa','id'=>'adresa'));
            $this->addElement($adresa);

            $firma = new Zend_Form_Element_Text('firma');
            $firma->setAttribs(array('class'=>'form-control','placeholder'=>'Firma','id'=>'client'));
            $this->addElement($firma);

            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setValue('Adauga client');
            $submit->setAttribs(array('class'=>'btn btn-primary'));
            $submit->setIgnore(true);
            $this->addElement($submit);
    }
    function edit(Default_Model_Clients $model)
    {
            $this->nume->setValue($model->getNume());
            $this->nume->setLabel('Nume');
            
            $this->nivel1->setValue($model->getId_nivel1());
            
            $nivele = new Default_Model_Nivel2();
            $select = $nivele->getMapper()->getDbTable()->select()				
                                ->where('id_nivel1 = '.$model->getId_nivel1())
                                ->order('name ASC');
            $result = $nivele->fetchAll($select);
            $optionsLoc= array('0'=>'Selecteaza localitatea');
            if(NULL != $result)
            {
                foreach($result as $value){
                    $optionsLoc[$value->getId_nivel2()] = $value->getName();
                }
            }
            
            $this->nivel2->addMultiOptions($optionsLoc);
            $this->nivel2->setValue($model->getId_nivel2());

            $this->telefon->setValue($model->getTelefon());
            $this->telefon->setLabel('Telefon');

            $this->email->setValue($model->getEmail());
            $this->email->setLabel('Email');

            $this->adresa->setValue($model->getAdresa());
            $this->adresa->setLabel('Adresa');

            $this->firma->setValue($model->getFirma());
            $this->firma->setLabel('Firma');

            $this->submit->setValue('Salveaza modificarile');

    }
}