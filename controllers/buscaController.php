<?php
class buscaController extends controller {

	private $user;

    public function __construct() 
    {
        parent::__construct();
    }

    public function index() 
    {
        $searchTerm = !empty($_GET['s'])? $_GET['s'] : null;
        if (!$searchTerm) {
            header('Location: ./');
            exit;
        }

        $dados = Store::getTemplateData();

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

        // busca
        $filters['searchTerm'] = $searchTerm;
        $filters['category'] = $_GET['category'];

        $dados['list'] = $products->getList($offset, $limit, $filters);
        $dados['totalItems'] = $products->getTotal($filters);
        $dados['numberOfPages'] = ceil($dados['totalItems'] / $limit);
        $dados['currentPage'] = $currentPage;
        $dados['categorias'] = $categories->getList();
        $dados['filters'] = $f->getFilters($filters);
        $dados['filters_selected'] = $filters;
        $dados['searchTerm'] = $searchTerm;
        $dados['category'] = $filters['category'];

        $dados['sidebar'] = true;

        $this->loadTemplate('busca', $dados);
    }
}