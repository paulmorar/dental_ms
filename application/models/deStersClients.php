<?php
class Default_Model_DbTable_Clients extends Zend_Db_Table_Abstract
{
	protected $_name    = 'clienti';
	protected $_primary = 'id';
}

class Default_Model_Clients
{
	protected $_id;
	protected $_nume;
	protected $_id_nivel1;
	protected $_id_nivel2;
	protected $_telefon;
	protected $_email;
	protected $_adresa;
	protected $_firma;
        
	protected $_created;
	protected $_modified;	
	protected $_deleted;	

        protected $_total;
        
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
        
	public function setNume($name)
	{
            $this->_nume =  (string) $name;
            return $this;
	}

	public function getNume()
	{
            return $this->_nume;
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
        
        public function setId_nivel2($id)
	{
            $this->_id_nivel2 = (int) $id;
            return $this;
	}

	public function getId_nivel2()
	{
            return $this->_id_nivel2;
	}
        
	public function setEmail($email)
	{
            $this->_email =  (string) $email;
            return $this;
	}

	public function getEmail()
	{
            return $this->_email;
	}			

	public function setTelefon($value)
	{
            $this->_telefon = (string) $value;
            return $this;
	}

	public function getTelefon()
	{
            return $this->_telefon;
	}
	
	public function setAdresa($value)
	{
            $this->_adresa = (string) $value;
            return $this;
	}
	
	public function getAdresa()
	{
            return $this->_adresa;
	}          
        
        public function setFirma($value)
	{
            $this->_firma = (string) $value;
            return $this;
	}

	public function getFirma()
	{
            return $this->_firma;
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
	
        public function setModified($date)
        {
            $this->_modified = strtotime($date);
            return $this;
        }

        public function getModified()
        {
            return $this->_modified;
        }
	
	public function setDeleted($value)
	{
            $this->_deleted = (int) $value;
            return $this;
	}

	public function getDeleted()
	{
            return $this->_deleted;
	}
        
        public function setTotal($id)
	{
            $this->_total = (int) $id;
            return $this;
	}

	public function getTotal()
	{
            return $this->_total;
	}
        
	public function setMapper($mapper)
	{
            $this->_mapper = $mapper;
            return $this;
	}

	public function getMapper()
	{
            if(null === $this->_mapper) {
                    $this->setMapper(new Default_Model_ClientsMapper());
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

class Default_Model_ClientsMapper
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
                    $this->setDbTable('Default_Model_DbTable_Clients');
            }
            return $this->_dbTable;
	}

	public function find($id, Default_Model_Clients $model)
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
                    $model = new Default_Model_Clients();
                    $model->setOptions($row->toArray())
                                    ->setMapper($this);
                    $entries[] = $model;
            }
            return $entries;
	}
	
	public function fetchRow($select, Default_Model_Clients $model)
	{
            $result=$this->getDbTable()->fetchRow($select);
            if(0 == count($result))
            {
                    return;
            }
            $model->setOptions($result->toArray());
            return $model;
	}
	
        public function save(Default_Model_Clients $value)
        {
            $auth = Zend_Auth::getInstance();
            $authAccount = $auth->getStorage()->read();
            if (null != $authAccount) {
                if (null != $authAccount->getId()) {
                        $user = new Default_Model_Clients();
                        $user->find($authAccount->getId());

                        $data = [
                                'nume'      => $value->getNume(),
                                'id_nivel1' => $value->getId_nivel1(),
                                'id_nivel2' => $value->getId_nivel2(),
                                'telefon'   => $value->getTelefon(),
                                'email'     => $value->getEmail(),
                                'adresa'    => $value->getAdresa(),
                                'firma'     => $value->getFirma(),
                                'deleted'   => '0'
                                ];	
                        if (null === ($id = $value->getId()))
                        {     
                            $data['created']	 = new Zend_Db_Expr('NOW()');
                            $id = $this->getDbTable()->insert($data); 
                        } else {    
                            $data['modified']	 = new Zend_Db_Expr('NOW()');            
                            $this->getDbTable()->update($data, array('id = ?' => $id)); 
                        }
                        return $id;
                    }
            }
        }
	
        public function delete(Default_Model_Clients $value)
        {   
            $auth = Zend_Auth::getInstance();
            $authAccount = $auth->getStorage()->read();
            if (null != $authAccount) {
                if (null != $authAccount->getId()) {
                    $user = new Default_Model_Users();
                    $user->find($authAccount->getId());

                    $id = $value->getId();

                    $this->getDbTable()->delete(array('id = ?' => $id));    

                    return $id;
                }
            }
	}    
}