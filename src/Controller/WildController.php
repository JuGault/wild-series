<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Location;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/wild", name="wild_")
 */
Class WildController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return Response A response instance
     */
    public function index(Request $request) : Response
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
            'nav_categories' => $navCategories,

        ]);
    }
    /**
     * @Route("/actors", name="index_actor")
     * @param Request $request
     * @return Response A response instance
     */
    public function indexActor(Request $request) : Response
    {
        $navCategories= $this->navbarCategory();
        $actors = $this->getDoctrine()
            ->getRepository(Actor::class)
            ->findAll();
        if (!$actors) {
            throw $this->createNotFoundException(
                'No actors found in actor\'s table.'
            );
        }


        return $this->render('wild/index_actor.html.twig', [
            'actors' => $actors,
            'nav_categories' => $navCategories,

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

        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository(Program::class);

        $program = $repository->findOneByTitle( ucfirst(strtolower($programName)));
        $id = $program->getId();
        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository(Season::class);
        $seasons = $repository->findBy(['program' =>$id]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No '.$programName.' , found in program\'s table.'
            );
        }
        if (!$seasons) {
            throw $this->createNotFoundException(
                'No season with '.$seasons.' number, found in season\'s table.'
            );
        }
        return $this->render('wild/program.html.twig', [
            'programName' => $programName,
            'program' => $program,
            'seasons' => $seasons,
            'nav_categories' => $navCategories
        ]);
    }

    /**
     * @Route("/season/{programName}/{seasonId}",
     *     name="show_season"
     * )
     * @param string $programName
     * @param int $seasonId
     * @return Response
     */
    public function showBySeason(string $programName, int $seasonId) : Response
    {
        $navCategories= $this->navbarCategory();

        $repository = $this->getDoctrine()
            ->getManager()
            ->getRepository(Season::class);

        $season = $repository->findOneBy( ['id' => $seasonId] );
        $program = $season->getProgram();
        $episodes = $season->getEpisodes();
        if (!$season) {
            throw $this->createNotFoundException(
                'No season with '.$season.' number, found in season\'s table.'
            );
        }
        if (!$program) {
            throw $this->createNotFoundException(
                'No '.$programName.' , found in program\'s table.'
            );
        }
        return $this->render('wild/season.html.twig', [
            'programName' => $programName,
            'program' => $program,
            'season' => $season,
            'episodes'=> $episodes,
            'nav_categories' => $navCategories
        ]);
    }

    /**
     * @Route("/episode/{id}",
     *     name="show_episode"
     * )
     * @param Episode $episode
     * @return Response
     */
    public function showEpisode(Episode $episode) : Response
    {
        $navCategories= $this->navbarCategory();
       $season = $episode->getSeason();
       $program = $season->getProgram();
        return $this->render('wild/episode.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode'=> $episode,
            'nav_categories' => $navCategories
        ]);
    }

    /**
     * @Route("/actor/{id}",
     *     name="show_actor"
     * )
     * @param Actor $actor
     * @return Response
     */
    public function showActor(Actor $actor) : Response
    {
        $navCategories= $this->navbarCategory();
        return $this->render('wild/actor.html.twig', [
            'actor'=> $actor,
            'nav_categories' => $navCategories
        ]);
    }
    /**
     * @Route("/category_add/", name="category_add")
     * @param Request $request
     * @return Response A response instance
     */
    public function categoryAdd( Request $request) : Response
    {
        $navCategories= $this->navbarCategory();
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('wild_index');
        }

        return $this->render('wild/category_add.html.twig', [
            'nav_categories' => $navCategories,
            'category' => $category,
            'form' => $form->createView(),
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