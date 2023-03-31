<?php
namespace App\Controller;

use App\Entity\PriceSearch;
use App\Form\PriceSearchType;
use App\Entity\CategorySearch;
use App\Form\CategorySearchType;
use App\Form\ArticleType;
use App\Form\PropertySearchType;
use App\Entity\PropertySearch;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Category;
use App\Form\CategoryType;

class IndexController extends AbstractController
{
  private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
   {
      $this->entityManager = $entityManager;
   }

   public function home(Request $request)
   { 
     $propertySearch = new PropertySearch();
     $form = $this->createForm(PropertySearchType::class,$propertySearch);
     $form->handleRequest($request);
    //initialement le tableau des articles est vide, 
    //c.a.d on affiche les articles que lorsque l'utilisateur clique sur le bouton rechercher
     $articles= [];
     
    if($form->isSubmitted() && $form->isValid()) {
    //on récupère le nom d'article tapé dans le formulaire
     $nom = $propertySearch->getNom();   
     if ($nom!="") 
       //si on a fourni un nom d'article on affiche tous les articles ayant ce nom
       $articles= $this->entityManager->getRepository(Article::class)->findBy(['nom' => $nom] );
     else   
       //si si aucun nom n'est fourni on affiche tous les articles
       $articles=$this->entityManager->getRepository(Article::class)->findAll();
    }
     return  $this->render('articles/index.html.twig',[ 'form' =>$form->createView(), 'articles' => $articles]);  
   }

 public function save() {

    $article = new Article();
  $article->setNom('Article 3');
  $article->setPrix(300);

  $this->entityManager->persist($article);
  $this->entityManager->flush();
  return new Response('Article enregisté avec id '.$article->getId());
  }

  public function new(Request $request) {
    $article = new Article(); 
    $form = $this->createForm(ArticleType::class,$article); 
    $form->handleRequest($request); if($form->isSubmitted() && $form->isValid()) {
        $article = $form->getData();
        $this->entityManager->persist($article);
        $this->entityManager->flush();
          return $this->redirectToRoute('article_list'); } 
   return $this->render('articles/new.html.twig',['form' => $form->createView()]); }
 
   
   
  public function show($id) {
    $article = $this->entityManager->getRepository(Article::class)
    ->find($id);
    return $this->render('articles/show.html.twig',
    array('article' => $article));
   }

   public function edit(Request $request, $id) {
    $article = new Article();
   $article = $this->entityManager->getRepository(Article::class)->find($id);
   
    $form = $this->createForm(ArticleType::class,$article);
   
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
   
    //$entityManager = $this->entityManager->getManager();
    $entityManager->flush();
   
    return $this->redirectToRoute('article_list');
    }
   
    return $this->render('articles/edit.html.twig', ['form' =>
   $form->createView()]);
    }
   

     public function delete(Request $request, $id) {
      $article = $this->entityManager->getRepository(Article::class)->find($id);
     
      $entityManager = $this->entityManager;
      $entityManager->remove($article);
      $entityManager->flush();
     
      $response = new Response();
      $response->send();
      return $this->redirectToRoute('article_list');
      }

      public function newCategory(Request $request) 
{
   $category = new Category();
    $form = $this->createForm(CategoryType::class,$category); 
    $form->handleRequest($request); 
    if($form->isSubmitted() && $form->isValid()) {
       $article = $form->getData(); 
       
       $this->entityManager->persist($category);
       $this->entityManager->flush();
       } 
       return $this->render('articles/newCategory.html.twig',['form'=> $form->createView()]); 
      }
      
      public function articlesParCategorie(Request $request) {
        $categorySearch = new CategorySearch();
        $form = $this->createForm(CategorySearchType::class,$categorySearch);
        $form->handleRequest($request);
        $articles= [];
        if($form->isSubmitted() && $form->isValid()) {
          $category = $categorySearch->getCategory();
         
          if ($category!="")
         $articles= $category->getArticles();
          else
          $articles= $this->entityManager->getRepository(Article::class)->findAll();
          }
          return $this->render('articles/articlesParCategorie.html.twig',['form' => $form->createView(),'articles' => $articles]);
          }

          
 public function articlesParPrix(Request $request)
 {

 $priceSearch = new PriceSearch();
 $form = $this->createForm(PriceSearchType::class,$priceSearch);
 $form->handleRequest($request);
 $articles= [];
 if($form->isSubmitted() && $form->isValid()) {
 $minPrice = $priceSearch->getMinPrice();
 $maxPrice = $priceSearch->getMaxPrice();

 $articles= $this->entityManager->getRepository(Article::class)->findByPriceRange($minPrice,$maxPrice);
 }
 return $this->render('articles/articlesParPrix.html.twig',[ 'form' =>$form->createView(), 'articles' => $articles]);
 }
}
?>