<?php
class IframeController extends Zend_Controller_Action
{
        public function init(){
            $bootstrap = $this->getInvokeArg('bootstrap');
            if($bootstrap->hasResource('db')) {
                    $this->db = $bootstrap->getResource('db');
            }
            $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
            $this->view->message = $this->_flashMessenger->getMessages();
        }	

	public function messagesAttachementAction()
	{
		$id     = (int)  $this->getRequest()->getParam('id');
		$this->view->id = $id;
	}
	
	public function forwardAction()
	{
		$id     = (int)  $this->getRequest()->getParam('id');
		
		$messageModel = new Default_Model_Messages();
		$messageModel->find($id);
		
		$form = new Default_Form_MessageForward();
		$form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/messages/forward.phtml'))));
		$this->view->formForward = $form;
		
		if($this->getRequest()->isPost())
		{
			$forwardModel = new Default_Model_MessagesForwarded();
			
			$forwardModel->setIdUser(Zend_Registry::get('user')->getId());
			$forwardModel->setEmail($this->getRequest()->getPost('email'));
			$forwardModel->setIdMessage($id);
			
			if($forwardModel->save()){
				
				$message = Needs_Tools::getMessageById($id);
				
				$emailArray = array();
				
				$emailArray['toEmail']		= $this->getRequest()->getPost('email');
				$emailArray['toName']		= $this->getRequest()->getPost('email');
				$emailArray['fromEmail']	= SYSTEM_EMAIL;
				$emailArray['fromName']		= SYSTEM_ADMIN;
				$emailArray['content']		= '<p><strong>Email forwarded by:</strong> '.Zend_Registry::get('user')->getEmail().'</p><p><strong>Content:</strong> '.$message->getMessage().'</p><p><strong>Request sent by:</strong> '.$message->getEmail().'</p>';
				$emailArray['subject']		= $message->getSubject();
								
				Needs_Tools::sendEmail($emailArray);
				
				$this->_flashMessenger->addMessage("<div class='notification success  canhide'><p>Emailul a fost trimis.<p></div>");
			}
			else
			{
				$this->_flashMessenger->addMessage("<div class='notification failure canhide'><p>Eroare la trimiterea email-ului!<p></div>");
			}
			$this->_redirect('messages/messages');
		}		
	}
        
        public function detailsAction()
        {
            $id = $this->getRequest()->getParam('id');
            if($id){
                $model = new Default_Model_Comanda();
                $select = $model->getMapper()->getDbTable()->select()
                                ->from(array('c'=>'comenzi'), array('*'))
                                ->joinLeft(array('cd'=>'comanda_to_departament'), 'c.id=cd.comanda_id',array())
                                ->joinLeft(array('cc'=>'comanda_to_client'), 'c.id=cc.comanda_id',array())
                                ->where('NOT c.deleted')
                                ->where('cc.client_id ='.$id)
                                ->group('c.id')
                                ->order('c.created DESC')
                                ->setIntegrityCheck(false);
                $result = $model->fetchAll($select);

                $this->view->result = $result;
            } else {
                $this->_redirect('/clienti');
            }
        }
}