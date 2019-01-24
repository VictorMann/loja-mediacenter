<?php
namespace Controllers;

use Core\Controller;
use Models\Users;

class LoginController extends Controller
{
    public function index()
    {
		$array = array();

		$this->loadView('login', $array);
	}

	public function index_action()
	{
		if (!empty($_POST['email']) and !empty($_POST['password']))
		{
			$email = $_POST['email'];
			$pass  = $_POST['password'];

			$u = new Users;
			if ($u->validateLogin($email, $pass))
			{
				header('Location:'. BASE_URL);
				exit;
			}
			else ;
		}

		header('Location:' . BASE_URL .'login');
		exit;
	}
}