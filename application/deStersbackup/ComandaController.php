<?php
class ComandaController extends Zend_Controller_Action{
    
    public function init(){
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();
        
        $auth = Zend_Auth::getInstance();
        $authAccount = $auth->getStorage()->read();
        if (null!=$authAccount) {
            if (null!=$authAccount->getId()) {
                $this->userId = $authAccount->getId();   
            }
        }
    }

    public function indexAction()
    {
        $type = Needs_Tools::getUserTypeById($this->userId);
        
        $startDate  = ($this->getRequest()->getParam('start')) ? date('Y-m-d', strtotime($this->getRequest()->getParam('start'))) : null ;
        $endDate    = ($this->getRequest()->getParam('end')) ? date('Y-m-d', strtotime($this->getRequest()->getParam('end'))) : null ;
        
        
        if($type == 1 || $type == 2){
            $model = new Default_Model_Comanda();
            $select = $model->getMapper()->getDbTable()->select()
                            ->from(array('c'=>'comenzi'), array('*'))
                            ->where('NOT c.deleted');
                            if($startDate && $endDate){
                                $select->where("(DATE(c.created) >= '".$startDate."' AND DATE(c.created) <= '".$endDate."')");
                            }
                            $select->group('c.id')
                            ->order('c.created DESC')
                            ->setIntegrityCheck(false);
            
            $result = $model->fetchAll($select);

            $this->view->result = $result;
        } else {
            $model = new Default_Model_Comanda();
            $select = $model->getMapper()->getDbTable()->select()
                            ->from(array('c'=>'comenzi'), array('*'))
                            ->joinLeft(array('cd'=>'comanda_to_departament'), 'c.id=cd.comanda_id',array())
                            ->where('NOT c.deleted')
                            ->where('cd.departament_id ='.$this->userId);
                            if($startDate && $endDate){
                                $select->where("(DATE(c.created) >= '".$startDate."' AND DATE(c.created) <= '".$endDate."')");
                            }
                            $select->group('c.id')
                            ->order('c.created DESC')
                            ->setIntegrityCheck(false);
            $result = $model->fetchAll($select);

            $this->view->result = $result;
            $this->_helper->viewRenderer('comanda/normal', null, true);
        }
        
        
        
    }

    public function addAction()
    {
        $model = new Default_Model_Comanda();
        $form = new Default_Form_Comanda();
        $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/comanda/add.phtml'))));
        
        $this->view->formAddComanda = $form;

        if($this->getRequest()->isPost())
        {			
            if($form->isValid($this->getRequest()->getPost())) 
            {
                $model->setOptions($form->getValues());
                if($this->getRequest()->getPost('incomplet')){
                    $model->setIncomplet(1);
                    $model->setDetalii_incomplet($this->getRequest()->getPost('detalii_incomplet'));
                } else {
                    $model->setIncomplet(0);
                }
                if($id = $model->save()) {     
                    $modelLegatura = new Default_Model_ComandaToClient();
                    $modelLegatura->setClientId($this->getRequest()->getPost('client'));
                    $modelLegatura->setComandaId($id);
                    
                    $modelDepartament = new Default_Model_ComandaToDepartament();
                    $modelDepartament->setDepartament_id($this->getRequest()->getPost('departament'));
                    $modelDepartament->setComanda_id($id);
                    
                    $modelLogs = new Default_Model_ComandaLogs();
                    $modelLogs->setUserId($this->userId);
                    $modelLogs->setComandaId($id);
                    $modelLogs->setVerb('a adaugat comanda');
                    $modelLogs->setTip('add');
                    $modelLogs->setDepartamentInitial(0);
                    $modelLogs->setDepartamentTrimis($this->getRequest()->getPost('departament'));
                    $modelLogs->save();

                    if($modelLegatura->save() && $modelDepartament->save()){
                        
                        $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Comanda a fost adaugat cu succes."
                                                        . "</div>");
                    } else {
                        $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                        . "</div>");
                    }
                    
                } else {
                    
                     $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                        . "</div>");
                }

                $this->_redirect('/comanda');
            }
        }
    }

    public function editAction() 
    {
        $id = $this->getRequest()->getParam('id');
        $model = new Default_Model_Comanda();
        
        if ($model->find($id)) {
            $form = new Default_Form_Comanda();
            $form->edit($model);
            $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/comanda/edit.phtml'))));
            $this->view->formEditComanda    = $form;
            $this->view->data               = date('d-m-Y',$model->getCreated());

            if ($this->getRequest()->isPost()) {
                if ($form->isValid($this->getRequest()->getPost())) {
                    $model->setOptions($form->getValues());
                    if($this->getRequest()->getPost('incomplet')){
                        $model->setIncomplet(1);
                        $model->setDetalii_incomplet($this->getRequest()->getPost('detalii_incomplet'));
                    } else {
                        $model->setIncomplet(0);
                        $model->setDetalii_incomplet(NULL);
                    }
                    if ($id = $model->save()) {
                            $modelLegatura = new Default_Model_ComandaToClient();   
                            $modelLegatura->setClientId($this->getRequest()->getPost('client'));
                            $modelLegatura->setComandaId($id);
                            $modelLegatura->delete($id);
                            
                            $modelLegatura->setClientId($this->getRequest()->getPost('client'));
                            $modelLegatura->setComandaId($id);
                            
                            $modelDepartament = new Default_Model_ComandaToDepartament();
                            $modelDepartament->delete($id);
                            $modelDepartament->setDepartament_id($this->getRequest()->getPost('departament'));
                            $modelDepartament->setComanda_id($id);
                            $modelDepartament->save();

                            $modelLogs = new Default_Model_ComandaLogs();
                            $modelLogs->setUserId($this->userId);
                            $modelLogs->setComandaId($id);
                            $modelLogs->setVerb('a editat comanda');
                            $modelLogs->setTip('edit');
                            $modelLogs->setDepartamentInitial(0);
                            $modelLogs->setDepartamentTrimis($this->getRequest()->getPost('departament'));
                            $modelLogs->save();
                            
                            if($modelLegatura->save()){
                                $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Comanda a fost editat cu succes."
                                                        . "</div>");
                            } else {
                                $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                    . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                    . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                    . "</div>");
                            }
                    } else {
                        $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                    . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                    . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                    . "</div>");
                    }
                    $this->_redirect('/comanda');		
                }				
            }
        }
    }
    
    public function deleteAction() 
    {
        $id = $this->getRequest()->getParam('id');
        $model = new Default_Model_Comanda();
        if ($model->find($id)) {
            if ($model->delete()) {
                    //stergem din tabela de legatura cu utilizatorii
                    $modelDepartament = new Default_Model_ComandaToDepartament();
                    $modelDepartament->setComanda_id($id);
                    $modelDepartament->delete($id);
                    //stergem din tabela de legatura cu clientii
                    $modelLegatura = new Default_Model_ComandaToClient();   
                    $modelLegatura->setComandaId($id);
                    $modelLegatura->delete($id);
                    
                    $modelLogs = new Default_Model_ComandaLogs();
                    $modelLogs->setUserId($this->userId);
                    $modelLogs->setComandaId($id);
                    $modelLogs->setVerb('a sters comanda');
                    $modelLogs->setTip('delete');
                    $modelLogs->setDepartamentInitial(0);
                    $modelLogs->setDepartamentTrimis(0);
                    $modelLogs->save();
                    
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
            $this->_redirect('/comanda');	
        }
    }
    
    public function sendNextAction(){
        
        $id = $this->getRequest()->getParam('id');
        $model = new Default_Model_Comanda();
        if($id){
            if ($model->find($id)) {
                if($this->getRequest()->getParam('minilab')){
                    $departamentVechi = 90;
                    $departamentNou = Needs_Tools::getUserByRole(4);
                } else {
                    $departamentVechi = Needs_Tools::getDepartamentComandaById($id);
                    switch ($departamentVechi->getIdRole()) {
                        case 3:
                            $departamentNou = 90;

                            break;
                        case 4:
                            $userNou = Needs_Tools::getUserByRole(5);
                            if($userNou){
                                $departamentNou = $userNou;
                            }

                            break;
                        case 5:
                            $userNou = Needs_Tools::getUserByRole(6);
                            if($userNou){
                                $departamentNou = $userNou;
                            }

                            break;
                        case 6:

                            $departamentNou = 91;

                            break;
                        case 90:

                            $userNou = Needs_Tools::getUserByRole(4);
                            if($userNou){
                                $departamentNou = $userNou;
                            }

                            break;

                        default:
                            die('eroare');
                            break;
                    }
                    
                    $departamentVechi = $departamentVechi->getIdRole();
                }
                
                
                $modelLogs = new Default_Model_ComandaLogs();
                $modelLogs->setUserId($this->userId);
                $modelLogs->setComandaId($id);
                $modelLogs->setVerb('a schimbat statusul comenzii');
                $modelLogs->setTip('send');
                $modelLogs->setDepartamentInitial($departamentVechi);
                $modelLogs->setDepartamentTrimis($departamentNou);
                $modelLogs->save();



                $modelDepartament = new Default_Model_ComandaToDepartament();
                $modelDepartament->setComanda_id($id);
                $modelDepartament->delete($id);
                $modelDepartament->setDepartament_id($departamentNou);
                $modelDepartament->setComanda_id($id);
                
                if($modelDepartament->save()){
                    $utilizator     = Needs_Tools::getDepartamentComandaById($id);
                   
                    if($utilizator) {
                        $departament    = Needs_Tools::getLevelById($utilizator->getIdRole());
                    } else {
                        $departamentNou = Needs_Tools::getDepByComanda($id);
                    }
                    
                    
                    $emailArray = array();
                    $client = Needs_Tools::getClientByComandaId($id);
                    print_r(Needs_Tools::sendEmail($emailArray,$departamentNou,$model->getNume(),$client->getEmail()));die();
                    
                    $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Etapa a fost finalizata cu succes."
                                                        . "</div>");
                } else {
                    $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                        . "</div>");
                }
            } else {
                $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                    . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                    . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>Nu am putut gasi aceasta comanda, va rugam incercati din nou!"
                                                    . "</div>");
            }
        }
        $this->_redirect('/comanda');	
        
    }
}
