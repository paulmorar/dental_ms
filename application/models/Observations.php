<?php
class Default_Model_DbTable_Observations extends Zend_Db_Table_Abstract
{
	protected $_name    = 'observations';
	protected $_primary = 'id';
}

class Default_Model_Observations
{
	protected $_id;
	protected $_user;
	protected $_doctor;
	protected $_observation;
	protected $_created;

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
    
	public function setUser($id)
	{
            $this->_user = (int) $id;

            return $this;
	}

	public function getUser()
	{
            return $this->_user;
	}

    public function setDoctor($id)
    {
        $this->_doctor = (int) $id;

        return $this;
    }

    public function getDoctor()
    {
        return $this->_doctor;
    }
    
	public function setObservation($value)
	{
            $this->_observation = $value;
            return $this;
	}

	public function getObservation()
	{
            return $this->_observation;
	}
    public function setCreated($value)
    {
        $this->_created = $value;
        return $this;
    }

    public function getCreated()
    {
        return strtotime($this->_created);
    }
    
	public function setMapper($mapper)
	{
            $this->_mapper = $mapper;
            return $this;
	}

	public function getMapper()
	{
            if(null === $this->_mapper) {
                    $this->setMapper(new Default_Model_ObservationsMapper());
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
            return $this->getMapper()->delete($this);
	}
}

class Default_Model_ObservationsMapper
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
                    $this->setDbTable('Default_Model_DbTable_Observations');
            }
            return $this->_dbTable;
	}

	public function find($id, Default_Model_Observations $model)
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
                    $model = new Default_Model_Observations();
                    $model->setOptions($row->toArray())->setMapper($this);
                    $entries[] = $model;
            }

        return $entries;
	}
	
	public function fetchRow($select, Default_Model_Observations $model)
	{
            $result=$this->getDbTable()->fetchRow($select);
            if(0 == count($result))
            {
                    return;
            }
            $model->setOptions($result->toArray());
            return $model;
	}
	
    public function save(Default_Model_Observations $value)
    {
        $auth = Zend_Auth::getInstance();
        $authAccount = $auth->getStorage()->read();
        if (null != $authAccount) {
            if (null != $authAccount->getId()) {
                    $user = new Default_Model_Users();
                    $user->find($authAccount->getId());

                    $data = [
                            'user'    => $value->getUser(),
                            'doctor'    => $value->getDoctor(),
                            'observation'     => $value->getObservation(),
                            ];

                    $id = $this->getDbTable()->insert($data);

                    return $id;
                }
        }
    }
}
