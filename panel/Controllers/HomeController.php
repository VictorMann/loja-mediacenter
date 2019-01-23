<?php
namespace Controllers;

use Core\Controller;
use Models\Exemplo;

class HomeController extends Controller {

	public function index() {
		$array = array();

		$exemplo = new Exemplo();

		$this->loadTemplate('home', $array);
	}

}