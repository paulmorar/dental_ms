<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoload()
    {	
		Zend_Loader_Autoloader::getInstance()->registerNamespace('Needs_');		
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Default_',			
            'basePath'  => dirname(__FILE__),
        ));
        return $autoloader;
    }
    
    //initiate ZendX
    protected function _initViewHelpers()
    {
        $view = new Zend_View();
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        
        $view->addHelperPath('ZendX/JQuery/View/Helper/','ZendX_JQuery_View_Helper');
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    }
}