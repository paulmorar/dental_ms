<?php
class AgentsController extends Zend_Controller_Action{
    
    public function init(){
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();                
    }

    public function indexAction()
    {
        $model = new Default_Model_Users();			
        $select = $model->getMapper()->getDbTable()->select()
                        ->from(['u'=>'users'], ['id','idRole','name','status'])
                        ->joinLeft(['c'=>'clients'],'u.id=c.idAgent',['idClient'=>'c.id','idAgent','client','company','products','idCounty','idCity','address', 'phone','isClient'])
                        ->where('NOT u.deleted')
                        ->where('NOT c.deleted') 
                        ->order('u.name');            
        $select->setIntegrityCheck(false);
        $result = $model->fetchAll($select);

        $this->view->result = $result;
    }
	
    public function viewAction()
    {
        $idAgent = $this->getRequest()->getParam('id');
        $idClient = $this->getRequest()->getParam('client');
        $model = new Default_Model_Users();
        $select = $model->getMapper()->getDbTable()->select()
                ->from(['u'=>'users'],['id','name','idCar'])
                ->joinLeft(['c'=>'clients'],'u.id=c.idAgent',['idClient'=>'c.id','idAgent','client','company','products','idCounty','idCity','address','cui','phone','phoneMedic','email','isClient'])
                ->where('u.id = ?', $idAgent)
                ->where('c.id = ?', $idClient)
                ->where('NOT u.deleted')                           
                ->group('c.id')
                ->setIntegrityCheck(false);
        $result = $model->fetchRow($select);     
        $this->view->resultAgent = $result;
    }           
}
