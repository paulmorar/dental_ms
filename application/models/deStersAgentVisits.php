<?php
class Default_Model_DbTable_AgentVisits extends Zend_Db_Table_Abstract
{
	protected $_name    = 'agent_visits';
	protected $_primary = 'id';
}

class Default_Model_AgentVisits
{
	protected $_id;
	protected $_idAgent;
	protected $_idClient;
	protected $_visitDates;
	protected $_comment;
		
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

	public function setIdClient($value)
	{
		$this->_idClient = (int) ($value);
		return $this;
	}

	public function getIdClient()
	{
		return $this->_idClient;
	}

	public function setVisitDates($value)
	{
		$this->_visitDates = (string) strip_tags($value);
		return $this;
	}

	public function getVisitDates()
	{
		return $this->_visitDates;
	} 

        public function setComment($string)
        {
            $this->_comment = (!empty($string))?(string) strip_tags($string):null;
            return $this;
        }

        public function getComment()
        {
            return $this->_comment;
        }
    
	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper()
	{
		if(null === $this->_mapper) {
			$this->setMapper(new Default_Model_AgentVisitsMapper());
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

class Default_Model_AgentVisitsMapper
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
			$this->setDbTable('Default_Model_DbTable_AgentVisits');
		}
		return $this->_dbTable;
	}

	public function find($id, Default_Model_AgentVisits $model)
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
			$model = new Default_Model_AgentVisits();
			$model->setOptions($row->toArray())
					->setMapper($this);
			$entries[] = $model;
		}
		return $entries;
	}
	
	public function fetchRow($select, Default_Model_AgentVisits $model)
	{
		$result=$this->getDbTable()->fetchRow($select);
		if(0 == count($result))
		{
			return;
		}
		$model->setOptions($result->toArray());
		return $model;
	}
	
	public function save(Default_Model_AgentVisits $value)
    {
		$auth = Zend_Auth::getInstance();
		$authAccount = $auth->getStorage()->read();
		if (null != $authAccount) {
			if (null != $authAccount->getId()) {
				$user = new Default_Model_AgentVisits();
				$user->find($authAccount->getId());
		
        $data = array(	
                        'idAgent'					=> $value->getIdAgent(),
                        'idClient'					=> $value->getIdClient(),
                        'visitDates'					=> $value->getVisitDates(),                        
                        'comment'					=> $value->getComment()                        
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
        public function delete(Default_Model_AgentVisits $value)
    {   
        $id = $value->getId();       
        $this->getDbTable()->delete(array('id = ?' => $id));
        return $id;
    }
}
