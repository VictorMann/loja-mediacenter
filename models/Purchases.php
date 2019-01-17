<?php

class Purchases extends model
{
    public function create($uid, $total, $payment_type)
    {
        $sql = 'INSERT INTO purchases 
        (id_user, total_amount, payment_type) 
        VALUES (?, ?, ?)';

        $sql = $this->db->prepare($sql);
        $sql->execute([$uid, $total, $payment_type]);
        return $this->db->lastInsertId();
    }

    public function addItem($id_purchase, $id_product, $qt, $price)
    {
        $sql = 'INSERT INTO purchases_products 
        (id_purchase, id_product, quantity, product_price) 
        VALUES (?, ?, ?, ?)';

        $sql = $this->db->prepare($sql);
        $sql->execute([$id_purchase, $id_product, $qt, $price]);
        return $this->db->lastInsertId();
    }

    public function setPaid($id)
    {
        $sql = 'UPDATE purchases SET payment_status = :status WHERE id = :id';
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':status', 2); // status do nosso sistema para PAGO
        $sql->bindValue(':id', $id);
        $sql->execute();
    }

    public function setCancelled($id)
    {
        $sql = 'UPDATE purchases SET payment_status = :status WHERE id = :id';
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':status', 3); // status do nosso sistema para CANCELADA
        $sql->bindValue(':id', $id);
        $sql->execute();
    }

    
}