<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

Class WildController extends AbstractController
{
    /**
     * @Route("/wild/", name="wild_index")
     * @return Response A response instance
     */
    public function index() : Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();
        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }
        return $this->render('wild/index.html.twig', [
            'programs' => $programs
        ]);
    }

    /**
     * @Route("/wild/show/{slug}",
     *     requirements={"slug"="^[a-z0-9-]+$"},
     *     defaults={"slug"=1},
     *     name="wild_show"
     * )
     * @param string $slug
     * @return Response
     */
    public function show(?string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
        ]);
    }
    /**
     * @Route("/wild/category/{categoryName}",
     *     name="show_category"
     * )
     * @param string $categoryName
     * @return Response
     */
    public function showByCategory(string $categoryName) : Response
    {
        if (!$categoryName) {
            throw $this->createNotFoundException(
                'No category with '.$categoryName.' title, found in category\'s table.'
            );
        }

        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository(Category::class);

        $category = $repository->findOneByName( ucfirst(strtolower($categoryName)));
        $id = $category->getId();
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository(Program::class);
        $programs = $repository->findByCategory(($id),
            array('id' => 'desc'),
            3, 0);

        if (!$programs) {
        throw $this->createNotFoundException(
            'No program with '.$categoryName.' category, found in program\'s table.'
        );
    }
        return $this->render(
            'wild/category.html.twig', [
            'categoryName' => $categoryName,
            'category' => $category,
            'programs' => $programs
        ]);
    }

}