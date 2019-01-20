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
                    'notification_url' => BASE_URL . 'mp/notification',
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

                if ($link['status'] == 201)
                {
                    // $link['response']['init_point']; - venda oficial ambiente de produção
                    // $link['response']['sandbox_init_point']; - ambiente de teste muito instável
                    $link = $link['response']['init_point'];

                    header('Location: '. $link);
                    exit;  
                }
            }
        }

        $this->loadTemplate('cart_mp', $dados);
    }

    public function notification()
    {
        
        $mp = new MP(MERCADOPADO_ID, MERCADOPADO_KEY);
        $purchases = new Purchases;

        // para dizer que não é ambiente de teste
        $mp->sandbox_mode(false);

        // pega as informações
        $info = $mp->get_payment_info($_GET['id']);

        // verifica se deu tudo OK
        if ($info['status'] == 200)
        {
            // pega os dados
            $dados = $info['response'];

            // referencia do produto id_purchases
            $ref = $dados['collection']['external_reference'];
            // status
            // - pending : Em análise
            // - approved : Aprovado
            // - in_procress : Em revisão
            // - in_mediation : Em processo de disputa
            // - rejected : Foi rejeitado
            // - cancelled : Foi cancelado
            // - refunded : Reembolsado
            // - charged_back : Chargeback
            $status = $dados['collection']['status'];

            if ($status == 'approved') { // caso aprovado
                $purchases->setPaid($ref);
            }
            else if ($status == 'cancelled') { // caso cancelado
                $purchases->setCancelled($ref);
            }

            // demais casos...

            // gera um log com os dados
            file_put_contents('logs/mplog.txt', print_r($dados, true));
        }

    }
}