<?php
namespace Models;

use \Core\Model;

class Users extends Model
{
    private $uid;

    public function isLogged()
    {
        if (!empty($_SESSION['token']))
        {
            $token = $_SESSION['token'];

            $sql = 'SELECT id FROM users WHERE token = ?';
            $sql = $this->db->prepare($sql);
            $sql->execute([$token]);

            if ($sql->rowCount() > 0)
            {
                return $this->uid = $sql->fetch()['id'];
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