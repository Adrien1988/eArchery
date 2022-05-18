<?php


namespace App\Service\Cart;


use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
  private $request;

  public function __construct(RequestStack $request, ProduitRepository $repo)
  {
    $this->request = $request;
    $this->repo = $repo;
  }

  public function add($id){
    $session = $this->request->getSession();

    $cart = $session->get('cart', []);

    if (!empty($cart[$id])) {
      $cart[$id]++;
    } else {
      $cart[$id] = 1;
    }
    $session->set('cart', $cart);
  }

  public function remove($id){
    $session = $this->request->getSession();
    $cart = $session->get("cart", []);

    if (!empty($cart[$id])) {
      if ($cart[$id] > 1) {
        $cart[$id]--;
      } else {
        unset($cart[$id]);
      }
    }
    $session->set('cart', $cart);
  }

  public function delete($id){
    $session = $this->request->getSession();
    $cart = $session->get("cart", []);

    if(!empty($cart[$id])){
      unset($cart[$id]);
    }
    $session->set('cart', $cart);
  }

  public function deleteAll(){
    $session = $this->request->getSession();
    $session->remove("cart");
  }

  public function payed(){
    $session = $this->request->getSession();
    $session->remove("cart");
  }

  public function getCartWithData(){
    $session = $this->request->getSession();
    $cart = $session->get('cart', []);
    $qt = 0;

    $cartWithData = [];

    foreach ($cart as $id => $quantite) {
      $cartWithData[] = [
        'produit' => $this->repo->find($id),
        'quantite' => $quantite
      ];

      $qt += $quantite;
    }
    $session->set('qt', $qt);
    return $cartWithData;
  }

  public function getTotal(){
    $total = 0;
    $cartWithData = $this->getCartWithData();

    foreach ($cartWithData as $item) {
      $totalItem = $item['produit']->getPrix() * $item['quantite'];

      $total += $totalItem;
    }
    return $total;
  }
}
