<?php

class productController extends controller
{
	private $user;

    public function __construct() 
    {
        parent::__construct();
    }

    public function index() {}

    public function open() 
    {
        $dados = array();

        $products = new Products;
        $categories = new Categories;
        $f = new Filters;

        $filters = [];

        $dados['filters'] = $f->getFilters($filters);
        $dados['filters_selected'] = $filters;

        $dados['widget_featured1'] = $products->getList(0, 5, ['featured' => 1], true);
        $dados['widget_featured2'] = $products->getList(0, 3, ['featured' => 1], true);
        $dados['widget_sale'] = $products->getList(0, 3, ['sale' => 1], true);
        $dados['widget_toprated'] = $products->getList(0, 3, ['toprated' => 1]);

        $this->loadTemplate('product', $dados);
    }
}