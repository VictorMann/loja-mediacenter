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
        $dados['shipping'] = null;

        if (empty($_SESSION['shipping']))
        {
            $_SESSION['shipping'] = ['price' => 0, 'date' => 0];
        }

        if (!empty($_POST['cep']))
        {
            $cep = (int) $_POST['cep'];
            $dados['shipping'] = $cart->shippingCalculate($cep);
            $_SESSION['shipping'] = $dados['shipping'];
        }
        elseif (!empty($_SESSION['shipping']))
        {
            $dados['shipping'] = $_SESSION['shipping'];
        }
        
        $_SESSION['total_com_frete'] = $_SESSION['total_sem_frete'] + $_SESSION['shipping']['price'];


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

    public function del($id)
    {
        $this->ajustartotal($id);
        unset($_SESSION['cart'][$id]);
        header('Location: '. BASE_URL . 'cart');
        exit;
    }

    public function update()
    {
        // se não for uma requisição POST
        if ($_SERVER['REQUEST_METHOD'] <> 'POST') return;

        $id = (int) $_POST['id'];
        $qt = (int) $_POST['qt'];

        $o = new StdClass;
        $o->id      = $id;
        $o->qt_last = $_SESSION['cart'][$id];
        $o->qt_now  = $qt;

        $_SESSION['cart'][$id] = $qt;
        
        echo json_encode($o);
    }

    public function ajustartotal($id = NULL)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $_SESSION['total_sem_frete'] = (float) $_POST['valor'];
            echo $_SESSION['total_com_frete'] = $_SESSION['total_sem_frete'] + $_SESSION['shipping']['price'];
        }

        if ($id)
        {
            $p = new Products;
            $p = $p->get($id);

            $_SESSION['total_sem_frete'] -= (float) $p['price'];
            $_SESSION['total_com_frete'] = $_SESSION['total_sem_frete'] + $_SESSION['shipping']['price'];
        }
    }

    public function payment_redirect()
    {
        if (empty($_POST['payment_type']))
        {
            header('Location: '. BASE_URL . 'cart');
            exit;
        }

        $payment = $_POST['payment_type'];

        switch ($payment)
        {
            case 'checkout_transparent':
                header('Location:'. BASE_URL .'psckttransparente');
            break;
            case 'mp':
                header('Location:'. BASE_URL .'mp');
            break;
            case 'paypal':
                header('Location:'. BASE_URL .'paypal');
            break;
            case 'boleto':
                header('Location:'. BASE_URL .'boleto');
            break;
        }

        exit;
    }
}