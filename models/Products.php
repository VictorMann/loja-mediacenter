<?php

class Products extends model
{
    public function getList($offset = 0, $limit = 3, $filters = [])
    {
        $dados = [];
        
        $where = ['1=1'];
        if (!empty($filters['category'])) $where[] = 'p.id_category = :id_category';
        

        $sql = 'SELECT p.*,
        (SELECT b.name FROM brands b WHERE b.id = p.id_brand) as brand_name,
        (SELECT c.name FROM categories c WHERE c.id = p.id_category) as category_name 
        FROM products p
        WHERE '. implode(' AND ', $where);

        $sql .= " LIMIT {$limit} OFFSET {$offset}";

        $sql = $this->db->prepare($sql);
        if (!empty($filters['category'])) $sql->bindValue(':id_category', $filters['category']);
        $sql->execute();

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

    public function getTotal($filters = [])
    {
        $where = ['1=1'];
        if (!empty($filters['category'])) $where[] = 'p.id_category = :id_category';
    
        $sql = 'SELECT COUNT(*) FROM products p WHERE '. implode(' AND ', $where);

        $sql = $this->db->prepare($sql);
        if (!empty($filters['category'])) $sql->bindValue(':id_category', $filters['category']);
        $sql->execute();

        $count = $sql->fetch();
        return $count[0];
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