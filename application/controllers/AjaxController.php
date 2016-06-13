<?php
class AjaxController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
        $bootstrap = $this->getInvokeArg('bootstrap');
        if($bootstrap->hasResource('db')) {
        	$this->db = $bootstrap->getResource('db');
        }

       	$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->message = $this->_flashMessenger->getMessages();
    }

    public function getHoursByIdDoctorAction(){
        if($this->getRequest()->getParam('doctorId')){
            $id     = $this->getRequest()->getParam('doctorId');
            $date   = strtotime($this->getRequest()->getParam('date'));
            $options= '';
            $model = new Default_Model_Programari();
            $select = $model->getMapper()->getDbTable()->select()
                                ->where("idDoctor = '".$id."'")
                                ->where("data = '".date('Y-m-d', $date)."'");
            $result = $model->fetchAll($select);

            $arrayHours[] = '09:00';
            $arrayHours[] = '10:00';
            $arrayHours[] = '11:00';
            $arrayHours[] = '12:00';
            $arrayHours[] = '13:00';
            $arrayHours[] = '14:00';
            $arrayHours[] = '15:00';
            $arrayHours[] = '16:00';
            $arrayHours[] = '17:00';
            if($result)
            {
                foreach($result as $value){
                    if(in_array($value->getOra(), $arrayHours)){
                        $key = array_search($value->getOra(), $arrayHours);
                        unset($arrayHours[$key]);
                    }
                }

                foreach( $arrayHours as $hours){
                    $options .= '<option value='.$hours.'>'.$hours.'</option>';
                }


            } else {
                foreach($arrayHours as $hours){
                    $options .= '<option value='.$hours.'>'.$hours.'</option>';

                }
            }

            echo $options;

        } else {
            echo null;
        }
    }
}


