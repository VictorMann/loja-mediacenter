<?php

class mpController extends controller
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
                $id_purchases = $purchases->create($uid, $_SESSION['total_com_frete'], 'mp');

                // inserindo os intens na compra
                $list = $cart->all();
                foreach ($list as $item)
                {
                    $purchases->addItem($id_purchases, $item['id'], $item['qt'], $item['price']);
                }

                $mp = new MP(MERCADOPADO_ID, MERCADOPADO_KEY);

                // dados da compra
                $data = [
                    'items' => [],
                    'shipments' => [ // dados de array, caso n tenha desnecessario
                        'mode' => 'custom', // tipo de frete
                        'cost' => floatval($_SESSION['shipping']['price']),
                        'receiver_address' => [ // nao é obrigatório
                            'zip_code' => $cep
                        ]
                    ],
                    'back_urls' => [ // bapinas a serem redirecionadas
                        'success' => BASE_URL .'mp/obrigadoaprovado',   // pagamento sucesso
                        'pending' => BASE_URL .'mp/obrigadoanalise',    // pagamento em analise
                        'failure' => BASE_URL .'mp/obrigadocancelado'   // falha no pagamento
                    ],
                    // mudança de estado no mp, te mando um aviso
                    'notification_url' => BASE_URL . 'mp',
                    // qnd terminar o pagamento o q vc quer q aconteça
                    // que retonar para seu site, nas situações success, pending, failure
                    // all : todas retornam
                    // approved : apenas aprovadas
                    'auto_return' => 'all', // approved | all
                    'external_reference' => $id_purchases
                ];

                foreach ($list as $item)
                {
                    $data['items'][] = [
                        'title' => $item['name'],
                        'quantity' => $item['qt'],
                        'currency_id' => 'BRL',
                        'unit_price' => floatval($item['price'])
                    ];
                }

                // link para redirecionar
                $link = $mp->create_preference($data);

                // alanisar o retorno
                // echo '<pre>';
                // print_r($link);
                // exit;

                // venda oficial - ambiente de produção
                // $link = $link['response']['init_point'];
                // sandbox - ambiente de teste
                $link = $link['response']['sandbox_init_point'];

                header('Location: '. $link);
                exit;               
            }
        }

        $this->loadTemplate('cart_mp', $dados);
    }
}