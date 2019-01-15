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

    public function checkout()
    {
        echo json_encode($_POST);

        if ($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            header('Location: '. BASE_URL);
            exit;
        }

        $id = $_POST['id'];
        $cartao_cpf = $_POST['cartao_cpf'];
        $cartao_titular = $_POST['cartao_titular'];
        $cartao_token = $_POST['cartao_token'];
        $cep = $_POST['cep'];
        $cvv = $_POST['cvv'];
        $email = $_POST['email'];
        $endereco = $_POST['endereco'];
        $name = $_POST['name'];
        $password = $_POST['password'];
        $numero = $_POST['numero'];
        $v_ano = $_POST['v_ano'];
        $v_mes = $_POST['v_mes'];

        $users = new Users;
        $cart = new Cart;
        $purchases = new Purchases;
        $uid = 0;

        if ($users->emailExists($email))
        {
            $uid = $users->validate($email, $password);
            
            if (!$uid)
            {
                echo json_encode([
                    'error' => true,
                    'message' => 'Email ou senhas não conferem'
                ]);
                exit;
            }
            
        }
        else
        {
            $users->create($name, $email, $password);
            $uid = $users->id;
        }

        $total = $cart->getSubTotal() + $_SESSION['shipping']['price'];
        $id_purchases = $purchases->create($uid, $total, 'psckttransparente');

        foreach ($cart->all() as $item)
        {
            $purchases->addItem($id_purchases, $item['id'], $item['qt'], $item['price']);
        }



    }
}