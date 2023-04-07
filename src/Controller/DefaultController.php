<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name:'show_home', methods:['GET'])]
    public function showHome(ProductRepository $repository): Response
    {
        $products = $repository->findBy(['deletedAt' => null]);

        return $this->render('default/show_home.html.twig', [
            'products' => $products
        ]);
    }
} // end class
