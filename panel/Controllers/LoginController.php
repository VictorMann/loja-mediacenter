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
}