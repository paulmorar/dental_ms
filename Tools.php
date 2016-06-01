<?php
class Needs_Tools
{	
    public static function findAdmins($id=NULL){
        $modelu = new Default_Model_Users();
        if (!isset($id)){
        $select = $modelu->getMapper()->getDbTable()->select()
                ->from(array('u'=>'users'),array('u.email'))
                ->joinLeft(array('r'=>'role'), 'u.`idRole` = r.`id`',array(''))
                ->where('r.`isAdmin` = ?', '1')
                ->setIntegrityCheck(false);
        }else{
        $select = $modelu->getMapper()->getDbTable()->select()
                ->from(array('u'=>'users'),array('u.id'))
                ->joinLeft(array('r'=>'role'), 'u.`idRole` = r.`id`',array(''))
                ->where('r.`isAdmin` = ?', '1')
                ->setIntegrityCheck(false);
        }
        $result=$modelu->fetchAll($select);
        $return=null;
        if ($result){
                foreach ($result as $res){
                        if (!isset($id)){
                                $array[]=$res->getEmail();
                        }else{
                                $array[]=$res->getId();
                        }
                }
                $return=$array;
        }
        return $return;
    }

    public static function checkApi($apiKey=NULL){
            $model = new Default_Model_UsersFront();
            if (isset($apiKey)){
                    $select = $model->getMapper()->getDbTable()->select()
                            ->where('`apiKey` = ?', $apiKey);
                    $result=$model->fetchRow($select);
                    if (count($result)>0){
                            $idUser=$result->getId();
                            return $idUser;
                    }
                    else
                            return false;
            }else{
                    return false;
            }
    }

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
    public static function sendEmail($emailArray, $departamentId, $numeComanda,$emailClient)
    {
            $emailArray['toEmail']		= (!empty($emailArray['toEmail']))?$emailArray['toEmail']:'';
            $emailArray['toName']		= (!empty($emailArray['toName']))?$emailArray['toName']:'';
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
//                            'auth'		=> 'login',
//                            'username'          => $emailArray['SMTP_USERNAME'],
//                            'password'          => $emailArray['SMTP_PASSWORD'],
//                            'port'		=> $emailArray['SMTP_PORT']
                            'ssl'               => 'tls',
                            'port'              => 587,
                            'auth'              => 'login',
                            'username'          => 'brinduse.claudiu@gmail.com',
                            'password'          => 'Claudiu1',
                            
                    );
                    $transport = new Zend_Mail_Transport_Smtp($emailArray['SMTP_URL'], $config);
            }
            switch ($departamentId) {
                case 7:
                    //EXPEDIAT
                    $contents	= 'Buna ziua, <br> Comanda '. $numeComanda .' este in departamentul printare presa digitala. <br> O zi buna,<br> Echipa Photo Fuji';

                break;
                case 3:
                    //MACHETARE
                    $contents	= 'Buna ziua, <br> Comanda '. $numeComanda .' este in departamentul machetare. <br> O sa fiti anuntat cand comanda dumneavoastra trece in alt departament sau isi schimba statusul. <br> O zi buna,<br> Echipa Photo Fuji';

                break;
                case 90:
                    //CONFIRMARE MACHETARE
                    $contents	= 'Buna ziua, <br> Comanda '. $numeComanda .' asteapta confirmarea machetarii. <br> O sa fiti anuntat cand comanda dumneavoastra trece in alt departament sau isi schimba statusul. <br> O zi buna,<br> Echipa Photo Fuji';

                break;
                case 4:
                    //MINILAB
                    $contents	= 'Buna ziua, <br> Comanda '. $numeComanda .' este in departamentul minilab. <br> O sa fiti anuntat cand comanda dumneavoastra trece in alt departament sau isi schimba statusul. <br> O zi buna,<br> Echipa Photo Fuji';

                break;
                case 5:
                    //LEGATORIE
                    $contents	= 'Buna ziua, <br> Comanda '. $numeComanda .' este in departamentul legatorie. <br> O sa fiti anuntat cand comanda dumneavoastra trece in alt departament sau isi schimba statusul. <br> O zi buna,<br> Echipa Photo Fuji';

                break;
                case 6:
                    //EXPEDIERE
                    $contents	= 'Buna ziua, <br> Comanda '. $numeComanda .' urmeaza sa fie expediata. <br> O sa fiti anuntat cand comanda dumneavoastra trece in alt departament sau isi schimba statusul. <br> O zi buna,<br> Echipa Photo Fuji';

                break;
                case 91:
                    //EXPEDIAT
                    $contents	= 'Buna ziua, <br> Comanda '. $numeComanda .' a fost expediata. <br> O zi buna,<br> Echipa Photo Fuji';

                break;
                default:
                    //EXPEDIAT
                    $contents	= 'Buna ziua, <br> Comanda '. $numeComanda .' a trecut in alt departament. <br> O zi buna,<br> Echipa Photo Fuji';
                    break;
            }
            
            
            $fromEmail	= 'brinduse.claudiu@gmail.com';
            $fromName	= 'Claudiu';
            $subject	= 'Comanda Dumneavoastra';

            $toEmail	= $emailClient;


            $mail = new Zend_Mail('UTF-8');
            $mail->setBodyHtml($contents,'UTF-8', Zend_Mime::MULTIPART_RELATED);
            $mail->setFrom($fromEmail, $fromName);
            $mail->addTo($toEmail);
            $mail->setSubject($subject);

                try {
                    if((!empty($emailArray['SMTP_USERNAME'])) && (!empty($emailArray['SMTP_PASSWORD'])))
                    {
                        if($mail->send($transport))
                        {

                               // return true;
                        }		
                    }
                    else 
                    {
                        if($mail->send())
                        {
                               // return true;	
                        }
                    }
                } catch (Exception $exc) {
                        echo $exc->getTraceAsString();
                }

//            }
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

    public static function getClientByComandaId($id){
        $model = new Default_Model_Clients();
        $select = $model->getMapper()->getDbTable()->select()
                ->from(array('cl'=>'clienti'))
                ->joinLeft(array('c'=>'comanda_to_client'), 'c.`client_id` = cl.`id`', array('idComandaToClient'=>'c.id'))
                ->joinLeft(array('co'=>'comenzi'), 'c.`comanda_id` = co.`id`', array('idComanda'=>'co.id'))
                ->where('co.`id`= ?', $id)
                ->where('NOT co.`deleted`')
                ->where('NOT cl.`deleted`')
                ->setIntegrityCheck(false);

        $result = $model->fetchRow($select);

        return $result;
    }

    public static function getTipComandaById($id){
        $model = new Default_Model_TipComanda();
        $select = $model->getMapper()->getDbTable()->select()
                ->from(array('t'=>'tip_comanda'))
                ->where('t.`id`= ?', $id)
                ->where('NOT t.`deleted`');

        $result = $model->fetchRow($select);
        if($result){
            return $model->getNume();
        } else {
            return '-';
        }
    }

    public static function getClients(){
        $model = new Default_Model_Clients();
        $select = $model->getMapper()->getDbTable()->select()
                ->from(array('c'=>'clienti'))
                ->where('NOT c.`deleted`');

        $result = $model->fetchAll($select);

        if($result){
            return $result;
        } else {
            return null;
        }
    }
    
    public static function getStatusComandaById($id){
        $model = new Default_Model_Comanda();
        $select = $model->getMapper()->getDbTable()->select()
                ->where('NOT deleted')
                ->where('id ='.$id);

        $result = $model->fetchRow($select);
        if($result){
            return $result->getIncomplet();
        } else {
            return null;
        }
    }
    public static function getComandaById($id)
    {
            $model  = new Default_Model_Comanda();		 
            $model->find($id);		
            return $model;		
    }
    
    public static function getDepartamentComandaById($id){
        
        $departamentEdit     = new Default_Model_Users();
        $selectDep = $departamentEdit->getMapper()->getDbTable()->select()->from(array('u'=>'users'), array('*'))				
                                ->joinLeft(array('cd'=>'comanda_to_departament'), 'u.id=cd.departament_id',array())
                                ->where('NOT deleted')
                                ->where('cd.comanda_id  ='.$id)
                                ->order('id DESC')
                                ->setIntegrityCheck(false);
        $resultDep = $departamentEdit->fetchRow($selectDep);
        if($resultDep){
            return $resultDep;         
        } else {
            return null;
        }
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
    
    public static function getDepByComanda($id){
        
        $model = new Default_Model_ComandaToDepartament();
        $select = $model->getMapper()->getDbTable()->select()
                ->from(array('c'=>'comanda_to_departament'))
                ->where('c.`comanda_id`= ?', $id);

        $result = $model->fetchRow($select);
        
        if($result){
            return $result->getDepartamentId();
        } else {
            return false;
        }
    }
    
    
    public static function getComenziTipNr($id,$start = null,$end = null){
        
        $model  = new Default_Model_Comanda();
        $select = $model->getMapper()->getDbTable()->select()
                        ->from(array('c'=>'comenzi'), array('total'=>'COUNT(*)'))
                        ->where('c.tip = '.$id)
                        ->where('NOT c.deleted');
        if($start && $end){
            $select->where("(DATE(c.created) >= '".$start."' AND DATE(c.created) <= '".$end."')");
        }
        $result = $model->fetchRow($select);

        if($result){
            return $result->getTotal();
        } else {
            return 0;
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
}
