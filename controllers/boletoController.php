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
                $id_purchases = $purchases->create($uid, $_SESSION['total_com_frete'], 'boleto');

                // inserindo os intens na compra
                $list = $cart->all();
                foreach ($list as $item)
                {
                    $purchases->addItem($id_purchases, $item['id'], $item['qt'], $item['price']);
                }

                // Integração com Boleto

                // definindo lista dos itens 
                $items = [];
                foreach ($list as $item)
                {
                    $items[] = [
                        'name' => $item['name'],
                        'amount' => $item['qt'],
                        'value' => $item['price'] * 100 // em centavos
                    ];
                }

                $metadata = [
                    'custom_id' => $id_purchases,
                    'notification_url' => BASE_URL. 'boleto/notification' // req ao ter mudança de estado
                ];

                $shipping = [
                    [
                        'name' => 'FRETE',
                        'value' => $_SESSION['shipping']['price'] * 100 // em centavos
                    ]
                ];

                $body = [
                    'metadata' => $metadata,
                    'items' => $items,
                    'shippings' => $shipping
                ];

                try {

                    $api = new \Gerencianet\Gerencianet([
                        'client_id' => GERENCIANET_ID,
                        'client_secret' => GERENCIANET_SECRET,
                        'sandbox' => GERENCIANET_SANDBOX
                    ]);

                    $charge = $api->createCharge([], $body);

                    // valida
                    if ($charge['code'] == 200) {
                        $charge_id = $charge['data']['charge_id'];

                        $params = [
                            'id' => $charge_id
                        ];

                        $customer = [
                            'name' => $name,
                            'cpf' => $cpf,
                            'phone_number' => $phone
                        ];

                        $bankingBillet = [
                            'expire_at' => date('Y-m-d', strtotime('+4 days')),
                            'customer' => $customer
                        ];

                        $payment = [
                            'banking_billet' => $bankingBillet
                        ];

                        $body = [
                            'payment' => $payment
                        ];

                        try {

                            $charge = $api->payCharge($params, $body);

                            if ($charge['code'] == 200) {

                                // link do boleto
                                $link = $charge['data']['link'];
                                // guardar link do boleto
                                $purchases->updateBilletUrl($id_purchases, $link);
                                // remove do carrinho
                                unset($_SESSION['cart']);
                                // redireciona para o boleto
                                header('Location: '. $link);
                                exit;
                            }

                        } catch (Exception $e) {
                            echo 'ERRO ';
                            print_r($e->getMessage());
                            exit;
                        }
                    }

                } catch (Exception $e) {
                    echo 'ERRO ';
                    print_r($e->getMessage());
                    exit;
                }
                
            }
        }

        $this->loadTemplate('cart_boleto', $dados);
    }

    public function notification()
    {

    }
}