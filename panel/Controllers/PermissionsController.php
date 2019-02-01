<?php
namespace Controllers;

use Core\Controller;
use Models\Users;
use Models\Permissions;

class PermissionsController extends Controller
{
	private $user;

	public function __construct()
	{

		$this->user = new Users;

        // aplica a toda a Controller
		if (!$this->user->isLogged())
		{
			header('Location: '. BASE_URL .'login');
			exit;
        }
        // aplica a toda a Controller
        if (!$this->user->hasPermission('permissions_view'))
        {
            header('Location: '. BASE_URL);
            exit;
        }
	}

	public function index()
	{
        $p = new Permissions;
        $array = array();

        $array['user'] = $this->user;
        $array['list'] = $p->getAllGroups();

        // echo '<pre>';
        // print_r($array['list']);exit;
        
        $this->loadTemplate('permissions', $array);
    }
    
    public function del($id)
    {
        $p = new Permissions;
        $p->delete($id);

        header('Location:'. BASE_URL .'permissions');
        exit;
    }

    public function add()
    {
        $array = [
            'user' => $this->user,
            'errors' => []
        ];

        if (!empty($_SESSION['errors']))
        {
            $array['errors'] = $_SESSION['errors'];
            unset($_SESSION['errors']);
        }

        $p = new Permissions;

        $array['permission_items'] = $p->getAllItems();
        
        $this->loadTemplate('permissions_add', $array);
    }

    public function add_action()
    {
        if (!empty($_POST['name']))
        {
            $p = new Permissions;
            $id = $p->addGroup($_POST['name']);

            if (!empty($_POST['items'])) $p->linkItemToGroup($_POST['items'], $id);

            header('Location:'. BASE_URL .'permissions');
            exit;
        }

        // errors de validação
        $_SESSION['errors'] = [
            'name' => 'Campo obrigatório'
        ];
        
        header('Location:'. BASE_URL .'permissions/add');
        exit;
    }
}