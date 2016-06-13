<?php
class PacientiController extends Zend_Controller_Action{

    protected $_userId;

	public function init(){
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$this->view->message = $this->_flashMessenger->getMessages();

        $userId = NULL;
        $auth = Zend_Auth::getInstance();
        $authAccount = $auth->getStorage()->read();
        if (null!=$authAccount) {
            if (null!=$authAccount->getId()) {
                $this->userId   = $authAccount->getId();
                $this->roleId   = $authAccount->getIdRole();
                $this->name     = $authAccount->getName();
            }
        }
	}

	public function indexAction()
	{
            $model = new Default_Model_Users();
            $select = $model->getMapper()->getDbTable()->select()
                            ->from(array('u'=>'users'), array('*'))
                            ->joinLeft(array('r'=>'role'), 'r.id = u.idRole',array('idRole'=>'r.name'))
                            ->where('NOT u.deleted')
                            ->where('u.idRole = ?', 3);
                            if($this->roleId == 3){
                                $select->where('u.id = ?', $this->userId);
                            }
            $select->order('u.created DESC');
            $select->setIntegrityCheck(false);
            $result = $model->fetchAll($select);

            $this->view->result = $result;
	}

  public function showAction()
  {
      $id = $this->getRequest()->getParam('id');
      if($this->getRequest()->isPost())
        {
            $model = new Default_Model_Observations();
            $model->setUser($id);
            $model->setDoctor($this->userId);
            $model->setObservation($this->getRequest()->getParam('observatie'));

            if($model->save())
            {

              $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                  . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                  . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Observatia a fost adaugata cu succes."
                  . "</div>");
            }
            else
            {
              $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                  . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                  . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                  . "</div>");
            }

            $this->_redirect('/pacienti/show/id/'.$id);

        } else {
              if ($this->roleId == 3)
              {
                  $id = $this->userId;
              }

              $model = new Default_Model_Users();
              $select = $model->getMapper()->getDbTable()->select()
                  ->from(array('u'=>'users'), array('*'))
                  ->where('NOT u.deleted')
                  ->where('u.id = ?', $id);
              $select->order('u.created DESC');
              $result = $model->fetchAll($select);
          
              $this->view->result = $result;
              $this->view->role   = $this->roleId;
        }



  }

  public function addAction()
  {
      $model    = new Default_Model_Users();
      $form     = new Default_Form_Pacienti($this->roleId);
      $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/pacienti/user-add.phtml'))));

      $this->view->formAddUser = $form;

      if($this->getRequest()->isPost())
      {
          if($form->isValid($this->getRequest()->getPost()))
          {
              $password	= $form->getValue('password');

              $model->setOptions($form->getValues());
              $model->setIdRole(3);
              $model->setPassword(md5($password));


              if($id = $model->save())
              {
                  $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                      . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                      . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Pacientul a fost adaugat cu succes."
                                                      . "</div>");
              }
              else
              {
                  $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                  . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                  . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                  . "</div>");
              }

              $this->_redirect('/pacienti');
          }
      }
  }

	public function editAction() {

            $id = $this->getRequest()->getParam('id');
            $model = new Default_Model_Users();
            if ($model->find($id)) {
                $form = new Default_Form_Pacienti();
                $form->edit($model);
                $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/pacienti/edit-user.phtml'))));
                $this->view->formEditUser = $form;

                if ($this->getRequest()->isPost()) {
                        if ($form->isValid($this->getRequest()->getPost())) {
                                $model->setOptions($form->getValues());
                                if ($model->save()) {

                                        $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                            . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                            . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Utilizatorul a fost editat cu succes."
                                                            . "</div>");
                                } else {
                                        $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                        . "</div>");
                                }
                                $this->_redirect('/pacienti');
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
                                                                . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Utilizatorul a fost sters cu succes."
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
                    $this->_redirect('/pacienti');
            }
	}

}
