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

    public function getListTotalItems()
    {
        $sql  = 'SELECT b.id, b.name, COUNT(*) qtd FROM '. self::TABLENAME .' b ';
        $sql .= 'INNER JOIN products p ON b.id = p.id_brand ';
        $sql .= 'GROUP BY b.id';

        $sql = $this->db->query($sql);
        return $sql->rowCount() ? $sql->fetchAll() : [];
    }
}