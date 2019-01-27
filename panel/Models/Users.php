<?php
namespace Models;

use \Core\Model;

class Users extends Model
{
    private $uid;
    private $permissions;

    public function isLogged()
    {
        if (!empty($_SESSION['token']))
        {
            $token = $_SESSION['token'];

            $sql = 'SELECT id, id_permission FROM users WHERE token = ?';
            $sql = $this->db->prepare($sql);
            $sql->execute([$token]);

            if ($sql->rowCount() > 0)
            {
                $p = new Permissions;

                $d = $sql->fetch(\PDO::FETCH_ASSOC);

                $this->uid = $d['id'];
                $this->permissions = $p->get($d['id_permission']);

                

                return $this->uid;
            }
        }
    }

    public function getId()
    {
        return $this->uid;
    }

    public function validateLogin($email, $password)
    {
        $sql = 'SELECT id FROM users WHERE email = ? AND password = ? AND admin = 1 LIMIT 1';
        $sql = $this->db->prepare($sql);
        $sql->execute([$email, md5($password)]);

        if ($sql->rowCount() > 0)
        {
            $id = $sql->fetch()['id'];
            $token = md5(uniqid(rand()));

            $sql = 'UPDATE users SET token = ? WHERE id = ?';
            $sql = $this->db->prepare($sql);
            $sql->execute([$token, $id]);

            $_SESSION['token'] = $token;
            return true;
        }
    }
}