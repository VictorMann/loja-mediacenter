<?php

class Brands extends model
{
    const TABLENAME = 'brands';

    public function getList()
    {
        $sql = 'SELECT * FROM '. self::TABLENAME;
        $sql = $this->db->query($sql);
        return $sql->rowCount() ? $sql->fetchAll() : [];
    }

}