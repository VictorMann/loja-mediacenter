<?php
class homeController extends controller {

	private $user;

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $dados = array();

        $products = new Products;
        $dados['list'] = $products->getList();

        // print_r($dados);exit;

        $this->loadTemplate('home', $dados);
    }

}