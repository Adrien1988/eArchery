<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
  #[Route('/product', name: 'app_product')]
  public function index(ProduitRepository $repo, Request $request): Response
  {
    $form = $this->createForm(RechercheType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $data = $form->get('recherche')->getData();
      $produits = $repo->getProduitByName($data);
    } else {
      $produits = $repo->getProduitParOrdreAlpha();
    }
    return $this->render('product/index.html.twig', [
      'produits' => $produits,
      'formRecherche' => $form->createView()
    ]);
  }
  /**
   * @Route("/product/show/{id}", name="product_show")
   */
  public function show(Produit $produit = null, Request $request, EntityManagerInterface $manager)
  {

    if (!$produit) {
      return $this->render("404.html.twig");
    }

    $commentaire = new Commentaire;

    $form = $this->createForm(PostCommentaireType::class, $commentaire);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $commentaire->setCreatedAt(new \DateTime())
        ->setProduit($produit)
        ->setAuteur($this->getUser());

      $manager->persist($commentaire);
      $manager->flush();

      return $this->redirectToRoute('product_show', [
        'id' => $produit->getId()
      ]);
    }
    return $this->render("product/show.html.twig", [
      'produit' => $produit,
      'commentaireForm' => $form->createView()
    ]);
  }

  /**
   * @Route("/product/profil", name="product_profil")
   */
  public function profil(ProduitRepository $repo)
  {
    if (!$this->getUser()) {
      return $this->redirectToRoute('app_product');
    }

    $produit = $repo->findBy(["auteur" => $this->getUser()]);

    return $this->render("product/profil.html.twig", [
      'produit' => $produit
    ]);
  }

  /**
   * @Route("/product/new", name="product_create")
   * @Route("/product/edit/{id}", name="product_edit")
   */
  public function form(Request $request, EntityManagerInterface $manager, Produit $produit = null)
  {

    if (!$produit) {

      $produit = new Produit;
    }

    $form = $this->createForm(ProduitType::class, $produit);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $produit->setAuteur($this->getUser());

      if (!$produit->getId()) {

        if (!$produit->getImageFile()) {

          return $this->redirectToRoute("product_create");
        }
      }
      $manager->persist($produit);
      $manager->flush();

      return $this->redirectToRoute('product_show', [
        'id' => $produit->getId()
      ]);
    }

    return $this->render("product/create.html.twig", [
      'formProduit' => $form->createView(),
      'editMode' => $produit->getId() !== null
    ]);
  }
}
