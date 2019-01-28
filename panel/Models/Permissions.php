<?php
namespace Models;

use \Core\Model;

class Permissions extends Model
{
    public function get($id_permission)
    {
        $sql = 'SELECT i.name, i.slug
        FROM permissions_links l
        INNER JOIN permissions_items i ON i.id = l.id_permission_item
        WHERE l.id_user_permission = ?';

        $sql = $this->db->prepare($sql);
        $sql->execute([$id_permission]);

        if ($sql->rowCount())
        {
            $dados = [];
            while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) $dados[] = $row;
            return $dados;
        }
    }
    
    public function getAllGroups()
    {
        $sql = 'SELECT up.*, (SELECT COUNT(u.id) FROM users u WHERE u.id_permission = up.id) total_users
        FROM users_permissions_group up';
        $sql = $this->db->query($sql);
        return $sql->rowCount() ? $sql->fetchAll(\PDO::FETCH_ASSOC) : null;
    }
}