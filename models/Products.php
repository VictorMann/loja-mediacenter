<?php

class Products extends model
{
    const TABLENAME = 'products';

    public function getList($offset = 0, $limit = 3, $filters = [])
    {
        $dados = [];

        $where = $this->buildWhere($filters);
        
        $sql = 'SELECT p.*,
        (SELECT b.name FROM brands b WHERE b.id = p.id_brand) as brand_name,
        (SELECT c.name FROM categories c WHERE c.id = p.id_category) as category_name 
        FROM products p
        WHERE '. implode(' AND ', $where);

        $sql .= " LIMIT {$limit} OFFSET {$offset}";

        $sql = $this->db->prepare($sql);
        $this->bindWhere($filters, $sql);
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
        $where = $this->buildWhere($filters);

        $sql = 'SELECT COUNT(*) FROM products WHERE '. implode(' AND ', $where);

        $sql = $this->db->prepare($sql);
        $this->bindWhere($filters, $sql);
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

    public function getMaxPrice($filters = [])
    {
        $where = $this->buildWhere($filters);

        $sql = 'SELECT MAX(price) FROM '. self::TABLENAME;
        $sql .= ' WHERE '. implode(' AND ', $where);

        $sql = $this->db->prepare($sql);
        $this->bindWhere($filters, $sql);
        $sql->execute();

        return $sql->rowCount() ? $sql->fetch()[0] : 9999;
    }

    public function getSaleCount($filters = [])
    {
        $where = $this->buildWhere($filters);

        $sql = 'SELECT SUM(sale) FROM '. self::TABLENAME;
        $sql .= ' WHERE '. implode(' AND ', $where);

        $sql = $this->db->prepare($sql);
        $this->bindWhere($filters, $sql);
        $sql->execute();

        return $sql->rowCount() ? $sql->fetch()[0] : 0;
    }

    public function getAvailableOptions($filters = [])
    {
        $groups = [];
        $ids = [];

        $where = $this->buildWhere($filters);

        $sql  = 'SELECT id, options FROM '. self::TABLENAME;
        $sql .= ' WHERE '. implode(' AND ', $where);

        $sql = $this->db->prepare($sql);
        $this->bindWhere($filters, $sql);
        $sql->execute();

        if ($sql->rowCount() > 0)
        {
            foreach ($sql->fetchAll() as $p)
            {
                $ops = explode(',', $p['options']);
                $ids[] = $p['id'];
                foreach ($ops as $op)
                {
                    if (!in_array($op, $groups)) $groups[] = $op;
                }
            }
        }

        $options = $this->getAvailableValuesFromOptions($groups, $ids);

        return $options;
    }

    public function getAvailableValuesFromOptions($g, $ids)
    {
        $dados = [];
        $options = new Options;

        foreach ($g as $op)
        {
            $dados[$op] = [
                'name' => $options->getName($op),
                'options' => []
            ];
        }

        $sql  = 'SELECT p_value, id_option, COUNT(*) AS c
        FROM products_options
        WHERE id_option IN ("'.implode('","', $g).'") 
        AND id_product IN ("'.implode('","', $ids).'") 
        GROUP BY p_value
        ORDER BY id_option';

        $sql = $this->db->query($sql);
        if ($sql->rowCount() > 0)
        {
            foreach ($sql->fetchAll() as $ops)
            {
                $dados[$ops['id_option']]['options'][] = [
                    'id' => $ops['id_option'],
                    'value' => $ops['p_value'],
                    'count' => $ops['c']
                ];
            }
        }


        return $dados;
    }

    public function getListOfStars($filters)
    {
        $where = $this->buildWhere($filters);

        $sql  = 'SELECT rating, COUNT(*) qtd FROM '. self::TABLENAME;
        $sql .= ' WHERE ' . implode($where, ' AND ');
        $sql .= ' GROUP BY rating';

        $sql = $this->db->prepare($sql);
        $this->bindWhere($filters, $sql);
        $sql->execute();

        return $sql->rowCount() ? $sql->fetchAll() : [];
    }

    public function getListTotalItems($filters = [])
    {
        $where = $this->buildWhere($filters);

        $sql  = 'SELECT b.id, b.name, COUNT(*) qtd FROM '. self::TABLENAME;
        $sql .= ' INNER JOIN brands b ON b.id = id_brand ';
        $sql .= 'WHERE ' . implode($where, ' AND ');
        $sql .= ' GROUP BY b.id';

        $sql = $this->db->prepare($sql);
        $this->bindWhere($filters, $sql);
        $sql->execute();

        return $sql->rowCount() ? $sql->fetchAll() : [];
    }

    private function buildWhere($filters)
    {
        $where = ['1=1'];
        if (!empty($filters['category'])) $where[] = 'id_category = :id_category';
        if (!empty($filters['id_brand'])) $where[] = 'id_brand = :id_brand';

        return $where;
    }

    private function bindWhere($filters, &$sql)
    {
        if (!empty($filters['category'])) $sql->bindValue(':id_category', $filters['category']);
        if (!empty($filters['id_brand'])) $sql->bindValue(':id_brand', $filters['id_brand']);
    }
}