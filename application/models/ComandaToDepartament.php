<?php
class Default_Model_DbTable_ComandaToDepartament extends Zend_Db_Table_Abstract
{
	protected $_name    = 'comanda_to_departament';
	protected $_primary = 'id';
}

class Default_Model_ComandaToDepartament
{
	protected $_id;
	protected $_comanda_id;	
	protected $_departament_id;	

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
    
	public function setComanda_id($id)
	{
            $this->_comanda_id = $id;
            return $this;
	}

	public function getComandaId()
	{
            return $this->_comanda_id;
	}
    
	public function setDepartament_id($id)
	{
            $this->_departament_id = $id;
            return $this;
	}

	public function getDepartamentId()
	{
            return $this->_departament_id;
	}
    
	public function setMapper($mapper)
	{
            $this->_mapper = $mapper;
            return $this;
	}

	public function getMapper()
	{
            if(null === $this->_mapper) {
                    $this->setMapper(new Default_Model_ComandaToDepartamentMapper());
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

class Default_Model_ComandaToDepartamentMapper
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
                    $this->setDbTable('Default_Model_DbTable_ComandaToDepartament');
            }
            return $this->_dbTable;
	}

	public function find($id, Default_Model_ComandaToDepartament $model)
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
                    $model = new Default_Model_ComandaToDepartament();
                    $model->setOptions($row->toArray())
                                    ->setMapper($this);
                    $entries[] = $model;
            }
            return $entries;
	}
	
	public function fetchRow($select, Default_Model_ComandaToDepartament $model)
	{
            $result=$this->getDbTable()->fetchRow($select);
            if(0 == count($result))
            {
                    return;
            }
            $model->setOptions($result->toArray());
            return $model;
	}
	
        public function save(Default_Model_ComandaToDepartament $value)
        {
            $auth = Zend_Auth::getInstance();
            $authAccount = $auth->getStorage()->read();
            if (null != $authAccount) {
                if (null != $authAccount->getId()) {
                        $user = new Default_Model_Users();
                        $user->find($authAccount->getId());

                        $data = [
                                'comanda_id'        => $value->getComandaId(),
                                'departament_id'    => $value->getDepartamentId(),
                                ];	

                        $id = $this->getDbTable()->insert($data); 

                        return $id;
                    }
            }
        }
        
        public function delete(Default_Model_ComandaToDepartament $value)
        {    
            $id = $value->getComandaId();
            
            $result = $this->getDbTable()->delete(array('comanda_id = ?' => $id));
            
            return $result;
        }
}
