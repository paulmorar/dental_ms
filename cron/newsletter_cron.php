<?php
include(dirname(__FILE__).'/../zend_application.php');
try{
	//BEGIN: Global Constants
	$globalConstants = new Default_Model_Setting();
	$select = $globalConstants->getMapper()->getDbTable()->select()
					->where('deleted=?',0);

	$result = $globalConstants->fetchAll($select);
	if(null != $result)
	{
		foreach ($result as $value)
		{
			if(!defined(strtoupper($value->getConstant()))){
				define(strtoupper($value->getConstant()), $value->getValue());
			}
		}
	}
	//END: Global Constants
	
	// BEGIN: Default Language
	$modelLang = new Default_Model_Languages();
	$select = $modelLang->getMapper()->getDbTable()->select()
				->where('`default` = ?',1);
				
	$defaultLang = $modelLang->fetchRow($select);
	if(!$defaultLang){
		throw new Exception('Error, default language not found.');
	}
	define("LANG_ID", $defaultLang->getId());
	// END: Default Language
	
	//BEGIN: Translate Constants
	$translateConstants = new Default_Model_Variables();
	$select = $translateConstants->getMapper()->getDbTable()->select()
					->where('deleted=?',0)
					->where('constant LIKE ?','NEWSLETTER_TEMPLATE_%');

	$result = $translateConstants->fetchAll($select);
	if(null != $result)
	{
		foreach ($result as $value)
		{
			$value->setMeta($defaultLang->getId());
			if($value->getMeta()){
				if(!defined(strtoupper($value->getConstant()))){
					define(strtoupper($value->getConstant()), $value->getMeta()->getValue());
				}
			}
		}
	}
	//END: Translate Constants

$sendingNumber = NEWSLETTER_SENDING_NUMBER_PER_SESSION;
$adminEmail= ADMIN_EMAIL;

$model = new Ldmadmin_Model_Newsletter();
$selectSending = $model->getMapper()->getDbTable()->select()
				->from(array('n'=>'newsletter'))
				->where('n.active = ?',1)			
				->where('n.status = ?','sending')			
				->where('n.deleted = ?',0)			
				->where('n.dataSend <= NOW()')			
				->order(array('n.created DESC'))
				->limit(1)
				->setIntegrityCheck(false);
$resultSending = $model->fetchRow($selectSending);

if($resultSending){	
	$newsletterId = $resultSending->getId();
	$subject = $resultSending->getSubject();
	
    $view = new Zend_View();
	$pathToTemplate = APPLICATION_PUBLIC_PATH . '/media/newsletter/';      
	$view->setScriptPath(array($pathToTemplate)); 
	$content = $view->partial('newsletter_template.phtml', array('newsletter' => $resultSending, 'PIC_PATH' => NEWSLETTER_PICTURES_PATH));
	
	$subscribersModel = new Default_Model_Subscribers();
	$select = $subscribersModel->getMapper()->getDbTable()->select()
				->from(array('s' => 'subscribers'))
				->joinInner(array('nts'=>'newsletter_to_subscribers'), 'nts.idSubscriber = s.id',array('ntsId'=>'nts.id','ntsCreated'=>'nts.created'))
				->where('nts.idNewsletter = ?',$newsletterId)			
				->where('nts.status = ?',0)
				->where('s.unsubscribed = ?',0)
				->order(array('nts.created DESC'))
				->limit($sendingNumber)
				->setIntegrityCheck(false);
	$resultSubscribers = $subscribersModel->fetchAll($select);	
	
	if($resultSubscribers){
		foreach($resultSubscribers as $subscriber){			
			$email = $subscriber->getEmail();
			$full_name = $subscriber->getName().' '.$subscriber->getSurname();
			
			$emailArray['subject']			= $subject;
			$emailArray['content']			= $content;
			$emailArray['toEmail']			= $email;
			$emailArray['toName']			= $full_name;
			$emailArray['fromEmail']		= $adminEmail;
			$emailArray['fromName']			= $adminEmail;

			$sended = Needs_Tools::sendEmail($emailArray);
			
			if($sended){
				$ntsModel = new Ldmadmin_Model_NewsletterToSubscribers();
				$ntsModel->setId($subscriber->getNtsId());
				$ntsModel->setStatus(1);
				$changeFromSubscriber = new Zend_Db_Expr('NOW()');
				$ntsModel->setLastStatusChange($changeFromSubscriber);
				
				//insert new record in newsletter_sent
				$nsModel = new Ldmadmin_Model_NewsletterSent();
				$row = array(
						'idNewsletter'		=> $newsletterId,
						'idSubscriber'		=> $subscriber->getId(),
						'createdFromSubscriber' => $subscriber->getNtsCreated(),
						'changeFromSubscriber' => $changeFromSubscriber,
						'statusFromSubscriber' => 1,
						'created'	        => new Zend_Db_Expr('NOW()')						
						);
				
				$insert =  $nsModel->getMapper()->getDbTable()->insert($row);
				
				//delete record from newsletter_to_subscriber
				$deleted = $ntsModel->delete();
			}
					
		}
	}else{			
		$resultSending->setStatus('sent');
		$resultSending->saveStatus();
	}
}

$selectWaiting = $model->getMapper()->getDbTable()->select()
				->from(array('n'=>'newsletter'))
				->where('n.active = ?',1)			
				->where('n.status = ?','waiting')			
				->where('n.deleted = ?',0)			
				->where('n.dataSend <= NOW()')		
				->order(array('n.created DESC'))
				->limit(1)
				->setIntegrityCheck(false);
$resultWaiting = $model->fetchRow($selectWaiting);	

if($resultWaiting){
	$resultWaiting->setStatus('sending');
	if($resultWaiting->saveStatus())
	{
		$newsletterId = $resultWaiting->getId();
		$subject = $resultWaiting->getSubject();
		
		$view = new Zend_View();
		$pathToTemplate = APPLICATION_PUBLIC_PATH . '/media/newsletter/';      
		$view->setScriptPath(array($pathToTemplate)); 
		$content = $view->partial('newsletter_template.phtml', array('newsletter' => $resultWaiting, 'PIC_PATH' => NEWSLETTER_PICTURES_PATH));	
		
		$subscribersModel = new Default_Model_Subscribers();
		$select = $subscribersModel->getMapper()->getDbTable()->select()
					->from(array('s' => 'subscribers'))
					->joinInner(array('nts'=>'newsletter_to_subscribers'), 'nts.idSubscriber = s.id',array('ntsId'=>'nts.id','ntsCreated'=>'nts.created'))
					->where('nts.idNewsletter = ?',$newsletterId)			
					->where('nts.status = ?',0)
					->where('s.unsubscribed = ?',0)
					->order(array('nts.created DESC'))
					->limit($sendingNumber)
					->setIntegrityCheck(false);
		$resultSubscribers = $subscribersModel->fetchAll($select);	

		if($resultSubscribers){
			foreach($resultSubscribers as $subscriber){			
				$email = $subscriber->getEmail();
				$full_name = $subscriber->getName().' '.$subscriber->getSurname();

				$emailArray['subject']			= $subject;
				$emailArray['content']			= $content;
				$emailArray['toEmail']			= $email;
				$emailArray['toName']			= $full_name;
				$emailArray['fromEmail']		= $adminEmail;
				$emailArray['fromName']			= $adminEmail;

				$sended = Needs_Tools::sendEmail($emailArray);

				if($sended){
					$ntsModel = new Ldmadmin_Model_NewsletterToSubscribers();
					$ntsModel->setId($subscriber->getNtsId());
					$ntsModel->setStatus(1);
					$changeFromSubscriber = new Zend_Db_Expr('NOW()');
					$ntsModel->setLastStatusChange($changeFromSubscriber);

					//insert new record in newsletter_sent
					$nsModel = new Ldmadmin_Model_NewsletterSent();
					$row = array(
							'idNewsletter'		=> $newsletterId,
							'idSubscriber'		=> $subscriber->getId(),
							'createdFromSubscriber' => $subscriber->getNtsCreated(),
							'changeFromSubscriber' => $changeFromSubscriber,
							'statusFromSubscriber' => 1,
							'created'	        => new Zend_Db_Expr('NOW()')						
							);

					$insert =  $nsModel->getMapper()->getDbTable()->insert($row);

					//delete record from newsletter_to_subscriber
					$deleted = $ntsModel->delete();
				}

			}
		}
	}	
}
}catch(EXCEPTION $e){
//	print_r($e->getMessage());
	exit('ERROR');
}
?>