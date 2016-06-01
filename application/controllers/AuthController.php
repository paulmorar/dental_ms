<?php
class AuthController extends Zend_Controller_Action
{
    protected $_flashMessenger = null;
	
    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();
        $bootstrap = $this->getInvokeArg('bootstrap');
        if($bootstrap->hasResource('db')) {
            $this->db = $bootstrap->getResource('db');
        }
    }

    public function loginAction()
    {
        $form = new Default_Form_Login();
        $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/login.phtml'))));
        $this->view->form = $form;
        
        if($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getPost())) {
            	$dbAdapter = new Zend_Auth_Adapter_DbTable($this->db, 'users', 'email', 'password', 'MD5(?)');
            	$dbAdapter -> setIdentity($this->getRequest()->getPost('userEmail'))
                           -> setCredential($this->getRequest()->getPost('userPassword'));      
                $selectAuth = $dbAdapter->getDbSelect();
                $selectAuth->where('deleted = 0');
            	$auth = Zend_Auth::getInstance();
            	$result = $auth->authenticate($dbAdapter);					
            	if(!$result->isValid()) {                    
                    switch($result->getCode()) {					
                        case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                        case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:							
                                            $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>Datele de logare sunt incorecte."
                                                        . "</div>");
                            break;					
                        default:						
                            /** do stuff for other failure **/
                                            $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>Datele de logare sunt incorecte."
                                                        . "</div>");
                            break;
                    }
            	}else{                   
                    $adminUserId    = $dbAdapter->getResultRowObject();                     
                    $adminUser      = new Default_Model_Users();
                    $adminUser -> find($adminUserId->id);                     
                    $storage        = $auth->getStorage();
                    $storage->write($adminUser);		        	
                }				
                $this->_redirect('/auth/login/');
            }
        }
    }		

    public function logoutAction()
    {
    	$this->_helper->layout->disableLayout();
    	$auth = Zend_Auth::getInstance();
    	if($auth->hasIdentity()) {
            $auth->clearIdentity();
    	}
        $this->_redirect('/auth/login');
    }
		
}