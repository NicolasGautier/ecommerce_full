<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Code\Generator\DocBlock\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProductController extends AbstractController
{

    /**
     * @Route("/nos-produits", name="products")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {

        $products = $entityManager->getRepository(Product::class)->findAll();
        $conn = $entityManager->getConnection();

        foreach ($products as $product){
            $product_id = $product->getId();
            $query = "SELECT tag.name FROM tag INNER JOIN products_tags ON products_tags.tag_id = tag.id WHERE products_tags.product_id = :product_id;";
            $stmt = $conn->prepare($query);
            $resultSet = $stmt->executeQuery(['product_id' => $product_id]);
            $product->tag_names = $resultSet->fetchFirstColumn();
        }

        $search =new Search();
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if($form->isSubmitted()&&$form->isValid()){
            $products = $entityManager->getRepository(Product::class)-> findWithSeach($search) ;
        }

        return $this->render('product/index.html.twig', [
            'products'=> $products,
            'form' =>$form->createView()
        ]);
    }

    /**
     * @Route ("/manytomany")
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function  manyToMany(EntityManagerInterface $entityManager){

        $tag = new \App\Entity\Tag();
        $tag->setName("SuperTop");
        $entityManager->persist($tag);

        $product = $entityManager->getRepository(Product::class)->find(5);
        $product->addTag($tag);
        $entityManager->flush();

        return new Response(sprintf($tag->getName().' has been added to'));
    }

    /**
     * @Route("/produit/{slug}", name="product")
     * @param $slug
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function show($slug, EntityManagerInterface $entityManager): Response
    {

        $product = $entityManager->getRepository(Product::class)->findOneBy(['slug'=>$slug]);
        $products = $entityManager->getRepository(Product::class)->findBy(['isBest' => 1]);

        if(!$product){
            return $this->redirectToRoute('products');
        }

        return $this->render('product/show.html.twig', [
            'product'=> $product,
            'products'=>$products
        ]);
    }
}
