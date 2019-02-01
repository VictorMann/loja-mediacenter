<?php
namespace Models;

use \Core\Model;
use PDO;

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

    public function getAllItems()
    {
        $sql = 'SELECT * FROM permissions_items';
        $sql = $this->db->query($sql);
        return $sql->rowCount() ? $sql->fetchAll(PDO::FETCH_ASSOC) : null;
    }

    public function addGroup($name)
    {
        $sql = 'INSERT INTO users_permissions_group SET name = ?';
        $sql = $this->db->prepare($sql);
        $sql->execute([$name]);
        return $this->db->lastInsertId();
    }

    public function linkItemToGroup($itens = [], $id_group)
    {
        $this->id_group = (int) $id_group;

        $itens = array_map(
            function($item) {
                $item = (int) $item;
                return "({$this->id_group}, {$item})";
            }
            , $itens
        );

        $sql = 'INSERT INTO permissions_links 
        (id_user_permission, id_permission_item) 
        VALUES '. implode(',', $itens);

        $this->db->exec($sql);
    }

    public function delete($id)
    {
        $sql = 'SELECT 1 FROM users WHERE id_permission = ? LIMIT 1';
        $sql = $this->db->prepare($sql);
        $sql->execute([$id]);

        // Caso NÃO haja usuários com essa permissão pode remove-la
        if ( ! $sql->rowCount() )
        {
            try
            {
                // inicia transação
                $this->db->beginTransaction();
    
                // remove de permissions_links
                $sql = 'DELETE FROM permissions_links WHERE id_user_permission = ?';
                $sql = $this->db->prepare($sql);
                $sql->execute([$id]);
                
                // remove de users_permissions_group
                $sql = 'DELETE FROM users_permissions_group WHERE id = ?';
                $sql = $this->db->prepare($sql);
                $sql->execute([$id]);
                
                // concretiza operação
                $this->db->commit();
                
                $_SESSION['mensagem']['class'] = 'alert-success';
                $_SESSION['mensagem']['text']  = 'Remoção realizada';
                
                return true;
            }
            catch (Exception $e)
            {
                // desfaz operação
                $this->db->rollback();

                $_SESSION['mensagem']['class'] = 'alert-danger';
                $_SESSION['mensagem']['text']  = 'Não foi possível realizar a operação. Tente em instantes...';

                return false;
            }
        }
    }
}