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
}