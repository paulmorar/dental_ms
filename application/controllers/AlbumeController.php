<?php
class AlbumeController extends Zend_Controller_Action{
    
    public function init(){
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();                
    }

    public function indexAction()
    {
        $model = new Default_Model_Albume();
        $select = $model->getMapper()->getDbTable()->select()
                        ->from(array('a'=>'albume'), array('*'))
                        ->where('NOT a.deleted')
                        ->order('a.created DESC');
        
        $result = $model->fetchAll($select);

        $this->view->result = $result;
    }

    public function addAction()
    {
        $model = new Default_Model_Albume();
        $form = new Default_Form_Albume();
        $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/albume/add.phtml'))));

        $this->view->formAddAlbum = $form;

        if($this->getRequest()->isPost())
        {			
            if($form->isValid($this->getRequest()->getPost())) 
            {
                $model->setOptions($form->getValues());
                if($model->save()) {                                
                   $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Albumul a fost adaugat cu succes."
                                                        . "</div>");
                } else {
                    
                     $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                        . "</div>");
                }

                $this->_redirect('/albume');
            }
        }
    }

    public function editAction() 
    {
        $id = $this->getRequest()->getParam('id');
        $model = new Default_Model_Albume();
        
        if ($model->find($id)) {
            $form = new Default_Form_Albume();
            $form->edit($model);
            $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/albume/edit.phtml'))));
            $this->view->formEditClient = $form;

            if ($this->getRequest()->isPost()) {
                if ($form->isValid($this->getRequest()->getPost())) {
                    $model->setOptions($form->getValues());
                    if ($model->save()) {						
                            $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Albumul a fost editat cu succes."
                                                        . "</div>");
                    } else {
                            $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                        . "</div>");
                    }
                    $this->_redirect('/albume');		
                }				
            }
        }
    }
    
    public function deleteAction() 
    {
        $id = $this->getRequest()->getParam('id');
        $model = new Default_Model_Albume();
        if ($model->find($id)) {
            if ($model->delete()) {
                    $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Albumul a fost sters cu succes."
                                                        . "</div>");
            } else {
                    $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                        . "</div>");
            }
            $this->_redirect('/albume');	
        }
    }
}
