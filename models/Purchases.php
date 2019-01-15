<?php

class Purchases extends model
{
    public function create($uid, $total, $payment_type)
    {
        $sql = 'INSERT INTO purchases 
        (id_user, total_amount, payment_type) 
        VALUES 
        (:uid, :total, :payment_type)';

        $sql = $this->db->prepare($sql);
        $sql->execute([$uid, $total, $payment_type]);
        return $this->db->lastInsertId();
    }

    public function addItem($id_purchase, $id_product, $qt, $price)
    {
        $sql = 'INSERT INTO purchases_products 
        (id_purchase, id_product, quantity, product_price) 
        VALUES 
        (:id_purchase, :id_product, :qt, :price)';

        $sql = $this->db->prepare($sql);
        $sql->execute([$id_purchase, $id_product, $qt, $price]);
        return $this->db->lastInsertId();
    }
}