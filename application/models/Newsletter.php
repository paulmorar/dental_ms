<?php
class Default_Model_DbTable_Newsletter extends Zend_Db_Table_Abstract
{
	protected $_name    = 'newsletter'; 
        protected $_primary = 'id';
}

class Default_Model_Newsletter
{
    protected $_id;
    protected $_title;
    protected $_message;
    protected $_image;
    protected $_sent;
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
    
    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }
    
    public function setTitle($string)
    {
            $this->_title = (!empty($string))?(string) strip_tags($string):null;
            return $this;
    }

    public function getTitle()
    {
            return $this->_title;
    }

    public function setMessage($string)
    {
            $this->_message = (!empty($string))?(string) strip_tags($string):null;
            return $this;
    }

    public function getMessage()
    {
            return $this->_message;
    }

    public function setImage($string)
    {
            $this->_image = (!empty($string))?(string) strip_tags($string):null;
            return $this;
    }
    
    public function setSent($string)
    {
            $this->_sent = (!empty($string))?(string) strip_tags($string):null;
            return $this;
    }

    public function getSent()
    {
            return $this->_sent;
    }
    
    public function getImage()
    {
            return $this->_image;
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
                    $this->setMapper(new Default_Model_NewsletterMapper());
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

class Default_Model_NewsletterMapper
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
			$this->setDbTable('Default_Model_DbTable_Newsletter');
		}
		return $this->_dbTable;
	}

	public function find($id, Default_Model_Newsletter $model)
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
			$model = new Default_Model_Newsletter();
			$model->setOptions($row->toArray())
					->setMapper($this);
			$entries[] = $model;
		}
		return $entries;
	}

        public function fetchRow($select, Default_Model_Newsletter $model)
        {		
            $result=$this->getDbTable()->fetchRow($select);
            if(0 == count($result)) 
                    {
                return;
            }      	
            $model->setOptions($result->toArray());
                    return $model;
        }
	
	public function save(Default_Model_Newsletter $model)
	{
            $data = array(			
                    'title'		=> $model->getTitle(),
                    'message'           => $model->getMessage(),			
                    'image'             => ($model->getImage()) ? $model->getImage() : '',
                    'sent'		=> $model->getSent(),
                    'created'           => new Zend_Db_Expr('NOW()')
            );	

            $id = $this->getDbTable()->insert($data);			
            return $id;
	}
}