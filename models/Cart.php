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
                'image' => $i['url']
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
}