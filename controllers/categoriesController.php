<?php
class categoriesController extends controller 
{
    public function __construct ()
    {
        parent::__construct();

        $this->categories = new Categories;
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

        $dados['list'] = $products->getList($offset, $limit);
        $dados['totalItems'] = $products->getTotal();
        $dados['numberOfPages'] = ceil($dados['totalItems'] / $limit);
        $dados['currentPage'] = $currentPage;
        $dados['categorias'] = $this->categories->getList();

        $this->loadTemplate('home', $dados);
    }

    public function enter($id)
    {
        $dados = [];
        
        $dados['categorias'] = $this->categories->getList();
        $dados['filter_category'] = $this->categories->getCategoryTree($id);

        $this->loadTemplate('categories', $dados);
    }
}