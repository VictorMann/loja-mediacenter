<?php

class Cart extends model
{
    public function all()
    {
        $dados = [];
        $products = new Products;
        $cart = $_SESSION['cart'];

        foreach ($cart as $id => $qt)
        {
            $p = $products->get($id);
            $i = $products->getImageByProductId($id)[0];
            
            $dados[] = [
                'id' => $id,
                'qt' => $qt,
                'name' => $p['name'],
                'price' => $p['price'],
                'image' => $i['url'],
                'weight' => $p['weight'],
                'width' => $p['width'],
                'height' => $p['height'],
                'length' => $p['length'],
                'diameter' => $p['diameter']
            ];
        }

        return $dados;
    }

    public static function getSubTotal()
    {
        if (empty($_SESSION['cart'])) return 0;

        $tal = 0;
        $product = new Products;

        foreach ($_SESSION['cart'] as $id => $qt)
            $tal += $qt * ($product->get($id)['price']);

        return $tal;
    }

    public function shippingCalculate($cep_destino)
    {
        $dados = [
            'price' => 0,
            'date' => ''
        ];

        $products = $this->all();
        

        // total da some das medidas de todos os produtos do cart
        $tp = (object) array_reduce($products, function($a, $b) {
            
            if (!isset($a['valor_declarado']))
            {
                $a = $b;
                $a['valor_declarado'] = $a['qt'] * $a['price'];
                return $a;
            }
            
            $a['weight']   += $b['weight'];
            $a['width']    += $b['width'];
            $a['height']   += $b['height'];
            $a['length']   += $b['length'];
            $a['diameter'] += $b['diameter'];
            $a['valor_declarado'] += $b['qt'] * $b['price'];
            return $a;
        }, []);

        // verifcação de limites estabelecidos pelo correio
        $this->validate_correios($tp);

        global $config;

        // dados p/ web service
        $data = [
            // tipo de envio sedex, sedex hj, pac...
            'nCdServico' => '40010', // sedex
            'sCepOrigem' => $config['cep_origin'],
            'sCepDestino' => $cep_destino,
            'nVlPeso' => $tp->weight,
            'nCdFormato' => '1', // caixa
            'nVlComprimento' => $tp->length,
            'nVlAltura' => $tp->height,
            'nVlLargura' => $tp->width,
            'nVlDiametro' => $tp->diameter,
            'sCdMaoPropria' => 'N',
            'nVlValorDeclarado' => $tp->valor_declarado,
            'sCdAvisoRecebimento' => 'N',
            'StrRetorno' => 'xml'
        ];

        $data = http_build_query($data);

        // url web service correios
        $url = 'http://ws.correios.com.br/calculador/CalcPrecoprazo.aspx';
        
        $ch = curl_init($url .'?'. $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $r = curl_exec($ch);
        curl_close($ch);

        $r = simplexml_load_string($r);

        $dados['price'] = current($r->cServico->Valor);
        $dados['date']  = current($r->cServico->PrazoEntrega);
        
        return $dados;
    }

    private function validate_correios(&$tp)
    {
        $soma = $tp->width + $tp->height + $tp->length;
        if ($soma > 200)
        {
            $default = floor($soma / 3);
            $tp->width  = $default;
            $tp->height = $default;
            $tp->length = $default;
        }

        if ($tp->diameter > 90) $tp->diameter = 90;
        if ($tp->weight > 40) $tp->weight = 40;
    }
}