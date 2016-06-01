<?php
class ImportController extends Zend_Controller_Action{
   
    public function indexAction(){
       
      require_once APPLICATION_PUBLIC_PATH.'/library/Needs/Excel/reader.php';
      $data = new Spreadsheet_Excel_Reader();

      $data->setOutputEncoding('utf-8');
      $data->read(APPLICATION_PUBLIC_PATH.'/library/emails.xls'); 
      
//      print_r($data);die();
      
        if (count($data->sheets[0]['cells']) > 0)
        {
                $i=0;
                foreach($data->sheets[0]['cells'] as $currentSheet)
                {	
				
				$i++;
				
				$element['email']          = $currentSheet[1];
//				$element['client']             = $currentSheet[2];
//				$element['idCounty']            = $currentSheet[3];
//				$element['idCity']            = $currentSheet[4];
//				$element['address']   = $currentSheet[5];
//				$element['cui']        = $currentSheet[6];
//				$element['phone']      = $currentSheet[7];
//				$element['phone_medic']            = $currentSheet[8];
//				$element['email']    = $currentSheet[9];
//				$element['isClient']    = '0';
							
                    if($i > 0)
                    {                        
                        if(!$this->insertValues($element))
                        {
                            $this->_messages[] = 'Eroare inserare'.$element['email'].'';
                        }
                    }
                
                    $this->_valuesArray[$i]  = $element;	
                
                }
        }
        else{
            return $this->_messages[] = 'Eroare inserare emailuri';
        }		
//        Zend_Debug::dump($this->_valuesArray);

        return $this->_messages;
   }
   
   public function insertValues($valueArray)
   {
       $model = new Default_Model_NewsletterSubscribers();
       $model->setOptions($valueArray);
           
       $result = $model->save();
       
       return $result;
   }
}


