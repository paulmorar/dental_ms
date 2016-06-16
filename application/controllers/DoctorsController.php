<?php
class DoctorsController extends Zend_Controller_Action{

    protected $_userId;

	public function init(){
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->message = $this->_flashMessenger->getMessages();

        $userId = NULL;
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
            $model = new Default_Model_Users();
            $select = $model->getMapper()->getDbTable()->select()
                            ->from(array('u'=>'users'), array('*'))
                            ->joinLeft(array('r'=>'role'), 'r.id=u.idRole',array('idRole'=>'r.name'))
                            ->where('NOT u.deleted')
                            ->where('u.idRole = ?', 2);
            $select->order('u.created DESC');
            $select->setIntegrityCheck(false);
            $result = $model->fetchAll($select);

            $this->view->result = $result;
	}

        public function addAction()
        {
            $model = new Default_Model_Users();
            $form = new Default_Form_Doctors();
            $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/doctors/user-add.phtml'))));

            $this->view->formAddUser = $form;

            if($this->getRequest()->isPost())
            {
                if($form->isValid($this->getRequest()->getPost()))
                {
                    $password	= $form->getValue('password');

                    $model->setOptions($form->getValues());
                    $model->setIdRole(2);
                    $model->setPassword(md5($password)); //generare parola random la inregistrare user


                    if($id = $model->save())
                    {

                        $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                            . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                            . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Doctorul a fost adaugat cu succes."
                                                            . "</div>");
                    }
                    else
                    {
                        $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                        . "</div>");
                    }

                    $this->_redirect('/doctors');
                }
            }
        }

	public function editAction() {

            $id = $this->getRequest()->getParam('id');
            $model = new Default_Model_Users();
            if ($model->find($id)) {
                $form = new Default_Form_Doctors();
                $form->edit($model);
                $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/doctors/edit-user.phtml'))));
                $this->view->formEditUser = $form;

                if ($this->getRequest()->isPost()) {
                        if ($form->isValid($this->getRequest()->getPost())) {
                                $model->setOptions($form->getValues());
                                if ($model->save()) {

                                        $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                            . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                            . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Doctorul a fost editat cu succes."
                                                            . "</div>");
                                } else {
                                        $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                        . "</div>");
                                }
                                $this->_redirect('/doctors');
                        }
                }
            }
	}

	public function deleteAction()
        {
            $id = $this->getRequest()->getParam('id');
            $model = new Default_Model_Users();
            if ($model->find($id)) {
                    if($id != $this->userId){
                            if ($model->delete()) {
                                    $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                                . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                                . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Doctorul a fost sters cu succes."
                                                                . "</div>");
                            } else {
                                    $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                            . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                            . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                            . "</div>");
                            }
                    }else{
                            $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                            . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                            . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>Nu puteti sterge contul cu care sunteti logat."
                                                            . "</div>");
                    }
                    $this->_redirect('/doctors');
            }
	}

}
