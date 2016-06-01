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

    //BEGIN:ROLES	
    public function showChildsAction()
    {
        $optionArray = array();
        $webRoot = $this->view->baseUrl();	
        if(Needs_Roles::hasAccess(Zend_Registry::get('user')->getIdRole(),'adaugare_rol')){
                $optionArray['addSubLink'] = $webRoot.'/role/add/id/';
                $optionArray['subName'] = 'subrol';	
        }
        if(Needs_Roles::hasAccess(Zend_Registry::get('user')->getIdRole(),'editare_rol')){
                $optionArray['editLink'] = $webRoot.'/role/edit/id/';
        }
        if(Needs_Roles::hasAccess(Zend_Registry::get('user')->getIdRole(),'stergere_rol')){
                $optionArray['deleteLink'] = $webRoot.'/role/delete/id/';
        }
        $response = '';
        $id = $this->getRequest()->getParam('id');
        $parent = $this->getRequest()->getParam('parent');

        $role = new Default_Model_Role();
        $role->find($id);

        //if first, show parent node	
        $showParent = ($parent == 'true') ? true : false;
        $graph = new Needs_Graph($role, $showParent,array('idParent','id','name'),'object',true);
        $childRoles = $graph->getTree();	
        if($childRoles)
        {			
                $last = count($childRoles)-1;
                $response .= "<div class='col-md-6 pull-right'>"  ;
                foreach ($childRoles as $key =>$value)
                {
                        $first = ($value->getId() == Zend_Registry::get('user')->getIdRole())?true:false;
                        $paddingFirst = (!$first)?'20px':'0';		

                        $isFirst = $key == 0 ? 'first' : '';
                        $isLast = $last == $key ? 'last' : '';										
                        $hasChild = (Needs_Graph::hasChild($value) && !$first) ? true : false ;

                        $afterLinks = '';
                        if($hasChild):
                                $afterLinks .= "							
                                                <a id='jsColapse-{$value->getId()}' class='jsColapse' rel='{$value->getId()}' href='javascript:;' title='Colapse'></a>							
                                ";
                        endif;
                        $afterLinks .= "<a class='user-info listingItem roleListing' href='javascript:;' rel='{$value->getId()}' title='Informatii'></a>";
                        if(!empty($optionArray['addSubLink'])){
                                $afterLinks.= '<a class="tipsyTrigger user-add-child" href="'.$optionArray['addSubLink'].$value->getId().'" title="Adauga '.$optionArray['subName'].'"><i class="fa fa-pencil"></i></a>';
                        }
                        if(!empty($optionArray['editLink']) && Zend_Registry::get('user')->getIdRole() != $value->getId())
                        {
                                $afterLinks.= ' <a class="tipsyTrigger user-edit" href="'.$optionArray['editLink'].$value->getId().'" title = "Editare"><i class="fa fa-pencil"></i></a>';
                        }
                        if(!empty($optionArray['deleteLink']) && Zend_Registry::get('user')->getIdRole() != $value->getId())
                        {
                                $afterLinks.= ' <a  href="#myModal_'.$value->getId().'" data-toggle="modal" title="Stergere"><i class="fa fa-times"></i></a>'
                                                . '<div class="modal fade" id="myModal_'.$value->getId().'">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <button aria-hidden="true" class="close" data-dismiss="modal" type="button">×</button>
                                  <h4 class="modal-title">Mobile City</h4>
                                </div>
                                <div class="modal-body">
                                  <h1>Confirmare stergere</h1>
                                  <p>Sunteti sigur ca doriti sa stergeti acest rol?</p>
                                </div>
                                <div class="modal-footer">
                                  <a class="btn btn-primary" href="'.$optionArray['deleteLink'].$value->getId().'">Da</a><button class="btn btn-default-outline" data-dismiss="modal" type="button">Nu</button>

                                </div>
                              </div>
                            </div>
                        </div>';  
                        }				


                        $response .="<div id='user-{$value->getId()}' class='col-md-8 user {$isFirst} {$isLast} listingDiv' style='margin-left: {$paddingFirst}'>
                                                <a class='listingItem roleListing fl' href='javascript:;' rel='{$value->getId()}'>{$value->getName()}</a>	
                                                <div class='col-md-4 pull-right'>
                                                        <div class='user-actions'>
                                                                {$afterLinks}
                                                        </div>
                                                </div>
                                         ";
                                $response .= "
                                        <div class='clear'></div>
                                ";
                                $response .= "
                                </div>
                                ";
                                if($hasChild):
                                        $response .= "<div class='child-element' id='load-child-{$value->getId()}'></div>";
                                endif;

                }
                $response .= '</div>';			

        }
        echo Zend_Json_Encoder::encode($response);
    }	

    public function showResourceAction()
    {
        $id			= $this->getRequest()->getParam('id');
        $coreId		= $this->getRequest()->getParam('coreId');
        $searchtext	= $this->getRequest()->getParam('searchtext');
        //BEGIN:get all resources and categories	

        $canAddResourceRole = true;

        $allResources = Needs_Roles::getAllResources($coreId,false,$canAddResourceRole,$id,$searchtext);
        echo Zend_Json_Encoder::encode($allResources);
        //END:get all resources and categories
    }

    public function saveResourceAction()
    {
        $resourceId = $this->getRequest()->getParam('resourceId');
        $roleId = $this->getRequest()->getParam('roleId');
        $actions = $this->getRequest()->getParam('actions');		

        $return = 'Error occured';

        //BEGIN:save or delete			
        if($actions == 'add')
        {
                $modelRR = new Default_Model_ResourceRole();
                $select3 = $modelRR->getMapper()->getDbTable()->select()
                                        ->where('idResource = ?',$resourceId)
                                        ->where('idRole = ?',$roleId);
                $modelRR->fetchRow($select3);
                if($modelRR->getId() == NULL)
                {
                        $model = new Default_Model_ResourceRole();
                        $model->setIdResource($resourceId);
                        $model->setIdRole($roleId);					
                        if($model->save())
                        {					
                                $return = 'Successfully added';
                                }
                }else{
                        $return = 'Already in database';
                        }
        }elseif($actions == 'remove'){
                $model = new Default_Model_ResourceRole();
                $select3 = $model->getMapper()->getDbTable()->select()
                                        ->where('idResource = ?',$resourceId)
                                        ->where('idRole = ?',$roleId);
                $model->fetchRow($select3);
                if($model->getId() != NULL)
                {				
                        if($model->delete())
                        {
                                //remove the resource from all child elements					
                                $role = new Default_Model_Role();
                                $role->find($roleId);					
                                $graph = new Needs_Graph($role, false,array('idParent','id'),'array');
                                $childRoles = $graph->getTree();				
                                foreach ($childRoles as $value)
                                {
                                    $condition = array(
                                                            'idRole = ?' => $value['id'],
                                                            'idResource = ?' => $resourceId
                                     );						
                                     $this->db->delete('resource_role', $condition);
                                }
                                $return = 'Successfully deleted';
        }
                }			
        }

        echo Zend_Json_Encoder::encode($return);
        //END:save or delete
    }
    public function searchRoleAction()
	{
		//BEGIN:select all sub childs of the current logged in user, that contains '$searchtext'
		$id = $this->getRequest()->getParam('id');		
		$searchtext= $this->getRequest()->getParam('searchtext');
		
		$optionArray = array();
//		if(Needs_Tools::hasAccess(Zend_Registry::get('user')->getIdRole(),'adaugare_rol')){
			$optionArray['addSubLink'] =  WEBROOT.'role/add/id/';
			$optionArray['subName'] = 'subrol';	
//		}
//		if(Needs_Tools::hasAccess(Zend_Registry::get('user')->getIdRole(),'editare_rol')){
			$optionArray['editLink'] =  WEBROOT.'role/edit/id/';
//		}
//		if(Needs_Tools::hasAccess(Zend_Registry::get('user')->getIdRole(),'stergere_rol')){
			$optionArray['deleteLink'] = WEBROOT.'role/delete/id/';
//		}
		
		$response = 'Nu s-au gasit rezultate!';
		$role = new Default_Model_Role();
		if($role->find($id)){
			$graph = new Needs_Graph($role, false,array('id'),'array');
			$childRoles = $graph->getTree();	
			if($childRoles)
			{
				$ids = array_map(function($item) { return $item['id']; }, $childRoles);
				$model = new Default_Model_Role();		
				$select = $model->getMapper()->getDbTable()->select()						
						->where('name LIKE (?)', '%'.$searchtext.'%')
						->where('id IN (?)', $ids);
				$results = $model->fetchAll($select);								
				if($results)
				{
					$last = count($results)-1;
					$response = " <div class='show-users'>"  ;
					foreach ($results as $key => $value)
					{						
						$isFirst = $key == 0 ? 'first' : '';
						$isLast = $last == $key ? 'last' : '';
						$paddingFirst =  '0';						
						
						$afterLinks = '';
						if($optionArray['addSubLink']){
							$afterLinks.= '<a class="user-add-child" href="'.$optionArray['addSubLink'].$value->getId().'" title="Adauga '.$optionArray['subName'].'"></a>';
						}
						if($optionArray['editLink'] && Zend_Registry::get('user')->getIdRole() != $value->getId())
						{
							$afterLinks.= ' <a class="user-edit" href="'.$optionArray['editLink'].$value->getId().'" title = "Editare"></a>';
						}
						if($optionArray['deleteLink'] && Zend_Registry::get('user')->getIdRole() != $value->getId())
						{
							$afterLinks.= ' <a class="user-delete confirmDelete" href="'.$optionArray['deleteLink'].$value->getId().'" title="Stergere"></a>';  
						}						

						$response .="<div id='user-{$value->getId()}' class='user {$isFirst} {$isLast}' style='margin-left: {$paddingFirst}'>
									<a class='listingItem roleListing fl' href='javascript:;' rel='{$value->getId()}'>{$value->getName()}</a>	
									<div class='fr'>															
										<div class='user-actions'>						
											<a class='user-info listingItem roleListing' href='javascript:;' rel='{$value->getId()}' title='Informatii'></a>
											{$afterLinks}
										</div>
									</div>
								 ";

							$response .= "
								<div class='clear'></div>
							";
							$response .= "
							</div>
							";							
				
					}
					$response .= '</div>';
					
				}
			}
		}	
		echo Zend_Json_Encoder::encode($response);		
	}
        
        public function getNivel2Action(){
            if($this->getRequest()->getParam('id_nivel1')){
                $id     = $this->getRequest()->getParam('id_nivel1');
                $options= '<option value="0">Selecteaza localitatea</option>';		
                $nivele = new Default_Model_Nivel2();
                $select = $nivele->getMapper()->getDbTable()->select()				
                                    ->where('id_nivel1 = '.$id)
                                    ->order('name ASC');
                $result = $nivele->fetchAll($select);

                if(NULL != $result)
                {
                    foreach($result as $value){
                        $options .= '<option value='.$value->getId_nivel2().'>'.$value->getName().'</option>';
                    }
                    
                    echo $options;
                    
                }
                
            } else {
                echo null;
            }
        }
}


