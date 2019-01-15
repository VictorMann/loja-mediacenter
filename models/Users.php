<?php

class Users extends model
{
    public $id;
    public $name;
    public $email;
    public $password;

    public function create($name, $email, $password)
    {
        $sql = 'INSERT INTO users 
        (name, email, password) 
        VALUES 
        (:name, :email, :password)';

        $sql = $this->db->prepare($sql);
        $sql->execute([$name, $email, md5($password)]);
        
        $this->id = $this->db->lastInsertId();
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function emailExists($email)
    {
        $sql = 'SELECT 1 FROM users 
        WHERE email = :email LIMIT 1';

        $sql = $this->db->prepare($sql);
        $sql->execute([$email]);
        return $sql->rowCount() ? !0 : !1;
    }

    public function validate($email, $password)
    {
        $sql = 'SELECT * FROM users 
        WHERE email = :email AND password = :password 
        LIMIT 1';

        $sql = $this->db->prepare($sql);
        $sql->execute([$email, md5($password)]);
        return $sql->rowCount() ? $sql->fetch()['id'] : !1;
    }
}