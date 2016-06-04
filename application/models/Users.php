<?php
class Default_Model_DbTable_Users extends Zend_Db_Table_Abstract
{
	protected $_name    = 'users';
	protected $_primary = 'id';
}

class Default_Model_Users
{
	protected $_id;
	protected $_idRole;
	protected $_idDoctor;
	protected $_role;
	protected $_email;
	protected $_name;
	protected $_password;
	protected $_birth_date;
	protected $_age;
	protected $_ocupation;
	protected $_address;
	protected $_cnp;
	protected $_ci;
	protected $_phone_number;
	protected $_status;
	protected $_created;
	protected $_modified;
	protected $_deleted;

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

	public function setIdRole($roleId)
  {
      $model = new Default_Model_Role();
      if($model->find($roleId))
      {
              $this->setRole($model);
      }
      $this->_idRole = $roleId;
      return $this;
  }

  public function getIdRole()
  {
      return $this->_idRole;
  }

	public function setIdDoctor($doctorId)
  {
      $this->_idDoctor = $doctorId;
      return $this;
  }

  public function getIdDoctor()
  {
      return $this->_idDoctor;
  }

  public function setRole(Default_Model_Role $var)
  {
      $this->_role = $var;
      return $this;
  }

  public function getRole()
  {
      return $this->_role;
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

	public function setName($value)
	{
		$this->_name = (string) strip_tags($value);
		return $this;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function setPassword($string)
	{
		$this->_password = (string) strip_tags($string);
		return $this;
	}

	public function getPassword()
	{
		return $this->_password;
	}

	public function setBirth_date($date)
	{
		$this->_birth_date = (!empty($date) && strtotime($date)>0)?strtotime($date):null;
		return $this;
	}

	public function getBirth_date()
	{
		return $this->_birth_date;
	}

	public function setAge($int)
	{
		$this->_age = (string) strip_tags($int);
		return $this;
	}

	public function getAge()
	{
		return $this->_age;
	}

	public function setOcupation($string)
	{
		$this->_ocupation = (string) strip_tags($string);
		return $this;
	}

	public function getOcupation()
	{
		return $this->_ocupation;
	}

	public function setAddress($string)
	{
		$this->_address = (string) strip_tags($string);
		return $this;
	}

	public function getAddress()
	{
		return $this->_address;
	}

	public function setCnp($string)
	{
		$this->_cnp = (string) strip_tags($string);
		return $this;
	}

	public function getCnp()
	{
		return $this->_cnp;
	}

	public function setCi($string)
	{
		$this->_ci = (string) strip_tags($string);
		return $this;
	}

	public function getCi()
	{
		return $this->_ci;
	}

	public function setPhone_number($string)
	{
		$this->_phone_number = (string) strip_tags($string);
		return $this;
	}

	public function getPhone_number()
	{
		return $this->_phone_number;
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

        public function setLastLogin($date)
        {
            $this->_lastLogin = (!empty($date) && strtotime($date)>0)?strtotime($date):null;
            return $this;
        }

        public function getLastLogin()
        {
            return $this->_lastLogin;
        }

            public function saveLastlogin()
        {
            $this->getMapper()->saveLastlogin($this);
        }

	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper()
	{
		if(null === $this->_mapper) {
			$this->setMapper(new Default_Model_UsersMapper());
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

class Default_Model_UsersMapper
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
			$this->setDbTable('Default_Model_DbTable_Users');
		}
		return $this->_dbTable;
	}

	public function find($id, Default_Model_Users $model)
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
			$model = new Default_Model_Users();
			$model->setOptions($row->toArray())
					->setMapper($this);
			$entries[] = $model;
		}
		return $entries;
	}

	public function fetchRow($select, Default_Model_Users $model)
	{
		$result=$this->getDbTable()->fetchRow($select);
		if(0 == count($result))
		{
			return;
		}
		$model->setOptions($result->toArray());
		return $model;
	}

	public function save(Default_Model_Users $value)
        {
            $auth = Zend_Auth::getInstance();
            $authAccount = $auth->getStorage()->read();
            if (null != $authAccount) {
                    if (null != $authAccount->getId()) {
                            $user = new Default_Model_Users();
                            $user->find($authAccount->getId());

                        $data = array(
                            'idRole'				=> $value->getIdRole(),
                            'idDoctor'			=> $value->getIdDoctor(),
                            'email'					=> $value->getEmail(),
                            'name'					=> $value->getName(),
                            'password'			=> $value->getPassword(),
                            'birth_date'		=> date('Y-m-d',$value->getBirth_date()),
                            'age'						=> $value->getAge(),
                            'ocupation'			=> $value->getOcupation(),
                            'address'				=> $value->getAddress(),
                            'cnp'						=> $value->getCnp(),
                            'ci'						=> $value->getCi(),
                            'phone_number'	=> $value->getPhone_number(),
                            'deleted'				=> '0',
                        );
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

	public function saveLastlogin(Default_Model_Users $adminUser)
        {
            $data = array(
                'lastLogin'	 => new Zend_Db_Expr('NOW()'),
            );

            if (null === ($id = $adminUser->getId())) {
                    throw new Exception("Invalid admin account selected!");
            } else {
                $this->getDbTable()->update($data, array('id = ?' => $id));
            }
            return $id;
        }

        public function delete(Default_Model_Users $value)
        {
           $auth = Zend_Auth::getInstance();
            $authAccount = $auth->getStorage()->read();
            if (null != $authAccount) {
                    if (null != $authAccount->getId()) {
                        $user = new Default_Model_Users();
                        $user->find($authAccount->getId());

                        $id = $value->getId();
                        $data = array(
                            'deleted' => '1',
                            'email' => $value->getEmail(),
                        );
                        $this->getDbTable()->update($data, array('id = ?' => $id));
                        return $id;
                    }
            }
        }
}
