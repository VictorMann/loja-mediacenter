<?php
namespace Controllers;

use Core\Controller;
use Models\Users;

class LoginController extends Controller
{
    public function index()
    {
		$array = array();

		if (!empty($_SESSION['error']['login']))
		{
			$array['error'] = $_SESSION['error']['login'];
			unset($_SESSION['error']['login']);
		}

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
			
			$_SESSION['error']['login'] = 'Email ou senha inválido';
		}

		header('Location:' . BASE_URL .'login');
		exit;
	}

	public function logout()
	{
		$token = $_SESSION['token'];
		unset($_SESSION['token']);
		header('Location:'. BASE_URL .'login?logout='. $token);
	}
}