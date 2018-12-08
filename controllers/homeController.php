<?php
class homeController extends controller {

	private $user;

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $dados = array();

        $limit = 3;
        $offset = 0;

        $currentPage = 1;
        if (!empty($_GET['p']) && $_GET['p'] > 0) $currentPage = (int) $_GET['p'];

        $offset = $currentPage * $limit - $limit;

        $products = new Products;
        $dados['list'] = $products->getList($offset, $limit);
        $dados['totalItems'] = $products->getTotal();
        $dados['numberOfPages'] = ceil($dados['totalItems'] / $limit);
        $dados['currentPage'] = $currentPage;
        // print_r($dados);exit;

        $this->loadTemplate('home', $dados);
    }

}