<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/wild", name="wild_")
 */
Class WildController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index() : Response
    {
        $navCategories= $this->navbarCategory();
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();
        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }
        return $this->render('wild/index.html.twig', [
            'programs' => $programs,
            'nav_categories' => $navCategories
        ]);
    }

    /**
     * @Route("/show/{slug}",
     *     requirements={"slug"="^[a-z0-9-]+$"},
     *     defaults={"slug"=1},
     *     name="show"
     * )
     * @param string $slug
     * @return Response
     */
    public function show(?string $slug): Response
    {
        $navCategories= $this->navbarCategory();
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
            'nav_categories' => $navCategories
        ]);
    }
    /**
     * @Route("/category/{categoryName}",
     *     name="show_category"
     * )
     * @param string $categoryName
     * @return Response
     */
    public function showByCategory(string $categoryName) : Response
    {
        $navCategories= $this->navbarCategory();
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
            'programs' => $programs,
            'nav_categories' => $navCategories
        ]);
    }
    /**
     * @Route("/program/{programName}",
     *     name="show_program"
     * )
     * @param string $programName
     * @return Response
     */
    public function showByProgram(string $programName) : Response
    {
        $navCategories= $this->navbarCategory();
        if (!$programName) {
            throw $this->createNotFoundException(
                'No program with '.$programName.' title, found in program\'s table.'
            );
        }

        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository(Program::class);

        $program = $repository->findOneByTitle( ucfirst(strtolower($programName)));
        $id = $program->getId();
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository(Season::class);
        $seasons = $repository->findBy(['program_id' =>$id]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No '.$programName.' , found in program\'s table.'
            );
        }
        return $this->render('wild/program.html.twig', [
            'programName' => $programName,
            'program' => $program,
            'seasons' => $seasons,
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