<?php
class ProgramariController extends Zend_Controller_Action{

    public function init(){
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();

        $auth = Zend_Auth::getInstance();
        $authAccount = $auth->getStorage()->read();
        if (null!=$authAccount) {
            if (null!=$authAccount->getId()) {
                $this->userId = $authAccount->getId();
                $this->roleId   = $authAccount->getIdRole();
            }
        }
    }

    public function indexAction()
    {
        $model = new Default_Model_Programari();
        $select = $model->getMapper()->getDbTable()->select()
            ->from(array('p'=>'programari'), array('*'));
        if( $this->roleId == 2){
            $select->where('p.idDoctor = '.$this->userId);

        } elseif ($this->roleId == 3){
            $select->where('p.idPacient = '.$this->userId);

        }
        $select->where("p.data >= '".date('Y-m-d')."'");
        $select->order('p.data ASC');
        $select->order('p.ora ASC');
        $select->setIntegrityCheck(false);

        $result = $model->fetchAll($select);
        if($this->roleId == 3){
            $model = new Default_Model_Users();
            $select = $model->getMapper()->getDbTable()->select()
                ->from(array('u'=>'users'), array('*'))
                ->joinLeft(array('r'=>'role'), 'r.id=u.idRole',array('idRole'=>'r.name'))
                ->where('NOT u.deleted')
                ->where('u.idRole = ?', 2);
            $select->order('u.created DESC');
            $select->setIntegrityCheck(false);
            $users = $model->fetchAll($select);

            $this->view->type   = 'patient';
        } else {
            $model = new Default_Model_Users();
            $select = $model->getMapper()->getDbTable()->select()
                ->from(array('u'=>'users'), array('*'))
                ->joinLeft(array('r'=>'role'), 'r.id=u.idRole',array('idRole'=>'r.name'))
                ->where('NOT u.deleted')
                ->where('u.idRole = ?', 3);
            $select->order('u.created DESC');
            $select->setIntegrityCheck(false);
            $users = $model->fetchAll($select);

            $this->view->type = 'doctor';
        }

        if($this->roleId == 1){
            $this->view->class = 'hidden';
        }

        $this->view->userId = $this->userId;
        $this->view->result = $result;
        $this->view->users = $users;

    }

    public function addAction()
    {
        $model = new Default_Model_Programari();
        if($this->getRequest()->isPost())
        {
                if($this->getRequest()->getPost('type') == 'doctor'){
                    $doctorId   = $this->userId;
                    $pacientId  = $this->getRequest()->getPost('doctor');
                } else {
                    $doctorId   = $this->getRequest()->getPost('doctor');
                    $pacientId  = $this->userId;

                }
                $date       = $this->getRequest()->getPost('date');
                $hour       = $this->getRequest()->getPost('hour');

                $model->setIdPacient($pacientId);
                $model->setIdDoctor($doctorId);
                $model->setData($date);
                $model->setOra($hour);

                if($id = $model->save()) {

                    $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                    . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                    . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Programarea a fost adaugat cu succes."
                                                    . "</div>");
                } else {

                     $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                        . "</div>");
                }
            $this->_redirect('/programari');
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

}
