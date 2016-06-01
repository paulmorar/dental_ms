<?php
class Default_Model_DbTable_Service extends Zend_Db_Table_Abstract
{
	protected $_name    = 'service';
	protected $_primary = 'id';
}

class Default_Model_Service
{
	protected $_id;
	protected $_idAgent;
	protected $_idCar;
	protected $_lastService;
	protected $_kmIndex;

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
		if(('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid '.$name.' property '.$method);
		}
		$this->$method($value);
	}

	public function __get($name)
	{
		$method = 'get' . $name;
		if(('mapper' == $name) || !method_exists($this, $method)) {
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
				$this->$method(stripcslashes($value));
			}
		}
		return $this;
	}

	public function setId($id)
	{
		$this->_id = (int) $id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}			

        public function setIdAgent($value)
	{
		$this->_idAgent = (int) ($value);
		return $this;
	}

	public function getIdAgent()
	{
		return $this->_idAgent;
	}
        
        public function setIdCar($value)
	{
		$this->_idCar = (int) ($value);
		return $this;
	}

	public function getIdCar()
	{
		return $this->_idCar;
	}

        public function setLastService($value)
	{
		$this->_lastService = (string) strip_tags($value);
		return $this;
	}
	
	public function getLastService()
	{
		return $this->_lastService;
	}    

        public function setKmIndex($value)
	{
		$this->_kmIndex = (int) $value;
		return $this;
	}

	public function getKmIndex()
	{
		return $this->_kmIndex;
	}
        
	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper()
	{
		if(null === $this->_mapper) {
			$this->setMapper(new Default_Model_ServiceMapper());
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
}

class Default_Model_ServiceMapper
{
	protected $_dbTable;

	public function setDbTable($dbTable)
	{
		if(is_string($dbTable))
		{
			$dbTable = new $dbTable();
		}
		if(!$dbTable instanceof Zend_Db_Table_Abstract)
		{
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_dbTable = $dbTable;
		return $this;
	}

	public function getDbTable()
	{
		if(null === $this->_dbTable)
		{
			$this->setDbTable('Default_Model_DbTable_Service');
		}
		return $this->_dbTable;
	}

	public function find($id, Default_Model_Service $model)
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
			$model = new Default_Model_Service();
			$model->setOptions($row->toArray())
					->setMapper($this);
			$entries[] = $model;
		}
		return $entries;
	}
	
	public function fetchRow($select, Default_Model_Service $model)
	{
		$result=$this->getDbTable()->fetchRow($select);
		if(0 == count($result))
		{
			return;
		}
		$model->setOptions($result->toArray());
		return $model;
	}
	
	public function save(Default_Model_Service $value)
    {
		$auth = Zend_Auth::getInstance();
		$authAccount = $auth->getStorage()->read();
		if (null != $authAccount) {
			if (null != $authAccount->getId()) {
				$user = new Default_Model_Service();
				$user->find($authAccount->getId());
		
        $data = array(	
                        'id'	=> $value->getId(),
                        'idAgent'	=> $value->getIdAgent(),
                        'idCar'	=> $value->getIdCar(),
                        'lastService'        => $value->getLastService(),
                        'kmIndex'        => $value->getKmIndex(),
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
		}
			
    }
}
