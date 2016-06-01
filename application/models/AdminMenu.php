<?php
class Default_Model_DbTable_AdminMenu extends Zend_Db_Table_Abstract
{
	protected $_name    = 'admin_menu';
	protected $_primary = 'id';
}

class Default_Model_AdminMenu
{
	protected $_id;
	protected $_idParent;
	protected $_menuName;
	protected $_menuConstant;
	protected $_iconClass;
	protected $_module ;
	protected $_controller;
	protected $_method;	
	protected $_params;	
	protected $_order;
	protected $_displayIt;
	protected $_dashboardDisplay;
	protected $_dashboardIcon;
	protected $_deleted;
		
	protected $_mapper;

	public function __construct(array $options = null)
	{
		if(is_array($options)) {
			$this->setOptions($options);
		}
	}

	public function __set($name, $value)
	{
		$method = 'set' . $name;
		if(('mapper' == $name) || !method_exists($this, $method))
		{
			throw new Exception('Invalid '.$name.' property '.$method);
		}
		$this->$method($value);
	}

	public function __get($name)
	{
		$method = 'get' . $name;
		if(('mapper' == $name) || !method_exists($this, $method))
		{
			throw new Exception('Invalid '.$name.' property '.$method);
		}
		return $this->$method();
	}

	public function setOptions(array $options)
	{
		$methods = get_class_methods($this);
		foreach($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if(in_array($method, $methods)) {
				$this->$method($value);
			}
		}
		return $this;
	}

	public function setId($value)
	{
		$this->_id = (!empty($value))?(int) $value:'0';
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}
	
	public function setIdParent($value)
	{
		$this->_idParent = (!empty($value))?(int) $value:'0';
		return $this;
	}

	public function getIdParent()
	{
		return $this->_idParent;
	}
	
	public function setMenuName($string)
	{
		$this->_menuName = (string) strip_tags($string);
		return $this;
	}

	public function getMenuName()
	{
		return $this->_menuName;
	}
	
	public function setMenuConstant($string)
	{
		$this->_menuConstant = (string) strip_tags($string);
		return $this;
	}

	public function getMenuConstant()
	{
		return $this->_menuConstant;
	}
	
	public function setIconClass($string)
	{
		$this->_iconClass = (string) strip_tags($string);
		return $this;
	}

	public function getIconClass()
	{
		return $this->_iconClass;
	}
	
	public function setModule($string)
	{
		$this->_module = (string) strip_tags($string);
		return $this;
	}

	public function getModule()
	{
		return $this->_module;
	}
	
	public function setController($string)
	{
		$this->_controller = (string) strip_tags($string);
		return $this;
	}

	public function getController()
	{
		return $this->_controller;
	}
	
	public function setMethod($string)
	{
		$this->_method = (string) strip_tags($string);
		return $this;
	}

	public function getMethod()
	{
		return $this->_method;
	}
	
	public function setParams($string)
	{
		$this->_params = (string) strip_tags($string);
		return $this;
	}

	public function getParams()
	{
		return $this->_params;
	}
	
	public function setOrder($value)
	{
		$this->_order = (int)$value;
		return $this;
	}

	public function getOrder()
	{
		return $this->_order;
	}
	
	public function setDisplayIt($value)
	{
		$this->_displayIt = (int)$value;
		return $this;
	}

	public function getDisplayIt()
	{
		return $this->_displayIt;
	}
		
	public function setDashboardDisplay($value)
	{
		$this->_dashboardDisplay = (int) $value;
		return $this;
	}

	public function getDashboardDisplay()
	{
		return $this->_dashboardDisplay;
	}
	
	public function setDashboardIcon($value)
	{
		$this->_dashboardIcon = (string) strip_tags($value);
		return $this;
	}

	public function getDashboardIcon()
	{
		return $this->_dashboardIcon;
	}
	
	public function setDeleted($value)
	{
		$this->_deleted = (int)$value;
		return $this;
	}

	public function getDeleted()
	{
		return $this->_deleted;
	}
	
	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}
	
	public function getMapper()
	{
		if(null === $this->_mapper) {
			$this->setMapper(new Default_Model_AdminMenuMapper());
		}
		return $this->_mapper;
	}

	public function find($id)
	{
		return $this->getMapper()->find($id, $this);
	}

	public function fetchAll($select=null)
	{
		return $this->getMapper()->fetchAll($select);
	}
	
	public function fetchRow($select =null)
    {
        return $this->getMapper()->fetchRow($select,$this);
    }
	
	public function save()
	{
		return $this->getMapper()->save($this);
	}

    public function delete()
    {
    	if(null === ($id = $this->getId())) {
			throw new Exception('Invalid record selected!');
		}
		return $this->getMapper()->delete($this);
    }
}

class Default_Model_AdminMenuMapper
{
	protected $_dbTable;

	public function setDbTable($dbTable)
	{
		if(is_string($dbTable)) {
			$dbTable = new $dbTable();
		}
		if(!$dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_dbTable = $dbTable;
		return $this;
	}

	public function getDbTable()
	{
		if(null === $this->_dbTable) {
			$this->setDbTable('Default_Model_DbTable_AdminMenu');
		}
		return $this->_dbTable;
	}

	public function find($id, Default_Model_AdminMenu $model)
	{
		$result = $this->getDbTable()->find($id);
		if(0 == count($result)) {
			return;
		}
		$row = $result->current();
		$model->setOptions($row->toArray());
		return $model;
	}

	public function fetchAll($select)
	{
		$resultSet = $this->getDbTable()->fetchAll($select);
		
		$entries = array();
		foreach($resultSet as $row) {
			$model = new Default_Model_AdminMenu();			
			$model->setOptions($row->toArray())
					->setMapper($this);			
			$entries[] = $model;
		}		
		return $entries;
	}
	
	public function fetchRow($select, Default_Model_AdminMenu $model)
    {		
		$result=$this->getDbTable()->fetchRow($select);		 
        if(0 == count($result)) 
		{
            return;
			
        }      	
        $model->setOptions($result->toArray());
		return $model;
    }
	
	public function save(Default_Model_AdminMenu $value)
    {
	
        $data = array(
					'dashboardDisplay'=>	$value->getDashboardDisplay(),
        );
		
        if (null === ($id = $value->getId()))
		{			
			$id = $this->getDbTable()->insert($data);			
        } 
		else 
		{
			$this->getDbTable()->update($data, array('id = ?' => $id));            
        }
        return $id;
    }	

    public function delete(Default_Model_AdminMenu $value)
    {
		$id = $value->getId();
		$data = array(					
			'deleted' => $value->getDeleted()
		);
		
		$this->getDbTable()->update($data, array('id = ?' => $id));     
        
        return $id;
//    	$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $id);
//        return $this->getDbTable()->delete($where);
    }
	
	public function changeStatus(Default_Model_AdminMenu $value)
    {    
		$id = $value->getId();
		$data = array(					
			'status' => $value->getStatus(),			
		);
		$this->getDbTable()->update($data, array('id = ?' => $id));     
        
        return $id;
    }
}
?>