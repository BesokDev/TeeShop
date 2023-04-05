<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/admin/ajouter-un-produit', name: "create_product", methods: ['GET', 'POST'])]
    public function createProduct(Request $request, ProductRepository $repository): Response
    {
        $product = new Product();
    }
}