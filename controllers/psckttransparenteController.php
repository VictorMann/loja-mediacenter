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
        
        $cartao_titular = $_POST['cartao_titular'];
        $cartao_cpf = $_POST['cartao_cpf'];
        $cartao_num = $_POST['cartao_num'];
        $cartao_token = $_POST['cartao_token'];
        $cvv = $_POST['cvv'];
        $v_mes = $_POST['v_mes'];
        $v_ano = $_POST['v_ano'];
        $parc = explode(';', $_POST['parc']);

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

        $id_purchases = $purchases->create($uid, $_SESSION['total_com_frete'], 'psckttransparente');

        foreach ($cart->all() as $item)
        {
            $purchases->addItem($id_purchases, $item['id'], $item['qt'], $item['price']);
        }

        $creditCard = new \PagSeguro\Domains\Requests\DirectPayment\CreditCard;
        $creditCard->setReceiverEmail(PAGSEGURO_USER); // constante do environment
        $creditCard->setReference($id_purchases);
        $creditCard->setCurrency('BRL');

        foreach ($cart->all() as $item)
        {
            $creditCard
            ->addItems()
            ->withParameters(
                $item['id'],
                $item['name'],
                intval($item['qt']),
                floatval($item['price'])
            );
        }
        // adição do frete ao valor
        $creditCard->setShipping()->setCost()->withParameters($_SESSION['shipping']['price']);

        // obtem o IP
        $ip = strlen($_SERVER['REMOTE_ADDR']) < 7 ? '127.0.0.1' : $_SERVER['REMOTE_ADDR'];
        $creditCard->setSender()->setName($name);
        $creditCard->setSender()->setEmail($email);
        $creditCard->setSender()->setDocument()->withParameters('CPF', $cartao_cpf);
        $ddd = substr($phone, 0, 2);
        $phone = substr($phone, 2);
        $creditCard->setSender()->setPhone()->withParameters($ddd, $phone);
        $creditCard->setSender()->setHash($id);
        $creditCard->setSender()->setIp($ip);
        
        $creditCard->setShipping()->setAddress()->withParameters(
            $endereco,
            $numero,
            $bairro,
            $cep,
            $cidade,
            $estado,
            'BRA',
            $complemento
        );

        $creditCard->setBilling()->setAddress()->withParameters(
            $endereco,
            $numero,
            $bairro,
            $cep,
            $cidade,
            $estado,
            'BRA',
            $complemento
        );

        $creditCard->setToken($cartao_token);
        $creditCard->setInstallment()->withParameters($parc[0], $parc[1]); // com juros, para sem exige o 3 param
        $creditCard->setHolder()->setName($cartao_titular);
        $creditCard->setHolder()->setDocument()->withParameters('CPF', $cartao_cpf);
        
        $creditCard->setMode('DEFAULT');

        // dizer ao PAGSEGURO qual url para ele requisitar
        // qnd houver mudaças de estado
        $creditCard->setNotificationUrl(BASE_URL .'psckttransparente/notification');

        try {
            $result = $creditCard->register(
                // credenciais
                \PagSeguro\Configuration\Configure::getAccountCredentials()
            );

            echo json_encode($result);
            exit;

        } catch (Exception $e) {
            echo json_encode(['error' => true, 'msg' => $e->getMessage()]);
            exit;
        }
    }

    public function notification()
    {
        try {
            // o proprio pagseguro verifica os dados que ele envia para as notificações
            if (\PagSeguro\Helpers\Xhr::hasPost())
            {
                $purchases = new Purchases;

                $r = \PagSeguro\Services\Transactions\Notification::check(
                    // credenciais
                    \PagSeguro\Configuration\Configure::getAccountCredentials()
                );

                // qual item houve a mudança de estado
                // nossa referencia que mandamos id_purchases
                $ref = $r->getReference();
                // status
                // 1 = aguardando pagamento
                // 2 = Em analise
                // 3 = Paga
                // 4 = Disponível - aprovação para o dinhero na sua conta
                // 5 = Em disputa - usuario entra em contato com o pagseguro, não recebeu o que comprou 
                // 6 = Devolução do dinheiro - ref a disputa
                // 7 = Cancelado
                // 8 = Debitado - compro ganho a disputa e o dinheiro já esta na conta do cliente
                // 9 = Retenção Temporária - chargeback - cartao invalido, procom
                $status = $r->getStatus();

                if ($status == 3) // pago
                {     
                    $purchases->setPaid($ref);
                }
                else if ($status == 7) // cancelada
                {
                    $purchases->setCancelled($ref);
                }

            }

        } catch (Exception $e) {
            echo 'ERROR: '. $e->getMessage();
        }
    }

    // Compra realizada 
    public function obrigado()
    {
        // limpa o carrinho
        unset($_SESSION['cart']);
        
        $dados = Store::getTemplateData();

        $this->loadTemplate('psckttransparente_obrigado', $dados);
    }
}