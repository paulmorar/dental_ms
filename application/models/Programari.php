<?php
class Default_Model_DbTable_Programari extends Zend_Db_Table_Abstract
{
	protected $_name    = 'programari';
	protected $_primary = 'id';
}

class Default_Model_Programari
{
	protected $_id;
	protected $_idPacient;
	protected $_idDoctor;
	protected $_role;
	protected $_data;
	protected $_ora;

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

	public function setIdPacient($idPacient)
    {
      $this->_idPacient = $idPacient;
      return $this;
    }

    public function getIdPacient()
    {
      return $this->_idPacient;
    }

    public function setIdDoctor($idDoctor)
    {
        $this->_idDoctor = $idDoctor;
        return $this;
    }

    public function getIdDoctor()
    {
        return $this->_idDoctor;
    }

    public function setData($date)
    {
        $this->_data = (!empty($date) && strtotime($date)>0)?strtotime($date):null;
        return $this;
    }

    public function getData()
    {
        return $this->_data;
    }

	public function setOra($value)
	{
		$this->_ora = $value;
		return $this;
	}

	public function getOra()
	{
		return $this->_ora;
	}

	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper()
	{
		if(null === $this->_mapper) {
			$this->setMapper(new Default_Model_ProgramariMapper());
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

class Default_Model_ProgramariMapper
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
			$this->setDbTable('Default_Model_DbTable_Programari');
		}
		return $this->_dbTable;
	}

	public function find($id, Default_Model_Programari $model)
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
			$model = new Default_Model_Programari();
			$model->setOptions($row->toArray())
					->setMapper($this);
			$entries[] = $model;
		}
		return $entries;
	}

	public function fetchRow($select, Default_Model_Programari $model)
	{
		$result=$this->getDbTable()->fetchRow($select);
		if(0 == count($result))
		{
			return;
		}
		$model->setOptions($result->toArray());
		return $model;
	}

	public function save(Default_Model_Programari $value)
        {
            $auth = Zend_Auth::getInstance();
            $authAccount = $auth->getStorage()->read();
            if (null != $authAccount) {
                    if (null != $authAccount->getId()) {
                            $user = new Default_Model_Programari();
                            $user->find($authAccount->getId());

                        $data = array(
                            'idPacient'		=> $value->getIdPacient(),
                            'idDoctor'		=> $value->getIdDoctor(),
                            'data'			=> date('Y-m-d', $value->getData()),
                            'ora'			=> $value->getOra(),
                        );

                    $id = $this->getDbTable()->insert($data);

                    return $id;
                    }
            }

        }
}
