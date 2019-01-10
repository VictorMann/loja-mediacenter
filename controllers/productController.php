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
        $dados = Store::getTemplateData();

        $products = new Products;
        $categories = new Categories;

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

        $this->loadTemplate('product', $dados);
    }
}