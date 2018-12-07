<?php

class Products extends model
{
    public function getList()
    {
        $dados = [];

        $sql = 'SELECT p.*,
        (SELECT b.name FROM brands b WHERE b.id = p.id_brand) as brand_name,
        (SELECT c.name FROM categories c WHERE c.id = p.id_category) as category_name
        FROM products p';
        $sql = $this->db->query($sql);

        if ($sql->rowCount() > 0)
        {
            $dados = $sql->fetchAll();

            foreach ($dados as $key => $item)
            {
                $dados[$key]['images'] = $this->getImageByProductId($item['id']);
            }
        }

        return $dados;
    }

    public function getImageByProductId($id)
    {
        $imgs = [];

        $sql = 'SELECT * FROM products_images WHERE id_product = :id';
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':id', $id);
        $sql->execute();

        if ($sql->rowCount() > 0)
        {
            $imgs = $sql->fetchAll();
        }

        return $imgs;
    }
}