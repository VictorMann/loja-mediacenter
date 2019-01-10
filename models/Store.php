<?php

class Store extends model
{
    public static function getTemplateData()
    {
        $dados = [];
        $products = new Products;
        $categories = new Categories;

        $dados['categorias'] = $categories->getList();
        $dados['widget_featured1'] = $products->getList(0, 5, ['featured' => 1], true);
        $dados['widget_featured2'] = $products->getList(0, 3, ['featured' => 1], true);
        $dados['widget_sale'] = $products->getList(0, 3, ['sale' => 1], true);
        $dados['widget_toprated'] = $products->getList(0, 3, ['toprated' => 1]);

        return $dados;
    }
}