<?php
class Default_Model_DbTable_Nivel1 extends Zend_Db_Table_Abstract
{
	protected $_name    = 'nivel1';
	protected $_primary = 'id_nivel1';
}

class Default_Model_Nivel1
{
	protected $_id_nivel1;
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

	public function setId_nivel1($id)
	{
            $this->_id_nivel1 = (int) $id;
            return $this;
	}

	public function getId_nivel1()
	{
            return $this->_id_nivel1;
	}
        
	public function setName($name)
	{
            $this->_name =  (string) $name;
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
                    $this->setMapper(new Default_Model_Nivel1Mapper());
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

class Default_Model_Nivel1Mapper
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
                    $this->setDbTable('Default_Model_DbTable_Nivel1');
            }
            return $this->_dbTable;
	}

	public function find($id, Default_Model_Nivel1 $model)
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
                    $model = new Default_Model_Nivel1();
                    $model->setOptions($row->toArray())
                                    ->setMapper($this);
                    $entries[] = $model;
            }
            return $entries;
	}
	
	public function fetchRow($select, Default_Model_Nivel1 $model)
	{
            $result=$this->getDbTable()->fetchRow($select);
            if(0 == count($result))
            {
                    return;
            }
            $model->setOptions($result->toArray());
            return $model;
	}
	
//        public function save(Default_Model_TipComanda $value)
//        {
//            $auth = Zend_Auth::getInstance();
//            $authAccount = $auth->getStorage()->read();
//            if (null != $authAccount) {
//                if (null != $authAccount->getId()) {
//                        $user = new Default_Model_Users();
//                        $user->find($authAccount->getId());
//
//                        $data = [
//                                'nume'      => $value->getNume(),
//                                'deleted'   => '0'
//                                ];	
//                        return $id;
//                    }
//            }
//        }
	
//        public function delete(Default_Model_TipComanda $value)
//        {   
//            $auth = Zend_Auth::getInstance();
//            $authAccount = $auth->getStorage()->read();
//            if (null != $authAccount) {
//                if (null != $authAccount->getId()) {
//                    $user = new Default_Model_Users();
//                    $user->find($authAccount->getId());
//
//                    $id = $value->getId();
//
//                    $data = array(					
//                            'deleted' => '1',
//                    );
//                    $this->getDbTable()->update($data, array('id = ?' => $id));     
//
//                    return $id;
//                }
//            }
//	}    
}
