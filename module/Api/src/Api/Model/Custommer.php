<?php

namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;

class Custommer
{
    protected $table = "jp_customer";
    protected $tableGateway;

    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }

    public function getList($array)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $script = new Sqlinjection();
        if (isset($array['search'])) {
            $select->where(array("fullname LIKE ?" => "%" . $script->Change($array['search'] . "%")));
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    } //end func

    public function getItem(array $array)
    {
        $data = array();
        $rows = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $script = new Sqlinjection();
        $flag = 0;
        if (!empty($array["id"])) {
            $select->where(array("id" => (int)$array["id"]));
            $flag = 1;
        }
        if (!empty($array["id_facebook"])) {
            $select->where(array("id_facebook" => $array["id_facebook"]));
            $flag = 1;
        }
        if (!empty($array["username"])) {
            $select->where(array("mobile" => $script->Change($array["username"])));
            $select->where(array("email" => $script->Change($array['username'])), "OR");
            $flag = 1;
        }
        if (!empty($array["mobile"])) {
            $select->where(array("mobile" => $script->Change($array["mobile"])));
            $flag = 1;
        }
        if (!empty($array["email"])) {
            $select->where(array("email" => $script->Change($array["email"])), "OR");
            $flag = 1;
        }

        if ($flag == 1) {
            $rowset = $table->selectWith($select)->toArray();
        }
        if (!empty($rowset))
            return $rowset[0];
        return array();
    }

    public function setLogin($arr)
    {
        $rowset = array();
        $script = new Sqlinjection();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("password" => $arr['password']));
        $select->where(array("mobile" => $script->Change($arr['username'])));
        $select->where(array("email" => $script->Change($arr['username'])), "OR");
        $rowset = $table->selectWith($select)->toArray();
        if (!empty($rowset)) {
            return $rowset[0];
        } else {
            return array();
        }
    }

    public function checkusername($arr)
    {
        $script = new Sqlinjection();
        if (isset($arr['username'])) {
            $rowset = array();
            $table = $this->tableGateway;
            $select = new Select($this->table);
            $select->where(array("username" => $script->Change($arr['username'])));
            $rowset = $table->selectWith($select)->toArray();

            if (!empty($rowset[0])) {
                return $rowset[0];
            } else {
                return false;
            }
        }//end if
    }//end func

    public function addItem($data, $id = "")
    {
        $value = array();
        $script = new Sqlinjection();
        if (isset($data['name'])) {
            $value['name'] = $script->Change($data['name']);
        }
        if (isset($data['password'])) {
            $value['password'] = md5(KEY_PASSWORD_FRONTEND . $data['password']);
        }
        if (isset($data['email'])) {
            $value['email'] = $script->Change($data['email']);
        }
        if (isset($data['id_facebook'])) {
            $value['id_facebook'] = $data['id_facebook'];
        }
        if (isset($data['id_google'])) {
            $value['id_google'] = $data['id_google'];
        }
        if (isset($data['id_guest'])) {
            $value['id_guest'] = $data['id_guest'];
        }
        $value['datecreate'] = date('Y-m-d h:i:s');
        $value['showview'] = 1;
        $table = $this->tableGateway;
        if (empty($id)) {
            if (isset($data['mobile'])) {
                $value['mobile'] = $script->Change($data['mobile']);
            }
            $table->insert($value);
        } else {
            $table->update($value, array("id" => $id));
        }
    }

    public function updateItem(array $data, $id)
    {
        try {
            $value = array();
            $script = new Sqlinjection();
            if (isset($data['sex'])) {
                $value['sex'] = $script->Change($data['sex']);
            }
            if (isset($data['birthday'])) {
                $value['birthday'] = date("Y-m-d", strtotime($data['birthday']));
            }
            if (isset($data['name'])) {
                $value['name'] = $script->Change($data['name']);
            }
            if (isset($data['code'])) {
                $value['code'] = $script->Change($data['code']);
            }
            if (isset($data['password']) && !empty($data['password'])) {
                $value['password'] = md5(KEY_PASSWORD_FRONTEND . $data['password']);
            }
           /*KHONG UPDATE SDT
            if (isset($data['mobile'])) {
                $value['mobile'] = $script->Change($data['mobile']);
            }*/
            if (isset($data['email'])) {
                $value['email'] = $script->Change($data['email']);
            }
            if (isset($data['id_city'])) {
                $value['id_city'] = (int)$data['id_city'];
            }
            if (isset($data['id_cityzone'])) {
                $value['id_cityzone'] = (int)$data['id_cityzone'];
            }
            if (isset($data['id_cityward'])) {
                $value['id_cityward'] = (int)$data['id_cityward'];
            }
            if (isset($data['address'])) {
                $value['address'] = $script->Change($data['address']);
            }
            if (isset($data['image'])) {
                $value['image'] = $script->Change($data['image']);
            }
            if (isset($data['id_vip'])) {
                $value['id_vip'] = $data['id_vip'];
            }
            if (isset($data['notes'])) {
                $value['notes'] = $script->Change($data['notes']);
            }
            if (isset($data['showview'])) {
                $value['showview'] = $data['showview'];
            }
            if (isset($data['username'])) {
                $value['username'] = $data['username'];
            }
            if (isset($data['id_guest'])) {
                $value['id_guest'] = $data['id_guest'];
            }
            $table = $this->tableGateway;
            if (!empty($value)) {
                return $table->update($value, array("id" => (int)$id));
            }
        } catch (\Exception $e) {
            throw  $e;
        }
    }//end func

    public function deleteItem($id)
    {
        try {
            $table = $this->tableGateway;
            $table->delete(array("id" => (int)$id));

        } catch (\Exception $e) {
            return $e;
        }
    } //end func

    public function changeStatus($id, $status)
    {

    }

    /*
     * hàm thay đổi mật khẩu của Account đăng nhập trong admin
     * param1 : user name
     * param2 : mật khẩu hiện tại
     * param3 : mật khẩu mới
     */
    public function changepwd($username, $curpwd, $npwd)
    {
        $table = $this->tableGateway;
        $script = new Sqlinjection();
        $select = new Select($this->table);
        $select->where(array("username" => $script->Change($username)));
        $rowset = $table->selectWith($select)->toArray();
        $pwds = $rowset[0]['password'];
        if (md5(KEY_ADMIN . $curpwd) != $pwds) {
            return 1;
        }
        $table->update(array('password' => md5(KEY_ADMIN . $npwd)), array(
            "username" => $script->Change($username)
        ));
        return 0;
    }

    public function changePass($arr)
    {
        $script = new Sqlinjection();
        $table = $this->tableGateway;
        $table->update(
            array('password' => md5(KEY_ADMIN . $arr["password"])),
            array("username" => $script->Change($arr["username"]))
        );
        return 0;
    }

    /*
     * Hàm lấy thông tin User đăng nhập Admin
     */
    public function getInfoUser($username)
    {
        $data = array();
        $rows = array();
        $script = new Sqlinjection();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("username" => $script->Change($username)));
        $rowset = $table->selectWith($select)->toArray();
        return $rowset[0];
    }
}