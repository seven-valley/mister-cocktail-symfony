<?php

namespace App\Controller;

use App\Entity\Cocktail;
use App\Form\CocktailType;
use App\Repository\CocktailRepository;
use App\Repository\CouleurRepository;
use App\Repository\FruitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CocktailRepository $repo): Response
    {
        return $this->render('main/index.html.twig', [
            'cocktails' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("/ajouter", name="ajouter")
     */
    public function ajouter(EntityManagerInterface $em,Request $req,FruitRepository $repoF): Response
    {
        $cocktail = new Cocktail();
        $formCocktail = $this->createForm(CocktailType::class,$cocktail);
        $formCocktail ->handleRequest($req);
        if ($formCocktail->isSubmitted()){
            $fruitId = $req->get('fruit');
            $fruit = $repoF->find($fruitId);
            $cocktail->setFruit($fruit);
            $em->persist($cocktail);
            $em->flush();
            return $this->redirectToRoute('home');

        }
        return $this->render('main/ajouter.html.twig', [
            'formCocktail' => $formCocktail->createView(),
        ]);
    }
    /**
     * @Route("/fruits-couleurs", name="api_fruits_couleurs")
     */
    public function fruitsCouleurs(FruitRepository $repoF,CouleurRepository $repoC): Response
    {
         $tab['fruits']= $repoF->findAll();
         $tab['couleurs']= $repoC->findAll();

        return $this->json($tab);
    }
}
