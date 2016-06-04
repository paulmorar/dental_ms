<?php
class Needs_Tools
{
    public static function paginatorToModel($paginator,$modelName)
    {		
            $entries = array();
            foreach($paginator as $row) {
                    $model = new $modelName();
                    $model->setOptions($row);
                    $entries[] = $model;
            }
            return $entries;
    }


    public static function hasAccess($roleId, $resourceConst,$displayNone = NULL)
    {	
            //if isAdmin no need for futher verification
            if(self::isAdmin($roleId))
            {
                    if ($displayNone){
                            return '';
                    }		
                    return true;
            }		

            //find resource
            $result = false;
            $resourceModel = new Default_Model_Resource();
            $select2 = $resourceModel->getMapper()->getDbTable()->select()
                                    ->from(array('resource'),array('id'))
                                    ->where('resource = ?',$resourceConst);
            $resourceModel->fetchRow($select2);
            if($resourceModel->getId() != null)
            {
                    //find resource and role connection, if there is any
                    $resourceRole = new Default_Model_ResourceRole;
                    $select3 = $resourceRole->getMapper()->getDbTable()->select()
                                            ->where('idResource = ?',$resourceModel->getId())
                                            ->where('idRole = ?',$roleId);
                    $resourceRole->fetchRow($select3);
                    if($resourceRole->getId() != NULL)
                    {
                            $result = true;
                    }
            }
            if($displayNone && !$result){
                    $result = ' style="display:none"';
            }elseif ($displayNone){
                    return '';
            }
            return $result;
    }	

    /**
     * Sends SMTP or normal emails
     * 
     * @param array $emailArray must have the following fields:
     * - toEmail
     * - toName
     * - fromEmail
     * - fromName
     * - content
     * - subject
     * 
     * Optional:
     * - SMTP_USERNAME
     * - SMTP_PASSWORD
     * - SMTP_PORT
     * - SMTP_URL
     */
    public static function sendEmail($utilizator, $password, $doctor)
    {
            $fromEmail	= 'noreply@dentalms.ro';
            $fromName	= 'Dental MS - No Reply';
            $subject	= 'Contul Dumneavoastra';

            $toEmail	= $utilizator;

            $contents	= 'Buna ziua, <br> V-a fost alocat cont in sistemul doctorului '.$doctor .'. <br> Folosind utilizatorul:'.$utilizator.' si parola '. $password .' <br> O zi buna,<br> Echipa Dental MS';


            $mail = new Zend_Mail('UTF-8');
            $mail->setBodyHtml($contents,'UTF-8', Zend_Mime::MULTIPART_RELATED);
            $mail->setFrom($fromEmail, $fromName);
            $mail->addTo($toEmail);
            $mail->setSubject($subject);
            
                try {
                    if($mail->send())
                    {
                            return true;
                    }
                } catch (Exception $exc) {
                        echo $exc->getTraceAsString();
                }
            return false;
    }
    
    /**
	 * Sends SMTP or normal emails
	 * 
	 * @param array $emailArray must have the following fields:
	 * - toEmail
	 * - toName
	 * - fromEmail
	 * - fromName
	 * - content
	 * - subject
	 * 
	 * Optional:
	 * - SMTP_USERNAME
	 * - SMTP_PASSWORD
	 * - SMTP_PORT
	 * - SMTP_URL
	 */
    public static function sendNewsletter($emailArray)
    {
            $emailArray['toEmail']		= (!empty($emailArray['toEmail']))?$emailArray['toEmail']:'';
            $emailArray['fromEmail']            = (!empty($emailArray['fromEmail']))?$emailArray['fromEmail']:'';
            $emailArray['fromName']		= (!empty($emailArray['fromName']))?$emailArray['fromName']:'';
            $emailArray['content']		= (!empty($emailArray['content']))?$emailArray['content']:'';
            $emailArray['subject']		= (!empty($emailArray['subject']))?$emailArray['subject']:'';
            
            $emailArray['SMTP_USERNAME'] = 'brinduse.claudiu@gmail.com';
            $emailArray['SMTP_PASSWORD'] = 'Claudiu1';
            $emailArray['SMTP_PORT']     = 465;
            $emailArray['SMTP_URL']      = 'smtp.gmail.com';
            
            if((!empty($emailArray['SMTP_USERNAME'])) && (!empty($emailArray['SMTP_PASSWORD'])) && (!empty($emailArray['SMTP_PORT'])) && (!empty($emailArray['SMTP_URL'])))
            {
                $config = array(
//                        'auth'		=> 'login',
//                        'username'	=> $emailArray['SMTP_USERNAME'],
//                        'password'	=> $emailArray['SMTP_PASSWORD'],
//                        'port'		=> $emailArray['SMTP_PORT']
                        'ssl'               => 'tls',
                        'port'              => 587,
                        'auth'              => 'login',
                        'username'          => 'brinduse.claudiu@gmail.com',
                        'password'          => 'Claudiu1',
                );
                $transport = new Zend_Mail_Transport_Smtp($emailArray['SMTP_URL'], $config);
            }
            $fromEmail	= $emailArray['fromEmail'];
            $fromName	= $emailArray['fromName'];
            if($emailArray['image']){
                $contents	= $emailArray['content'].'<img src="'.WEBROOT.'data/uploads/'.$emailArray['image'].'" alt="imagine">';
            }else{
                $contents	= $emailArray['content'];
            }
            

            foreach($emailArray['toEmail'] as $value){

                $toEmail	= $value;

                $mail = new Zend_Mail('UTF-8');
                $mail->setBodyHtml($contents,'UTF-8', Zend_Mime::MULTIPART_RELATED);
                $mail->setFrom($fromEmail, $fromName);
                $mail->addTo($toEmail);
                $mail->setSubject($emailArray['subject']);

                try {
                    if((!empty($emailArray['SMTP_USERNAME'])) && (!empty($emailArray['SMTP_PASSWORD'])))
                    {
                        if($mail->send($transport))
                        {

//                                return true;
                        }		
                    }
                    else 
                    {
                        if($mail->send())
                        {
//                                return true;	
                        }
                    }
                } catch (Exception $exc) {
//                    echo $exc->getTraceAsString();
                }

            }
            return true;
    }
    
    
    /**
     * Return all the Child Elements
     * @param (int) $parentId
     * @param model $model
     * @return $result - array of objects
     */
    public static function getChildElements($parentId,$model,$noOrder = null)
    {
            $model = new $model;
            $select = $model->getMapper()->getDbTable()->select()
            ->where('NOT deleted')
            ->where('idParent = ?',$parentId);
            if($noOrder){
                    $select->order('created DESC');
            }else{
                    $select->order('order ASC');
            }	

            $result = $model->fetchAll($select);
            return $result;
    }

    /**
     * 
     * @param string $name
     * @param type $options
     */
    public static function includeTemplate($name, $options = NULL)
    {
            $variables = new stdClass();
            if($options){
                    $variables = json_decode(json_encode($options), FALSE); // convert array to object using built in functions
            }
            $variables->webroot = WEBROOT;
            $templatesLocation = 'templates/';
            try{
                    include $templatesLocation.$name.'.phtml';
            }catch(Exception $e){
                    /**
                     * #toDo
                     * error handling
                     */
            }
    }

    public static function getUserById($id)
    {
            $model  = new Default_Model_Users();		 
            $model->find($id);		
            return $model->getName();		
    }
    
    public static function getUserTypeById($id)
    {
            $model  = new Default_Model_Users();		 
            $model->find($id);		
            return $model->getIdRole();		
    }
    
    public static function getLevelById($levelId){

            $model = new Default_Model_Role();
            $model->find($levelId);

            return $model->getName();
    }
    /**
     * Return the weeks end/start date by $date
     * 
     * @param (date) $date - format 'YYYY-mm-dd'
     * @param (string) $type - values: 'start' or 'end'
     * @return (date) $thisWeekDate - format 'YYYY-mm-dd'
     */
    public static function getWeekDaysByDate($date,$type='start')
    {
            $unixDate = strtotime($date);
            if($type == 'start'){
                    $thisWeekDate	=  date('Y-m-d', mktime(0, 0, 0, date('m',$unixDate), date('d',$unixDate)-date('w',$unixDate), date('Y',$unixDate)));			
            }else{
                    $thisWeekDate	=  date('Y-m-d', mktime(0, 0, 0, date('m',$unixDate), date('d',$unixDate)-date('w',$unixDate)+6, date('Y',$unixDate)));	
            }
            return $thisWeekDate;
    }

    public static function getLastWeekDaysByDate($date,$type='start')
    {
            $unixDate = strtotime($date);
            if($type == 'start'){
                    $thisWeekDate	=  date('Y-m-d', mktime(0, 0, 0, date('m',$unixDate), date('d',$unixDate)-date('w',$unixDate)-7, date('Y',$unixDate)));			
            }else{
                    $thisWeekDate	=  date('Y-m-d', mktime(0, 0, 0, date('m',$unixDate), date('d',$unixDate)-date('w',$unixDate)-1, date('Y',$unixDate)));	
            }
            return $thisWeekDate;
    }
    
    public static function getUserByRole($id){
        
        $model = new Default_Model_Users();
        $select = $model->getMapper()->getDbTable()->select()
                ->from(array('u'=>'users'))
                ->where('u.`idRole`= ?', $id)
                ->where('NOT u.`deleted`');

        $result = $model->fetchRow($select);
        
        if($result){
            return $model->getId();
        } else {
            return false;
        }
    }
    
    public static function getNivel1ById($id){

            $model = new Default_Model_Nivel1();
            $model->find($id);

            if($model->getName()){
                return $model->getName();
            } else {
                return '-';
            }
    }
    
    public static function getNivel2ById($id){

            $model = new Default_Model_Nivel2();
            $model->find($id);
            
            
            if($model->getName()){
                return $model->getName();
            } else {
                return '-';
            }
            
    }

    public static function getObservationsByPacient($id){

        $model = new Default_Model_Observations();
        $select = $model->getMapper()->getDbTable()->select()
            ->from(array('o'=>'observations'))
            ->where('o.`user`= ?', $id);
        $result = $model->fetchAll($select);

        if($result){
            return $result;
        } else {
            return false;
        }
    }
}
