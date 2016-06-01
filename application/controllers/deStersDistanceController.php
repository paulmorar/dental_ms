<?php
class DistanceController extends Zend_Controller_Action{
    
	public function init(){
            $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
            $this->view->message = $this->_flashMessenger->getMessages();                
	}
	
	public function indexAction()
	{
            $model = new Default_Model_Distance();
            $idAgent = Zend_Registry::get('user')->getId();
            //agent 
            $select = $model->getMapper()->getDbTable()->select()
                            ->from(array('d'=>'distance'), array('*'))
                            ->joinLeft(array('u'=>'users'), 'd.idAgent=u.id',array('name','idRole','idCar','status','deleted'))                           
                            ->where('d.idAgent = ?', $idAgent)                    
                            ->where('u.idRole = ?', 2)                    
                            ->where('NOT deleted')
                            ->where('u.status = ?', 1)
                            ->order('d.monthId DESC')
                            ->setIntegrityCheck(false); 
//            echo $select;die();
            $result = $model->fetchRow($select);
            $this->view->result = $result;	
            
            //admin
            $selectA = $model->getMapper()->getDbTable()->select()
                            ->from(array('d'=>'distance'), array('*'))
                            ->joinLeft(array('u'=>'users'), 'd.idAgent=u.id',array('name','idRole','idCar','status','deleted'))                           
                            ->where('u.status = ?', 1)
                            ->where('u.idRole = ?', 2)
                            ->where('NOT u.deleted')  
//                            ->where('d.monthId = ?', date('n',strtotime('-1 month')))
                            ->group('d.idAgent')
                            ->order('d.monthId DESC')                         
                            ->setIntegrityCheck(false); 
//            echo $selectA;die(); 
            $resultA = $model->fetchAll($selectA);
            $this->view->resultA = $resultA;
	}
        
        public function viewAction()
        {
            $model = new Default_Model_Distance();
            $idAgent = $this->getRequest()->getParam('id');
            //agent 
            $select = $model->getMapper()->getDbTable()->select()
                            ->from(array('d'=>'distance'), array('*'))
                            ->joinLeft(array('u'=>'users'), 'd.idAgent=u.id',array('name','idRole','idCar','status','deleted'))                           
                            ->where('d.idAgent = ?', $idAgent)                    
                            ->where('u.idRole = ?', 2)                    
                            ->where('NOT deleted')
                            ->where('u.status = ?', 1)
                            ->order('d.monthId DESC')
                            ->setIntegrityCheck(false); 
//            echo $select;die();
            $result = $model->fetchRow($select);
            $this->view->result = $result;
        }
}
