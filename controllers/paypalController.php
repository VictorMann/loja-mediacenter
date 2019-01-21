<?php

class paypalController extends controller
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

                // Integração com PAYPAL
                $apiContext = new \PayPal\Rest\ApiContext(
                    new \PayPal\Auth\OAuthTokenCredential(
                        PAYPAL_CLIENT_ID,
                        PAYPAL_SECRET
                    )
                );

                $payer = new \PayPal\Api\Payer();
                $payer->setPaymentMethod('paypal');

                $amount = new \PayPal\Api\Amount();
                $amount->setCurrency('BRL')->setTotal($_SESSION['total_com_frete']);

                $transaction = new \PayPal\Api\Transaction();
                $transaction->setAmount($amount);
                $transaction->setInvoiceNumber($id_purchases);

                $redirectUrls = new \PayPal\Api\RedirectUrls();
                $redirectUrls->setReturnUrl(BASE_URL. 'paypal/obrigado');
                $redirectUrls->setCancelUrl(BASE_URL. 'paypal/cancelar');

                $payment = new \PayPal\Api\Payment();
                $payment->setIntent('sale'); // dizendo que é uma vendas
                $payment->setPayer($payer);
                $payment->setTransactions([$transaction]);
                $payment->setRedirectUrls($redirectUrls);

                try {
                    $payment->create($apiContext);
                    header('Location:' . $payment->getApprovalLink());
                    exit;

                } catch (\PayPal\Exception\PayPalConnectionException $e) {
                    echo $e->getData();
                    exit;
                }
            }
        }

        $this->loadTemplate('cart_paypal', $dados);
    }

    public function obrigado()
    {
        $purchases = new Purchases;

        // Integração com PAYPAL
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                PAYPAL_CLIENT_ID,
                PAYPAL_SECRET
            )
        );

        $paymentId = $_GET['paymentId'];
        $payment = \PayPal\Api\Payment::get($paymentId, $apiContext);

        $execution = new \PayPal\Api\PaymentExecution();
        $execution->setPayerId($_GET['PayerID']);

        try {

            $result = $payment->execute($execution, $apiContext); // realiza o pagamento

            try {

                $payment = \PayPal\Api\Payment::get($paymentId, $apiContext);

                // status
                $status = $payment->getState(); 
                // obtem as transacoes pode ser mais de uma lembra do [$transaction]
                // current pega a primeira do array
                $t = current($payment->getTransactions());
                $t = $t->toArray();
                $ref = $t['invoice_number']; // id_purchases

                if ($status == 'approved')
                {
                    $purchases->setPaid($ref);

                    unset($_SESSION['cart']);
                    $dados = Store::getTemplateData();
                    $this->loadTemplate('paypal_obrigado', $dados);
                } 
                else 
                {
                    $purchases->setCancelled($ref);
                    header('Location: '. BASE_URL .'paypal/cancelar');
                    exit;
                }

            } catch (Exception $e) {
                header('Location: '. BASE_URL .'paypal/cancelar');
                exit;
            }

        } catch (Exception $e) {
            header('Location: '. BASE_URL .'paypal/cancelar');
            exit;
        }
    }

    public function cancelar()
    {
        unset($_SESSION['cart']);
        $dados = Store::getTemplateData();
        $this->loadTemplate('paypal_cancelar', $dados);
    }
}