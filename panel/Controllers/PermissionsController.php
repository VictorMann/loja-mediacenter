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

        echo '<pre>';
        print_r($array['list']);exit;
        
        $this->loadTemplate('permissions', $array);
	}
}