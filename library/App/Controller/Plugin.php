<?php
class App_Controller_Plugin extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if(!empty($_POST['PHPSESSID'])){
            session_id($_POST['PHPSESSID']);
        }
        // GET MODULE/CONTROLLER/ACTION
        $module			= $request->getModuleName();
        $controller		= $request->getControllerName();
        $action			= $request->getActionName();

        $auth	 = Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session());

        // SEND MODULE/CONTROLLER/ACTION
        $layout	= Zend_Layout::getMvcInstance();
        $layout->getView()->module			= $module;
        $layout->getView()->controller                  = $controller;
        $layout->getView()->action			= $action;


        // BEGIN: Translate
        setlocale(LC_ALL, 'en_US.UTF-8');
        Zend_Registry::set('lang', 'ro');
        Zend_Registry::set('lang_id', '1');

        $adminLang = 'en';
        $translate = new Zend_Translate('csv', 'data/lang/ro.csv', $adminLang);
        $translate->setLocale($adminLang);

        Zend_Registry::set('translate', $translate);
        // END: Translate

        $acl = new Zend_Acl();
        $acl->add(new Zend_Acl_Resource('default:auth'));
        $acl->add(new Zend_Acl_Resource('default:index'));

        //BEGIN:ROLES
        $acl->addRole(new Zend_Acl_Role('guest'));
        $acl->allow('guest', 'default:auth', 'login');
        $acl->allow('guest', 'default:auth', 'index');

        $roles = Needs_Roles::fetchAllRoles();
        if($roles){
            foreach ($roles as $value) {
                $acl->addRole(new Zend_Acl_Role($value->getId()));
                $acl->deny($value->getId(), 'default:auth', 'login');
            }
        }
        //END:ROLES

        $accountRole = 'guest';
        if ($auth->hasIdentity()) {
            $accountAuth = $auth->getStorage()->read();
            $user = new Default_Model_Users();
            if(!$user->find($accountAuth->getId()))
            {
                $this->_response->setRedirect(WEBROOT . 'auth/logout');
            }
            Zend_Registry::set('user', $user);
            if($accountAuth){
                    $accountRole = $user->getIdRole();

                    $isAdmin = false;
                    if(Needs_Roles::isAdmin($user->getIdRole()))
                    {
                        $isAdmin = true;
                    }
                    Zend_Registry::set('isAdmin', $isAdmin);
            }
        }


    	switch($module){
            //front-end
            default:
                $layout->setLayout('admin');

            $pages = array();

        //BEGIN:MENIU+RESOURCES
            $arrResources = array('default:index','default:auth');

            $resourcesGroup = Needs_Roles::fetchAllResourceGroups();
            if($resourcesGroup){
                foreach($resourcesGroup as $key=>$modelMenu)
                {
                    //fetch resources by resource group
                    $submenu = new Default_Model_Resource();
                    $select = $submenu->getMapper()->getDbTable()->select()
                                ->where('deleted = ?', 0)
                                ->where('idGroup = ?', $modelMenu->getId())
                                ->order('firstNode DESC');
                    $arrSubMenu = $submenu->fetchAll($select);
                    foreach($arrSubMenu as $submenu) {
                        if($submenu->getController() == NULL)
                        {
                            continue;
                        }

                        $modul = ($submenu->getModule() != NULL)?$submenu->getModule():'default';
                        $resource = $modul.':'.$submenu->getController().':'.$submenu->getAction();

                        //chack if has access
                        $hasaccess = Needs_Roles::hasAccessbyId($accountRole, $submenu->getId());
                        //check if resource is already made
                        if(!in_array($resource,$arrResources)) {
                            //add resource to acl and to $arrResources
                            $acl->add(new Zend_Acl_Resource($resource));
                            $arrResources[] = $resource;
                            if($hasaccess){
                                //allow on modul:controller (resource)
                                $acl->allow($accountRole, $resource);
                                $acl->deny('guest',$resource);
                            }
                        }

                        if($submenu->getInMeniu()){

                        //BEGIN:TOP MENIU
                        $visible = $submenu->getVisible()?true:false;
                        if($submenu->getFirstNode()){
                            $pages[$key] = array(
                                    'label'             => "<i class=\"".$modelMenu->getIconClass()."\"></i><span>".$modelMenu->getName()."</span>",
                                    'title'		=> $modelMenu->getName(),
                                    'module'		=> $modul,
                                    'controller'	=> $submenu->getController(),
                                    'action'		=> $submenu->getAction(),
                                    'resource'		=> $resource,
                                    'class'		=> '',
                                    'visible'		=> $visible,
                                );
                        }
                        //END:TOP MENIU

                        //BEGIN:SUBMENIU
                        $label = $submenu->getDescription();

                        $pages[$key]['pages'][] = array(
                                            'label'		=> $label,
                                            'title'		=> $label,
                                            'module'		=> $modul,
                                            'controller'	=> $submenu->getController(),
                                            'action'		=> $submenu->getAction(),
                                            'resource'		=> $resource,
                                            'visible'		=> $visible
                                        );
                        //END:SUBMENIU
                        }
                    }
                }

                //allow on index if logged in
                if ($auth->hasIdentity()) {
                        $acl->allow($accountRole,'default:index','index');
                        $acl->deny($accountRole, 'default:auth', 'login');
                        $acl->deny($accountRole);
                }
                $acl->deny('guest','default:index');
                //END:MENIU+RESOURCES
                }

                // Create container from array
                $container = new Zend_Navigation($pages);
                $layout->getView()->navigation($container)->setAcl($acl)->setRole($accountRole);
                $layout->getView()->headTitle('Dental MS', 'SET');

                $stylesheets = $layout->getView()->headLink();
                $stylesheets->appendStylesheet(WEBROOT . 'theme/css/bootstrap.min.css');
                $stylesheets->appendStylesheet(WEBROOT . 'theme/css/admin.css');
                $stylesheets->appendStylesheet(WEBROOT . 'theme/css/style.css');
                $stylesheets->appendStylesheet(WEBROOT . 'theme/css/_all-skins.min.css');
                $stylesheets->appendStylesheet(WEBROOT . 'theme/css/fonts.css');
                $stylesheets->appendStylesheet(WEBROOT . 'theme/css/jquery-ui.css');
                $stylesheets->appendStylesheet(WEBROOT . 'theme/plugins/selectize/css/selectize.bootstrap3.css');
                $stylesheets->appendStylesheet(WEBROOT . 'theme/plugins/datatables/dataTables.bootstrap.css');
                $stylesheets->appendStylesheet(WEBROOT . 'theme/plugins/validation/validationEngine.jquery.css');
                $stylesheets->appendStylesheet(WEBROOT . 'theme/plugins/daterangepicker/daterangepicker-bs3.css');
                $stylesheets->appendStylesheet(WEBROOT . 'theme/css/jquery.fancybox.css');


                $javascripts = $layout->getView()->headScript();
                $javascripts->prependFile(WEBROOT.'theme/js/bootstrap.min.js');
                $javascripts->prependFile(WEBROOT.'theme/js/app.min.js');
                $javascripts->prependFile(WEBROOT.'theme/plugins/highcharts/js/highcharts.js');
                $javascripts->prependFile(WEBROOT.'theme/js/jquery-ui.js');
                $javascripts->prependFile(WEBROOT.'theme/plugins/datatables/dataTables.bootstrap.min.js');
                $javascripts->prependFile(WEBROOT.'theme/plugins/datatables/jquery.dataTables.min.js');
                $javascripts->prependFile(WEBROOT.'theme/plugins/validation/jquery.validationEngine-ro.js');
                $javascripts->prependFile(WEBROOT.'theme/plugins/validation/jquery.validationEngine.js');
                $javascripts->prependFile(WEBROOT.'theme/plugins/selectize/js/standalone/selectize.js');
                $javascripts->prependFile(WEBROOT.'theme/plugins/chartjs/Chart.js');

                $javascripts->prependFile(WEBROOT.'theme/plugins/daterangepicker/daterangepicker.js');
                $javascripts->prependFile(WEBROOT.'theme/plugins/daterangepicker/moment.min.js');
                $javascripts->prependFile(WEBROOT.'theme/js/jquery.fancybox.js');
                $javascripts->prependFile(WEBROOT.'theme/plugins/jQuery/jQuery-2.1.4.min.js');

                switch($controller) {
                        case 'error':
                                switch($action) {
                                        case 'error' :
                                                $layout->setLayout('error');
                                                break;
                                        default:
                                                break;
                                }
                                break;
                        case 'iframe':
                                $layout->setLayout('iframe');
                                break;
                        case 'Api':
                                $layout->setLayout('layout');
                                break;
                        case 'auth':
                                $layout->setLayout('auth');
                                switch($action) {
                                        case 'login' :
                                                $layout->getView()->headTitle('Login', 'SET');
                                                if(!$acl->isAllowed($accountRole,'default:auth', 'login')) {
                                                    if($accountRole == 3){
                                                        $this->_response->setRedirect(WEBROOT.'pacienti/show/id/1');
                                                        $layout->setLayout('layout_user');
                                                    } else {
                                                        $this->_response->setRedirect(WEBROOT.'pacienti');
                                                        $layout->setLayout('layout');
                                                    }
                                                }
                                                break;
                                        default:
                                                break;
                                }
                                break;
                        default :
                            if($accountRole == 3){
                                $layout->setLayout('layout_user');
                            } else {
                                $layout->setLayout('layout');
                            }

                    if(!$acl->has($module.':'.$controller.':'.$action)){
                        $acl->add(new Zend_Acl_Resource($module.':'.$controller.':'.$action));
                    }

                    if (($auth->hasIdentity())) {
                        if(!$acl->isAllowed($accountRole,$module.':'.$controller.':'.$action) && !Zend_Registry::get('isAdmin')) {
                            $this->_response->setRedirect(WEBROOT . 'auth/login');
                        }
                    }else{
                        $this->_response->setRedirect(WEBROOT . 'auth/login');
                    }
                break;
                }
        break;
        }
    }
}