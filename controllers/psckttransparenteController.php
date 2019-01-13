<?php

class psckttransparenteController extends controller
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

        $dados = Store::getTemplateData();

        try
        {
            $sessionCode = \PagSeguro\Services\Session::create(
                \PagSeguro\Configuration\Configure::getAccountCredentials()
            );
            
            $dados['sessionCode'] = $sessionCode->getResult();
        }
        catch (Exeption $e)
        {
            echo $e->getMessage();
            exit;
        }
        
        $this->loadTemplate('cart_psckttransparente', $dados);
    }
}