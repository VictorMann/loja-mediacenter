<?php

class Products extends model
{
    const TABLENAME = 'products';

    public function get($id)
    {
        $sql  = 'SELECT *, (SELECT name FROM brands WHERE brands.id = products.id_brand) as brand FROM '. self::TABLENAME;
        $sql .= ' WHERE id = ?';

        $sql = $this->db->prepare($sql);
        $sql->execute([$id]);
        
        return $sql->rowCount() ? $sql->fetch(PDO::FETCH_ASSOC) : null;
    }

    public function getList($offset = 0, $limit = 3, $filters = [], $random = false)
    {
        $dados = [];

        $orderBySQL = $random ? ' ORDER BY RAND()' : '';

        if (!empty($filters['toprated'])) $orderBySQL = ' ORDER BY rating DESC';

        $where = $this->buildWhere($filters);
        
        $sql = 'SELECT products.*,
        (SELECT brands.name FROM brands WHERE brands.id = products.id_brand) as brand_name,
        (SELECT categories.name FROM categories WHERE categories.id = products.id_category) as category_name 
        FROM products
        WHERE '. implode(' AND ', $where);

        $sql .= $orderBySQL;
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

    public function getOptions($id)
    {
        $sql = 'SELECT 1 FROM products 
        WHERE id = ? 
        AND options IS NOT NULL 
        AND LENGTH(options) > 0';

        $sql = $this->db->prepare($sql);
        $sql->execute([$id]);

        // se não houver opções
        if (!$sql->rowCount()) return;

        // obtem nome e valor das opções
        $sql = 'SELECT o.name, op.p_value as `value`
        FROM products_options op
        INNER JOIN options o ON o.id = op.id_option
        WHERE id_product = ?';

        $sql = $this->db->prepare($sql);
        $sql->execute([$id]);

        return $sql->rowCount() ? $sql->fetchAll() : !1;
    }

    public function getRates($id, $qt)
    {
        $rates = new Rates;
        return $rates->getRates($id, $qt);
    }

    public function getMaxPrice($filters = [])
    {
        $where = $this->buildWhere($filters);

        $sql = 'SELECT MAX(price) FROM '. self::TABLENAME;
        // $sql .= ' WHERE '. implode(' AND ', $where);

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

        $sql  = 'SELECT brands.id, brands.name, COUNT(*) qtd FROM '. self::TABLENAME;
        $sql .= ' INNER JOIN brands ON brands.id = products.id_brand ';
        $sql .= 'WHERE ' . implode($where, ' AND ');
        $sql .= ' GROUP BY brands.id';

        $sql = $this->db->prepare($sql);
        $this->bindWhere($filters, $sql);
        $sql->execute();

        return $sql->rowCount() ? $sql->fetchAll() : [];
    }

    private function buildWhere($filters)
    {
        $where = ['1=1'];
        if (!empty($filters['category'])) $where[] = 'id_category = :id_category';
        if (!empty($filters['brand'])) $where[] = 'id_brand IN ("'. implode(",", $filters['brand']) .'")';
        if (!empty($filters['star'])) $where[] = 'rating IN ("'. implode(",", $filters['star']) .'")';
        if (!empty($filters['sale'])) $where[] = 'sale = 1';
        if (!empty($filters['featured'])) $where[] = 'featured = 1';

        if (!empty($filters['options'])) $where[] = 'products.id IN (SELECT id_product FROM products_options WHERE p_value IN ("'. implode(",", $filters['options']) .'"))';
        if (!empty($filters['slider']))
        {
            $price = array_map(function($v){return  (float) $v;}, $filters['slider']);
            $where[] = "price BETWEEN {$price['min']} AND {$price['max']}";
        }
        if (!empty($filters['searchTerm'])) $where[] = 'products.name LIKE :searchTerm';
        
        
        return $where;
    }

    private function bindWhere($filters, &$sql)
    {
        if (!empty($filters['category'])) $sql->bindValue(':id_category', $filters['category']);
        if (!empty($filters['searchTerm'])) $sql->bindValue(':searchTerm', '%'.$filters['searchTerm'].'%');
    }
}