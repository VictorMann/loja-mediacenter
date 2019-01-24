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
}