<?php
class NewsletterController extends Zend_Controller_Action{
    
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
        $startDate  = ($this->getRequest()->getParam('start')) ? date('Y-m-d', strtotime($this->getRequest()->getParam('start'))) : null ;
        $endDate    = ($this->getRequest()->getParam('end')) ? date('Y-m-d', strtotime($this->getRequest()->getParam('end'))) : null ;

        $model = new Default_Model_Newsletter();
        $select = $model->getMapper()->getDbTable()->select()
                        ->from(array('n'=>'newsletter'), array('*'));
                        if($startDate && $endDate){
                            $select->where("(DATE(n.created) >= '".$startDate."' AND DATE(n.created) <= '".$endDate."')");
                        }
        $result = $model->fetchAll($select);

        $this->view->result = $result;

    }

    public function addAction()
    {
        $model = new Default_Model_Newsletter();
        
        $form = new Default_Form_Newsletter();
        $form->setDecorators(array('ViewScript', array('ViewScript', array('viewScript' => 'forms/newsletter.phtml'))));
        
        $this->view->form = $form;

        if($this->getRequest()->isPost())
        {			
            if($form->isValid($this->getRequest()->getPost())) 
            {
//                print_r($form->getValues());die();
                $form->image->receive();
                $model->setOptions($form->getValues());
                $model->setTitle($this->getRequest()->getPost('title'));
                $model->setMessage($this->getRequest()->getPost('message'));
                
                if($this->getRequest()->getPost('emails')){
                    $options = $this->getRequest()->getPost('emails');
                    $sentTo = implode(',', $options);
                } else {
                    $options= array();
                    $clients = new Default_Model_Clients();
                    $select = $clients->getMapper()->getDbTable()->select()
                                            ->where('NOT deleted')
                                            ->order('nume ASC');
                    $result = $clients->fetchAll($select);

                    if(NULL != $result)
                    {
                        foreach($result as $value){
                            $options[] = $value->getEmail();
                        }
                    }
                }
                $sentTo = implode(',', $options);
                $model->setSent($sentTo);


                $emailArray['toEmail']		= $options;
                $emailArray['fromEmail']        = 'brinduse.claudiu@gmail.com';
                $emailArray['fromName']		= 'Claudiu';
                $emailArray['content']		= $this->getRequest()->getPost('message');
                $emailArray['subject']		= $this->getRequest()->getPost('title');
                if($form->getValue('image')){
                    $emailArray['image']		= $form->getValue('image');
                }
                

                if(Needs_Tools::sendNewsletter($emailArray)) {
                    $model->save();

                    $this->_flashMessenger->addMessage("<div class='alert alert-success alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-check'></i> Succes! </h4>Newsletterul a fost trimis cu succes."
                                                        . "</div>");
                    
                } else {

                    $this->_flashMessenger->addMessage("<div class='alert alert-danger alert-dismissible'>"
                                                        . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>"
                                                        . "<h4><i class='icon fa fa-ban'></i> Eroare! </h4>A existat o eroare. Va rugam incercati din nou."
                                                        . "</div>");
                }

                $this->_redirect('/newsletter');
            }
        }
    }
}
