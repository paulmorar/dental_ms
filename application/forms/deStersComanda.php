<?php
class Default_Form_Comanda extends Zend_Form
{
    function init()
    { 
        $this->setMethod('post');
        $this->addAttribs(array('id'=>'comanda'));
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $idComanda = new Zend_Form_Element_Hidden('idComanda');
        $idComanda->setAttribs(array('class'=>'form-control ','id'=>'idComanda'));
        $this->addElement($idComanda);

        $client = new Zend_Form_Element_Text('client');
        $client->setAttribs(array('class'=>'form-control','placeholder'=>'Client','id'=>'client'));
        $this->addElement($client);
        
        $numar = new Zend_Form_Element_Text('numar');
        $numar->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'Numar comanda','id'=>'numarC'));
        $this->addElement($numar);
        
        //BEGIN:tip
        $tip = new Zend_Form_Element_Select('tip');

        $options= array(''=>'Selecteaza tipul comenzii');		
        $tipuri = new Default_Model_TipComanda();
        $select = $tipuri->getMapper()->getDbTable()->select()				
                                ->where('NOT deleted')
                                ->order('id DESC');
        $result = $tipuri->fetchAll($select);
        if(NULL != $result)
        {
            foreach($result as $value){
                $options[$value->getId()] = $value->getNume();
            }
        }
        $tip->addMultiOptions($options);
        $tip->addValidator(new Zend_Validate_InArray(array_keys($options)));
        $tip->setAttribs(array('class'=>'form-control validate[required]','id'=>'tip'));
        $this->addElement($tip);
        //END:tip


        $nume = new Zend_Form_Element_Text('nume');
        $nume->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'Nume','id'=>'nume'));
        $this->addElement($nume);

        $detalii = new Zend_Form_Element_Textarea('detalii');
        $detalii->setAttribs(array('class'=>'form-control validate[required]','placeholder'=>'Detalii','id'=>'detalii'));
        $this->addElement($detalii);

        //BEGIN:tip
        $departament = new Zend_Form_Element_Select('departament');

        $optionsDep = array(''=>'Selecteaza persoana/departament');		
        $roluri     = new Default_Model_Users();
        $selectDep = $roluri->getMapper()->getDbTable()->select()				
                                ->where('NOT deleted')
                                ->where('idRole <> 1')
                                ->order('id DESC');
        $resultDep = $roluri->fetchAll($selectDep);
        if(NULL != $resultDep)
        {
            foreach($resultDep as $value){
                $departamentUser    = Needs_Tools::getLevelById($value->getIdRole());
                $optionsDep[$value->getId()] = $value->getName().' - '.$departamentUser;
            }
        }
        $departament->addMultiOptions($optionsDep);
        $departament->addValidator(new Zend_Validate_InArray(array_keys($optionsDep)));
        $departament->setAttribs(array('class'=>'form-control','id'=>'departament'));
        $this->addElement($departament);
        //END:tip

        $detaliiInc = new Zend_Form_Element_Text('detalii_incomplet');
        $detaliiInc->setAttribs(array('class'=>'form-control','placeholder'=>'Comanda incompleta ?','id'=>'detalii_incomplet','name'=>'detalii_incomplet'));
        $this->addElement($detaliiInc);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue('Adauga comanda');
        $submit->setAttribs(array('class'=>'btn btn-primary'));
        $submit->setIgnore(true);
        $this->addElement($submit);
    }
    function edit(Default_Model_Comanda $model)
    {           
        $this->idComanda->setValue($model->getId());
        
        $this->numar->setValue($model->getNumar());
        $this->numar->setLabel('Numar comanda');

        
        $this->tip->setValue($model->getTip());

        $this->nume->setValue($model->getNume());
        $this->nume->setLabel('Nume');

        $this->detalii->setValue($model->getDetalii());
        $this->detalii->setLabel('Detalii');

        $departamentEdit     = new Default_Model_Users();
        $selectDep = $departamentEdit->getMapper()->getDbTable()->select()->from(array('r'=>'users'), array('*'))				
                                ->joinLeft(array('cd'=>'comanda_to_departament'), 'r.id=cd.departament_id',array())
                                ->where('NOT deleted')
                                ->where('cd.comanda_id  ='.$model->getId())
                                ->order('id DESC');
        $resultDep = $departamentEdit->fetchRow($selectDep);
        if($resultDep){
            $this->departament->setValue($resultDep->getId());
        }

        $this->detalii_incomplet->setValue($model->getDetalii_incomplet());
        $this->detalii_incomplet->setLabel('Comanda incompleta ?');


        $this->submit->setValue('Salveaza modificarile');

    }
}