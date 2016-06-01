<?php
class Default_Form_Newsletter extends Zend_Form
{
    function init()
    {
        $this->setMethod('post');
        $this->addAttribs(array('id'=>'newsletter-form'));
        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);

        $title = new Zend_Form_Element_Text('title');		
        $title->setAttribs(array('class'=>'form-control validate[required]', 'placeholder'=>'Titlu'));
        $title->setRequired(true);
        $this->addElement($title);				

        $content = new Zend_Form_Element_Textarea('message');	
        $content->setAttribs(array('class'=>'form-control validate[required]','rows'=>'3', 'placeholder'=>'Mesaj'));
        $content->setRequired(true);
        $this->addElement($content);
        
        $options= array();
        $clients = new Default_Model_Clients();
        $select = $clients->getMapper()->getDbTable()->select()
                                ->where('NOT deleted')
                                ->order('nume ASC');
        $result = $clients->fetchAll($select);

        if(NULL != $result)
        {
            foreach($result as $value){
                $options[$value->getEmail()] = $value->getEmail();
            }
        }
        
        $customEmail = new Zend_Form_Element_Multiselect('emails');		
        $customEmail->setAttribs(array('class'=>'form-control','disabled'=>'disabled'));
        $customEmail->setRequired(false);
        $customEmail->addMultiOptions($options);
        $this->addElement($customEmail);

        $image = new Zend_Form_Element_File('image');
        $image->setRequired(false);                   
        $image->setLabel('File')
                ->setDestination(APPLICATION_UPLOADS_DIR);
//		$image->addValidator('Extension', false, 'jpg,jpeg,png,gif');
//		$image->addValidator('Size', false, 1048576);
        $image->setIgnore(false);
        $this->addElement($image, 'image');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue('Trimite');
        $submit->setAttribs(array('class'=>'btn btn-primary'));
        $submit->setIgnore(true);
        $this->addElement($submit);

    }	
}
