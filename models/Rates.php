<?php

class Rates extends model
{
    public function getRates($id, $qt)
    {
        $sql = 'SELECT 
        *,
        (SELECT users.name FROM users WHERE users.id = rates.id_user) as `user_name`
        FROM rates WHERE id_product = ? ORDER BY date_rated DESC LIMIT '. (int) $qt;
       
        $sql = $this->db->prepare($sql);
        $sql->execute([$id]);
        return $sql->rowCount() ? $sql->fetchAll() : null;
    }
}