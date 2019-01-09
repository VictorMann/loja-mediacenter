<?php

class productController extends controller
{
	private $user;

    public function __construct() 
    {
        parent::__construct();
    }

    public function index() {}

    public function open($id) 
    {
        $dados = array();

        $products = new Products;
        $categories = new Categories;
        $f = new Filters;

        $filters = [];

        $product = $products->get($id);
        if (!$product)
        {
            header('Location: '. BASE_URL);
            exit;
        }
        
        $dados['p'] = $product;
        $dados['p_imgs'] = $products->getImageByProductId($id);
        $dados['p_opts'] = $products->getOptions($id);
        $dados['p_rates'] = $products->getRates($id, 5);
        
        $dados['filters'] = $f->getFilters($filters);
        $dados['filters_selected'] = $filters;

        $dados['widget_featured1'] = $products->getList(0, 5, ['featured' => 1], true);
        $dados['widget_featured2'] = $products->getList(0, 3, ['featured' => 1], true);
        $dados['widget_sale'] = $products->getList(0, 3, ['sale' => 1], true);
        $dados['widget_toprated'] = $products->getList(0, 3, ['toprated' => 1]);

        $this->loadTemplate('product', $dados);
    }
}