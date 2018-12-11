<?php

class Filters extends model
{
  public function getFilters($filters)
  {
    $dados = [
      'brands' => [],
      'maxslider' => 999,
      'stars' => [0,0,0,0,0,0],
      'sale' => false,
      'options' => [],
    ];

    $p = new Products;
    $dados['brands'] = $p->getListTotalItems($filters);
    $dados['maxslider'] = $p->getMaxPrice($filters);

    // filtro de estrelas
    $star_products = $p->getListOfStars($filters);
    
    foreach ($star_products as $s)
    {
      $dados['stars'][$s['rating']] = $s['qtd'];
    }

    // promoção
    $dados['sale'] = $p->getSaleCount($filters);

    return $dados;
  }
}