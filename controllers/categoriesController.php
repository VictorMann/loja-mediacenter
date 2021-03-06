<?php
class categoriesController extends controller 
{
    public function __construct ()
    {
        parent::__construct();

        $this->limit = 3;
        $this->offset = 0;

        $this->categories = new Categories;
        $this->products = new Products;
    }


    public function index() 
    {
        $dados = Store::getTemplateData();

        $currentPage = 1;
        if (!empty($_GET['p']) && $_GET['p'] > 0) $currentPage = (int) $_GET['p'];

        $this->offset = $currentPage * $this->limit - $this->limit;

        $dados['list'] = $this->products->getList($this->offset, $this->limit);
        $dados['totalItems'] = $this->products->getTotal();
        $dados['numberOfPages'] = ceil($dados['totalItems'] / $this->limit);
        $dados['currentPage'] = $currentPage;
        $dados['categorias'] = $this->categories->getList();

        $dados['sidebar'] = true;
        
        $this->loadTemplate('home', $dados);
    }

    public function enter($id)
    {
        $f = new Filters;
        $dados = Store::getTemplateData();
        
        $dados['catName'] = $this->categories->getName($id);

        // fail-fast
        if (!$dados['catName'])
        {
            header('Location: '. BASE_URL);
            exit;
        }

        // filter
        $filters = ['category' => $id];

        $dados['categorias'] = $this->categories->getList();
        $dados['filter_category'] = $this->categories->getCategoryTree($id);

        // pagination
        $currentPage = 1;
        if (!empty($_GET['p']) && $_GET['p'] > 0) $currentPage = (int) $_GET['p'];

        $this->offset = $currentPage * $this->limit - $this->limit;
        
        $dados['sidebar'] = true;
        $dados['list'] = $this->products->getList($this->offset, $this->limit, $filters);
        $dados['totalItems'] = $this->products->getTotal($filters);
        $dados['numberOfPages'] = ceil($dados['totalItems'] / $this->limit);
        $dados['currentPage'] = $currentPage;

        $dados['filters'] = $f->getFilters($filters);
        $dados['filters_selected'] = $filters;

        


        $this->loadTemplate('categories', $dados);
    }
}