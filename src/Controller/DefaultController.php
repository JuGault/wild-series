<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

Class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index() : Response
    {
        $navCategories= $this->navbarCategory();
        return $this->render('Default/home.html.twig', [
            'website' => 'Bienvenue !',
            'nav_categories' => $navCategories
        ]);
    }
    public function navbarCategory(): array
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();
        if (!$categories) {
            throw $this->createNotFoundException(
                'No categories found in category\'s table.'
            );
        }
        return $categories;
    }
}