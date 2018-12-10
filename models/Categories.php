<?php

class Categories extends model
{
    private $categorias;

    public function getList()
    {
        $dados = [];
        
        $sql = 'SELECT * FROM categories ORDER BY sub DESC';
        $sql = $this->db->query($sql);

        foreach ($sql->fetchAll() as $c)
        {
            $c['subs'] = [];
            $dados[$c['id']] = $c;
        }

        $this->categorias = $dados;
        
        $this->organiza($dados);
        
        return $this->categorias;
    }

    /**
     *  Organiza as categorias por hierarquia de niveis sub
     *  @param $dados : lista de categorias aux
     *  @return void
     */
    private function organiza($dados)
    {
        foreach ($dados as $id => $c)
        {
            if ($c['sub'])
            {
                $this->categorias[$c['sub']]['subs'][$id] = $this->categorias[$id];
                unset($this->categorias[$id]);
            }
        }
    }

    public function getCategoryTree($id)
    {
        $dados = [];

        do
        {
            $sql = 'SELECT * FROM categories WHERE id = :id';
            $sql = $this->db->prepare($sql);
            $sql->bindValue(':id', $id);
            $sql->execute();

            if ($sql->rowCount())
            {
                $dados[] = $sql->fetch(PDO::FETCH_ASSOC);
                $id = end($dados)['sub'];
            }
            else break;
        } while (true);

        return array_reverse($dados);
    }

    public function getName($id)
    {
        $sql = 'SELECT name FROM categories WHERE id = :id';
        $sql = $this->db->prepare($sql);
        $sql->execute([':id' => $id]);
        return $sql->rowCount() ? $sql->fetch()[0] : null;
    }
}