<?php

class Filters extends model
{
  public function getFilters($filters)
  {
    $dados = [
      'brands' => [],
      'maxslider' => 999,
      'stars' => [0,0,0,0,0,0],
      'slider' => [
        'min' => 0,
        'max' => 0
      ],
      'sale' => false,
      'options' => [],
    ];

    $p = new Products;
    $dados['brands'] = $p->getListTotalItems($filters);

    // preço
    $dados['maxslider'] = $p->getMaxPrice($filters);
    $dados['slider']['min'] = !empty($filters['slider']['min']) ? $filters['slider']['min'] : 0;
    $dados['slider']['max'] = !empty($filters['slider']['max']) ? $filters['slider']['max'] : $dados['maxslider'];
    
    

    // filtro de estrelas
    $star_products = $p->getListOfStars($filters);
    
    foreach ($star_products as $s)
    {
      $dados['stars'][$s['rating']] = $s['qtd'];
    }

    // promoção
    $dados['sale'] = $p->getSaleCount($filters);

    // options
    $dados['options'] = $p->getAvailableOptions($filters);

    return $dados;
  }
}