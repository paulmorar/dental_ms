<?php
class ClientiController extends Zend_Controller_Action{
    
    public function init(){
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();                
    }

    public function indexAction()
    {
        $model = new Default_Model_Clients();
        $select = $model->getMapper()->getDbTable()->select()
                        ->from(array('c'=>'clienti'), array('*'))
                        ->where('NOT c.deleted')
                        ->order('c.created DESC');
        
        $result = $model->fetchAll($select);

        $this->view->result = $result;
    }

    public function addAction()
    {
        $model = new Default_Model_Clients();
        $form = new Default_Form_Clients();
        $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/clients/add-client.phtml'))));

        $this->view->formAddClient = $form;

        if($this->getRequest()->isPost())
        {			
            if($form->isValid($this->getRequest()->getPost())) 
            {
                $model->setOptions($form->getValues());
                $model->setId_nivel1($this->getRequest()->getPost('nivel1'));
                $model->setId_nivel2($this->getRequest()->getPost('nivel2'));
                if($model->save()) {                                
                   $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Clientul a fost adaugat cu succes."
                                                        . "</div>");
                } else {
                    
                     $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                        . "</div>");
                }

                $this->_redirect('/clienti');
            }
        }
    }

    public function editAction() 
    {
        $id = $this->getRequest()->getParam('id');
        $model = new Default_Model_Clients();
        
        if ($model->find($id)) {
            $form = new Default_Form_Clients();
            $form->edit($model);
            $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/clients/edit-client.phtml'))));
            $this->view->formEditClient = $form;

            if ($this->getRequest()->isPost()) {
                if ($form->isValid($this->getRequest()->getPost())) {
                    $model->setOptions($form->getValues());
                    $model->setId_nivel1($this->getRequest()->getPost('nivel1'));
                    $model->setId_nivel2($this->getRequest()->getPost('nivel2'));
                    if ($model->save()) {						
                            $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Clientul a fost editat cu succes."
                                                        . "</div>");
                    } else {
                            $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                        . "</div>");
                    }
                    $this->_redirect('/clienti');		
                }				
            }
        }
    }
    
    public function deleteAction() 
    {
        $id = $this->getRequest()->getParam('id');
        $model = new Default_Model_Clients();
        if ($model->find($id)) {
            if ($model->delete()) {
                    $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Clientul a fost sters cu succes."
                                                        . "</div>");
            } else {
                    $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                        . "</div>");
            }
            $this->_redirect('/clienti');	
        }
    }
}
