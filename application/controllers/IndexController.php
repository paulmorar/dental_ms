<?php
class IndexController extends Zend_Controller_Action{
    public function init(){
            $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
            $this->view->message = $this->_flashMessenger->getMessages();

            $bootstrap = $this->getInvokeArg('bootstrap');
            if($bootstrap->hasResource('db')) {
                    $this->db = $bootstrap->getResource('db');
            }
            
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
        $type = Needs_Tools::getUserTypeById($this->userId);
        if($type != 1){
            $this->_redirect('/comanda');
        }
        
        $startDate  = ($this->getRequest()->getParam('start')) ? date('Y-m-d', strtotime($this->getRequest()->getParam('start'))) : null ;
        $endDate    = ($this->getRequest()->getParam('end')) ? date('Y-m-d', strtotime($this->getRequest()->getParam('end'))) : null ;
        
        $model  = new Default_Model_Comanda();
        $select = $model->getMapper()->getDbTable()->select()
                        ->from(array('c'=>'comenzi'), array('total'=>'COUNT(*)'))
                        ->where('NOT c.deleted');
        if($startDate && $endDate){
            $select->where("(DATE(c.created) >= '".$startDate."' AND DATE(c.created) <= '".$endDate."')");
        }
        
        $result = $model->fetchRow($select);
        
        $modelExp   = new Default_Model_Comanda();
        $selectExp  = $modelExp->getMapper()->getDbTable()->select()
                        ->from(array('c'=>'comenzi'), array('total'=>'COUNT(*)'))
                        ->joinLeft(array('cd'=>'comanda_to_departament'), 'c.id=cd.comanda_id',array())
                        ->where('NOT c.deleted')
                        ->where('cd.departament_id ='. 91);
        if($startDate && $endDate){
            $selectExp->where("(DATE(c.created) >= '".$startDate."' AND DATE(c.created) <= '".$endDate."')");
        }

        $expediate = $modelExp->fetchRow($selectExp);
        
        $modelClients   = new Default_Model_Clients();
        $selectClients  = $modelClients->getMapper()->getDbTable()->select()
                        ->from(array('c'=>'clienti'), array('total'=>'COUNT(*)'))
                        ->where('NOT c.deleted');

        $clients    = $modelClients->fetchRow($selectClients);
        
        $modelTip   = new Default_Model_TipComanda();
        $selectTip  = $modelTip->getMapper()->getDbTable()->select()
                        ->from(array('t'=>'tip_comanda'), array('nume'=>'t.nume','id'=>'t.id'))
                        ->where('NOT t.deleted')
                        ->group('t.id');

        $tipuri = $modelTip->fetchAll($selectTip);
        
        if(!$startDate && !$endDate){
            $endDate    = date('Y-m-d');
            $startDate  = date('Y-m-d', strtotime ( '-30 day ', strtotime($endDate)));
        }
        
        $dateRangeArray = $this->createDateRangeArray($startDate, $endDate);
        $dateRangePostsStringArray =  implode("," ,array_map(function($date){
            return "'" . date('d-m-Y',strtotime($date)) . "'";
            },$dateRangeArray));
        
        $model  = new Default_Model_Comanda();
        $selectGrafic = $model->getMapper()->getDbTable()->select()
                        ->from(array('c'=>'comenzi'), array('total'=>'COUNT(*)', 'created'=>'DATE(created)'))
                        ->where('NOT c.deleted')
                        ->order('c.created ASC')
                        ->group('DATE(c.created)');
                        
        if($startDate && $endDate){
            $select->where("(DATE(c.created) >= '".$startDate."' AND DATE(c.created) <= '".$endDate."')");
        }
        
        $resultGrafic = $model->fetchAll($selectGrafic);

        $resultFinal = $this->getCompleteDateRangeArray($startDate,$endDate,$resultGrafic);
        
        $grafic = '[';
        foreach($resultFinal as $value){
            $grafic .= $value.',';
        }
        $grafic = rtrim($grafic,',');
        $grafic .= ']';
        
        $this->view->start      = $startDate;
        $this->view->end        = $endDate;
        $this->view->days       = $dateRangePostsStringArray;
        
        $this->view->tipuri     = $tipuri;
        $this->view->clienti    = $clients->getTotal();
        $this->view->expediate  = $expediate->getTotal();
        $this->view->total      = $result->getTotal();
        
        $this->view->grafic      = $grafic;
    }
    
    /**
     * returns the list of dates between 2 dates
     */
    public function createDateRangeArray($strDateFrom, $strDateTo){
        // takes two dates formatted as YYYY-MM-DD and creates an
        // inclusive array of the dates between the from and to dates.
        // could test validity of dates here but I'm already doing
        // that in the main script
            //$strDateFrom = date('Y-m-d', $strDateFrom);
            //$strDateTo = date('Y-m-d', $strDateTo);

        $aryRange = array();

        $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
        $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

        if ($iDateTo>=$iDateFrom)
        {
            array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
            while ($iDateFrom<$iDateTo)
            {
                $iDateFrom+=86400; // add 24 hours
                array_push($aryRange,date('Y-m-d',$iDateFrom));
            }
        }
        return $aryRange;
    }
    
    /**getInsightsDataByFieldByDate
     * Convention: $array must be a $key(date) -> $value(count) array
     * "Completes" date with count 0 for nonexisting dates of the given array
     */
    public function getCompleteDateRangeArray($strDateFrom, $strDateTo, $array){
	$result = array();
	foreach($this->createDateRangeArray($strDateFrom, $strDateTo) as $date){
            $result[$date] = 0;
	}
        
	foreach($array as $row){
	    if(array_key_exists(date('Y-m-d',$row->getCreated()), $result)){
		$result[date('Y-m-d',$row->getCreated())] = $row->getTotal();
	    }
	}
	return $result;
    }
}	

