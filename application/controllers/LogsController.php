<?php
class LogsController extends Zend_Controller_Action{
    
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
        $model = new Default_Model_ComandaLogs();
        $select = $model->getMapper()->getDbTable()->select()
                        ->from(array('c'=>'comanda_logs'), array('id'=>'c.id','userId'=>'c.user_id','comandaId'=>'c.comanda_id','verb'=>'c.verb','departamentInitial'=>'c.departament_initial','departamentTrimis'=>'c.departament_trimis','created'=>'c.created','tip'=>'c.tip'))
                        ->order('c.created DESC');
        
        $result = $model->fetchAll($select);

        $this->view->result = $result;
        
        $model2 = new Default_Model_ComandaLogs();
        $select2 = $model2->getMapper()->getDbTable()->select()
                        ->from(array('c'=>'comanda_logs'), array('c.created'))
                        ->order('c.created DESC');
        
        $result2 = $model2->fetchRow($select2);
        
        $this->view->firstDate = $result2->getCreated();
    }

}
