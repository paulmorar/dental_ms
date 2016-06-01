<?php
class Default_Model_DbTable_Agents extends Zend_Db_Table_Abstract
{
	protected $_name    = 'agents';
	protected $_primary = 'id';
}

class Default_Model_Agents
{
	protected $_id;
	protected $_agent;
	protected $_car;
        protected $_status;
	protected $_created;
	protected $_modified;	
	protected $_deleted;	

        protected $_client;
        protected $_company;
        protected $_products;
	protected $_idCounty;
	protected $_address;             
	protected $_cui;   
        protected $_phone;
        protected $_phoneMedic;
        protected $_email;
        
        
        
        protected $_idAgent;
        protected $_idVisit;
        protected $_idClient;
        protected $_visitDates;
        
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

	public function setAgent($value)
	{
		$this->_agent = (string) strip_tags($value);
		return $this;
	}

	public function getAgent()
	{
		return $this->_agent;
	}

	public function setCar($value)
	{
		$this->_car = (string) strip_tags($value);
		return $this;
	}

	public function getCar()
	{
		return $this->_car;
	}
	
	public function setPhone($value)
	{
		$this->_phone = (string) strip_tags($value);
		return $this;
	}

	public function getPhone()
	{
		return $this->_phone;
	}
        
	public function setEmail($value)
	{
		$this->_email = (string) strip_tags($value);
		return $this;
	}
	
	public function getEmail()
	{
		return $this->_email;
	}
        
	public function setStatus($value)
	{
		$this->_status = (!empty($value))?1:0;
		return $this;
	}

	public function getStatus()
	{
		return $this->_status;
	}        
        
	public function setIdVisit($value)
	{
		$this->_idVisit = (int) ($value);
		return $this;
	}

	public function getIdVisit()
	{
		return $this->_idVisit;
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
        
        public function setIdAgent($value)
	{
		$this->_idAgent = (int) ($value);
		return $this;
	}

	public function getIdAgent()
	{
		return $this->_idAgent;
	}
        
        public function setVisitDates($date)
	{
		$this->_visitDates = (!empty($date) && strtotime($date)>0)?strtotime($date):null;
		return $this;
	}
	
	public function getVisitDates()
	{
		return $this->_visitDates;
	}        
        
        public function setClient($value)
	{
		$this->_client = (string) strip_tags($value);
		return $this;
	}

	public function getClient()
	{
		return $this->_client;
	}

	public function setCompany($value)
	{
		$this->_company = (string) strip_tags($value);
		return $this;
	}

	public function getCompany()
	{
		return $this->_company;
	}

        public function setProducts($value)
	{
		$this->_products = (string) strip_tags($value);
		return $this;
	}

	public function getProducts()
	{
		return $this->_products;
	}
        
        public function setIdCounty($value)
	{
		$this->_idCounty = (int) ($value);
		return $this;
	}

	public function getIdCounty()
	{
		return $this->_idCounty;
	}
        
	public function setAddress($value)
	{
		$this->_address = (string) strip_tags($value);
		return $this;
	}

	public function getAddress()
	{
		return $this->_address;
	}
        
	public function setCui($value)
	{
		$this->_cui = (string) strip_tags($value);
		return $this;
	}

	public function getCui()
	{
		return $this->_cui;
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
    
	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper()
	{
		if(null === $this->_mapper) {
			$this->setMapper(new Default_Model_AgentsMapper());
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
    
    public function changeStatus()
	{
		if(null === ($id = $this->getId())) {
			throw new Exception('Invalid record selected!');
		}
		return $this->getMapper()->changeStatus($this);
	}
}

class Default_Model_AgentsMapper
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
			$this->setDbTable('Default_Model_DbTable_Agents');
		}
		return $this->_dbTable;
	}

	public function find($id, Default_Model_Agents $model)
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
			$model = new Default_Model_Agents();
			$model->setOptions($row->toArray())
					->setMapper($this);
			$entries[] = $model;
		}
		return $entries;
	}
	
	public function fetchRow($select, Default_Model_Agents $model)
	{
		$result=$this->getDbTable()->fetchRow($select);
		if(0 == count($result))
		{
			return;
		}
		$model->setOptions($result->toArray());
		return $model;
	}
	
	public function save(Default_Model_Agents $value)
    {
		$auth = Zend_Auth::getInstance();
		$authAccount = $auth->getStorage()->read();
		if (null != $authAccount) {
			if (null != $authAccount->getId()) {
				$user = new Default_Model_Agents();
				$user->find($authAccount->getId());
		
        $data = array(	
                        'agent'					=> $value->getAgent(),
                        'car'					=> $value->getCar(),                       
                        'status'				=> ($value->getStatus() == 1)?$value->getStatus():0,
			'deleted'				=> '0',
        );	
       if (null === ($id = $value->getId()))
		{     
			$data['created']	 = new Zend_Db_Expr('NOW()');
            $id = $this->getDbTable()->insert($data); 
        } 
		else 
		{    
            $data['modified']	 = new Zend_Db_Expr('NOW()');            
            $this->getDbTable()->update($data, array('id = ?' => $id)); 
        }
        return $id;
			}
		}
			
    }
	
   public function delete(Default_Model_Agents $value)
    {   $auth = Zend_Auth::getInstance();
		$authAccount = $auth->getStorage()->read();
		if (null != $authAccount) {
			if (null != $authAccount->getId()) {
				$user = new Default_Model_Agents();
				$user->find($authAccount->getId());
				
				$id = $value->getId();
				$data = array(					
					'deleted' => '1');
				$this->getDbTable()->update($data, array('id = ?' => $id));     
				return $id;
				}
	    }
	}
    
    public function changeStatus(Default_Model_Agents $value)
    {  		
        $id = $value->getId();
        $data = array(					
            'status' => ($value->getStatus() == 1) ? 0 : 1,
        );
        $result = $this->getDbTable()->update($data, array('id = ?' => $id));    
        return $result;
	}
}
