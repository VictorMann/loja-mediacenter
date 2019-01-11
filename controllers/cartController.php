<?php

class cartController extends controller
{
    private $user;

    public function __construct() 
    {
        parent::__construct();
    }

    public function index() 
    {
        $cart = new Cart;
        $products = new Products;

        if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0)
        {
            header('Location: '. BASE_URL);
            exit;
        }
        
        $dados = Store::getTemplateData();

        $dados['list'] = $cart->all();


        $this->loadTemplate('cart', $dados);
    }

    public function add()
    {
        if (!empty($_POST['id_product']))
        {
            $id = intval($_POST['id_product']);
            $qt = intval($_POST['qt']);

            if (isset($_SESSION['cart'][$id]))
                $_SESSION['cart'][$id] += $qt;
            else
                $_SESSION['cart'][$id] = $qt;
        }

        header('Location: '. BASE_URL .'cart');
        exit;
    }
}