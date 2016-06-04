<?php
class Default_Model_DbTable_ComandaLogs extends Zend_Db_Table_Abstract
{
	protected $_name    = 'comanda_logs';
	protected $_primary = 'id';
}

class Default_Model_ComandaLogs
{
	protected $_id;
	protected $_user_id;	
	protected $_comanda_id;	
	protected $_verb;	
	protected $_tip;	
	protected $_departament_initial;	
	protected $_departament_trimis;	
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
        
        public function setUserId($id)
	{
            $this->_user_id = (int) $id;
            return $this;
	}

	public function getUserId()
	{
            return $this->_user_id;
	}
        
        public function setVerb($value)
	{
            $this->_verb = $value;
            return $this;
	}

	public function getVerb()
	{
            return $this->_verb;
	}
        
        public function setTip($value)
	{
            $this->_tip = $value;
            return $this;
	}

	public function getTip()
	{
            return $this->_tip;
	}
        
	public function setComandaId($id)
	{
            $this->_comanda_id = (int) $id;
            return $this;
	}

	public function getComandaId()
	{
            return $this->_comanda_id;
	}
    
	public function setDepartamentInitial($id)
	{
            $this->_departament_initial = (int) $id;
            return $this;
	}

	public function getDepartamentInitial()
	{
            return $this->_departament_initial;
	}
    
	public function setDepartamentTrimis($id)
	{
            $this->_departament_trimis = (int) $id;
            return $this;
	}

	public function getDepartamentTrimis()
	{
            return $this->_departament_trimis;
	}
        
        public function setCreated($date)
	{
            $this->_created = (!empty($date) && strtotime($date)>0)?strtotime($date):null;
            return $this;
	}

	public function getCreated()
	{
            return $this->_created;
	}
        
	public function setMapper($mapper)
	{
            $this->_mapper = $mapper;
            return $this;
	}

	public function getMapper()
	{
            if(null === $this->_mapper) {
                    $this->setMapper(new Default_Model_ComandaLogsMapper());
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

class Default_Model_ComandaLogsMapper
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
                    $this->setDbTable('Default_Model_DbTable_ComandaLogs');
            }
            return $this->_dbTable;
	}

	public function find($id, Default_Model_ComandaLogs $model)
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
                    $model = new Default_Model_ComandaLogs();
                    $model->setOptions($row->toArray())
                                    ->setMapper($this);
                    $entries[] = $model;
            }
            return $entries;
	}
	
	public function fetchRow($select, Default_Model_ComandaLogs $model)
	{
            $result=$this->getDbTable()->fetchRow($select);
            if(0 == count($result))
            {
                    return;
            }
            $model->setOptions($result->toArray());
            return $model;
	}
	
        public function save(Default_Model_ComandaLogs $value)
        {
            $auth = Zend_Auth::getInstance();
            $authAccount = $auth->getStorage()->read();
            if (null != $authAccount) {
                if (null != $authAccount->getId()) {
                        $user = new Default_Model_Users();
                        $user->find($authAccount->getId());

                        $data = [
                                'user_id'               => $value->getUserId(),
                                'comanda_id'            => $value->getComandaId(),
                                'verb'                  => $value->getVerb(),
                                'tip'                   => $value->getTip(),
                                'departament_initial'   => $value->getDepartamentInitial(),
                                'departament_trimis'    => $value->getDepartamentTrimis(),
                                'created'               => new Zend_Db_Expr('NOW()'),
                                ];
                        
                        $id = $this->getDbTable()->insert($data); 

                        return $id;
                    }
            }
        }

}
