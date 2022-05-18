<?php

namespace App\Controller;

use App\Service\Cart\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
  #[Route('/cart', name: 'app_cart')]
  public function index(CartService $cs): Response
  {
    $cartWithData = $cs->getCartWithData();
    $total = $cs->getTotal();
    return $this->render('cart/index.html.twig', [
      'items' => $cartWithData,
      'total' => $total
    ]);
  }
  /**
   * @Route("/", name="home")
   */
  public function home()
  {
    return $this->render("cart/home.html.twig", [
      'title' => 'Bienvenue sur le Site eArchery',
    ]);
  }

  /**
   * @Route("/cart/contact", name="cart_contact")
   */
  public function contact(Request $request, EntityManagerInterface $manager, ContactNotification $notification)
  {

    $contact = new Contact;
    if ($this->getUser()) {
      $contact->setEmail($this->getUser()->getUserIdentifier());
    }
    $form = $this->createForm(ContactType::class, $contact);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $contact->setCreatedAt(new \DateTime());
      $notification->notify($contact);
      $this->addFlash('success', 'Votre Email a bien été envoyé');
      $manager->persist($contact);
      $manager->flush();

      return $this->redirectToRoute('cart_contact');
    }
    return $this->render("cart/contact.html.twig", [
      'formContact' => $form->createView()
    ]);
  }

  /**
   * @Route("/cart/add/{id}", name="cart_add")
   */
  public function add($id, CartService $cs)
  {
    $cs->add($id);

    return $this->redirectToRoute('app_cart');
  }

  /**
   * @Route("/cart/remove/{id}", name="cart_remove")
   */
  public function remove($id, CartService $cs)
  {
    $cs->remove($id);
    return $this->redirectToRoute('app_cart');
  }

  /**
   * @Route("/cart/delete/{id}", name="cart_delete")
   */
  public function delete($id, CartService $cs)
  {
    $cs->delete($id);
    return $this->redirectToRoute('app_cart');
  }

  /**
   * @Route("/cart/delete", name="cart_delete_all")
   */
  public function deleteAll(CartService $cs)
  {
    $cs->deleteAll();
    return $this->redirectToRoute('app_cart');
  }

  /**
   * @Route("/cart/payed", name="cart_payed")
   */

  public function payed(CartService $cs)
  {
    $cs->payed();
    return $this->render("cart/payed.html.twig");
  }
}
