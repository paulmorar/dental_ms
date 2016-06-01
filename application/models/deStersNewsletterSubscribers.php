<?php
class Default_Model_DbTable_NewsletterSubscribers extends Zend_Db_Table_Abstract
{
	protected $_name    = 'newsletter_subscribers'; 
        protected $_primary = 'id';
}

class Default_Model_NewsletterSubscribers
{
	protected $_email;

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
		if (('mapper' == $name) || !method_exists($this, $method)) {
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

	public function setEmail($string)
	{
		$this->_email = (!empty($string))?(string) strip_tags($string):null;
		return $this;
	}

	public function getEmail()
	{
		return $this->_email;
	}

	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper()
	{
		if(null === $this->_mapper) {
			$this->setMapper(new Default_Model_NewsletterSubscribersMapper());
		}
		return $this->_mapper;
	}

	public function find($id)
	{
		return $this->getMapper()->find($id, $this);
	}

	public function fetchRow($select =null)
    {
        return $this->getMapper()->fetchRow($select,$this);
    }
	
	public function fetchAll($select = null)
	{
		return $this->getMapper()->fetchAll($select);
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

class Default_Model_NewsletterSubscribersMapper
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
                    $this->setDbTable('Default_Model_DbTable_NewsletterSubscribers');
            }
            return $this->_dbTable;
    }

    public function find($id, Default_Model_NewsletterSubscribers $model)
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
                    $model = new Default_Model_NewsletterSubscribers();
                    $model->setOptions($row->toArray())
                                    ->setMapper($this);
                    $entries[] = $model;
            }
            return $entries;
    }

    public function fetchRow($select, Default_Model_NewsletterSubscribers $model)
    {		
		$result=$this->getDbTable()->fetchRow($select);
        if(0 == count($result)) 
		{
            return;
        }      	
        $model->setOptions($result->toArray());
		return $model;
    }
	
    public function save(Default_Model_NewsletterSubscribers $model)
    {

            $data = array(			
                    'email'=> $model->getEmail()
            );	

            $id = $this->getDbTable()->insert($data);			
            return $id;
    }
}