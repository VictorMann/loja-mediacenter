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
}