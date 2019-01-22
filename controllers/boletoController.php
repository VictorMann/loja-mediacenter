<?php

class boletoController extends controller
{
    private $user;

    public function __construct() 
    {
        parent::__construct();
    }

    public function index() 
    {
        $users = new Users;
        $cart = new Cart;
        $purchases = new Purchases;

        $dados = Store::getTemplateData();
        $dados['error'] = false;

        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $cpf = $_POST['cpf'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $cep = $_POST['cep'];
            $endereco = $_POST['endereco'];
            $numero = $_POST['numero'];
            $complemento = $_POST['complemento'];
            $bairro = $_POST['bairro'];
            $cidade = $_POST['cidade'];
            $estado = $_POST['estado'];

            $users = new Users;
            $cart = new Cart;
            $purchases = new Purchases;
            $uid = 0;

            if ($users->emailExists($email))
            {
                $uid = $users->validate($email, $password);
                
                if (!$uid)
                {
                    $dados['error'] = 'Email ou senhas não conferem'; 
                }
                
            }
            else
            {
                $users->create($name, $email, $password);
                $uid = $users->id;
            }

            // se não houve erro na geração/obtenção do mesmo
            // prosegui com o pagamento
            if ($uid)
            {
                // criando a compra
                $id_purchases = $purchases->create($uid, $_SESSION['total_com_frete'], 'paypal');

                // inserindo os intens na compra
                $list = $cart->all();
                foreach ($list as $item)
                {
                    $purchases->addItem($id_purchases, $item['id'], $item['qt'], $item['price']);
                }

                // Integração com Boleto
                
            }
        }

        $this->loadTemplate('cart_boleto', $dados);
    }
}