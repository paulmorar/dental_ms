<?php
class TipComandaController extends Zend_Controller_Action{
    
    public function init(){
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();                
    }

    public function indexAction()
    {
        $model = new Default_Model_TipComanda();
        $select = $model->getMapper()->getDbTable()->select()
                        ->from(array('t'=>'tip_comanda'), array('*'))
                        ->where('NOT t.deleted')
                        ->order('t.created DESC');
        
        $result = $model->fetchAll($select);

        $this->view->result = $result;
    }

    public function addAction()
    {
        $model = new Default_Model_TipComanda();
        $form = new Default_Form_TipComanda();
        $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/tip-comanda/add.phtml'))));

        $this->view->formAddAlbum = $form;

        if($this->getRequest()->isPost())
        {			
            if($form->isValid($this->getRequest()->getPost())) 
            {
                $model->setOptions($form->getValues());
                if($model->save()) {                                
                   $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Tipul de comanda a fost adaugat cu succes."
                                                        . "</div>");
                } else {
                    
                     $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                        . "</div>");
                }

                $this->_redirect('/tip-comanda');
            }
        }
    }

    public function editAction() 
    {
        $id = $this->getRequest()->getParam('id');
        $model = new Default_Model_TipComanda();
        
        if ($model->find($id)) {
            $form = new Default_Form_TipComanda();
            $form->edit($model);
            $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/tip-comanda/edit.phtml'))));
            $this->view->formEditClient = $form;

            if ($this->getRequest()->isPost()) {
                if ($form->isValid($this->getRequest()->getPost())) {
                    $model->setOptions($form->getValues());
                    if ($model->save()) {						
                            $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Tipul de comanda a fost editat cu succes."
                                                        . "</div>");
                    } else {
                            $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                        . "</div>");
                    }
                    $this->_redirect('/tip-comanda');		
                }				
            }
        }
    }
    
    public function deleteAction() 
    {
        $id = $this->getRequest()->getParam('id');
        $model = new Default_Model_TipComanda();
        if ($model->find($id)) {
            if ($model->delete()) {
                    $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Tipul de comanda a fost sters cu succes."
                                                        . "</div>");
            } else {
                    $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                        . "</div>");
            }
            $this->_redirect('/tip-comanda');	
        }
    }
}
