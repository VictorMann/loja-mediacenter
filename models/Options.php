<?php

class Options extends model
{
    const TABLENAME = 'options';

    public function getName($id)
    {
        $sql = 'SELECT name FROM '. self::TABLENAME;
        $sql .= ' WHERE id = :id';

        $sql = $this->db->prepare($sql);
        $sql->bindValue(':id', $id);
        $sql->execute();
        return $sql->rowCount() ? $sql->fetch()[0] : null;
    }
}