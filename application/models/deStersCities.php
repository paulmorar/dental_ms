<?php
class Default_Model_DbTable_Cities extends Zend_Db_Table_Abstract
{
	protected $_name    = 'cities';
	protected $_primary = 'idLevel2';
}

class Default_Model_Cities
{
	protected $_idLevel2;
	protected $_idLevel1;
	protected $_name;

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

	public function setIdLevel2($id)
	{
		$this->_idLevel2 = (int) $id;
		return $this;
	}

	public function getIdLevel2()
	{
		return $this->_idLevel2;
	}			

	public function setIdLevel1($id)
	{
		$this->_idLevel1 = (int) $id;
		return $this;
	}

	public function getIdLevel1()
	{
		return $this->_idLevel1;
	}			

        public function setName($value)
	{
		$this->_name = (string) strip_tags($value);
		return $this;
	}

	public function getName()
	{
		return $this->_name;
	}        

	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper()
	{
		if(null === $this->_mapper) {
			$this->setMapper(new Default_Model_CitiesMapper());
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

class Default_Model_CitiesMapper
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
			$this->setDbTable('Default_Model_DbTable_Cities');
		}
		return $this->_dbTable;
	}

	public function find($id, Default_Model_Cities $model)
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
			$model = new Default_Model_Cities();
			$model->setOptions($row->toArray())
					->setMapper($this);
			$entries[] = $model;
		}
		return $entries;
	}
	
	public function fetchRow($select, Default_Model_Cities $model)
	{
		$result=$this->getDbTable()->fetchRow($select);
		if(0 == count($result))
		{
			return;
		}
		$model->setOptions($result->toArray());
		return $model;
	}
	
	public function save(Default_Model_Cities $value)
    {
		$auth = Zend_Auth::getInstance();
		$authAccount = $auth->getStorage()->read();
		if (null != $authAccount) {
			if (null != $authAccount->getId()) {
				$user = new Default_Model_Cities();
				$user->find($authAccount->getId());
		
        $data = array(	
                        'idLevel2'	=> $value->getIdLevel2(),
                        'idLevel1'	=> $value->getIdLevel1(),
                        'name'        => $value->getName(),
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
