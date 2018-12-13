<?php
class homeController extends controller {

	private $user;

    public function __construct() 
    {
        parent::__construct();
    }

    public function index() 
    {
        $dados = array();

        $limit = 3;
        $offset = 0;

        $currentPage = 1;
        if (!empty($_GET['p']) && $_GET['p'] > 0) $currentPage = (int) $_GET['p'];

        $offset = $currentPage * $limit - $limit;

        $products = new Products;
        $categories = new Categories;
        $f = new Filters;

        $filters = [];

        if (!empty($_GET['filter']) and is_array($_GET['filter']))
        {
            $filters = $_GET['filter'];
        }

        $dados['list'] = $products->getList($offset, $limit, $filters);
        $dados['totalItems'] = $products->getTotal($filters);
        $dados['numberOfPages'] = ceil($dados['totalItems'] / $limit);
        $dados['currentPage'] = $currentPage;
        $dados['categorias'] = $categories->getList();
        $dados['filters'] = $f->getFilters($filters);
        $dados['filters_selected'] = $filters;

        $this->loadTemplate('home', $dados);
    }
}