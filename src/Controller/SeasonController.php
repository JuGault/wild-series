<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/season")
 */
class SeasonController extends AbstractController
{
    /**
     * @Route("/", name="season_index", methods={"GET"})
     * @param SeasonRepository $seasonRepository
     * @return Response
     */
    public function index(SeasonRepository $seasonRepository): Response
    {
        $navCategories= $this->navbarCategory();
        return $this->render('season/index.html.twig', [
            'seasons' => $seasonRepository->findBy([],['program'=>'ASC']),
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/new", name="season_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $navCategories= $this->navbarCategory();
        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($season);
            $entityManager->flush();
            $this->addFlash('success', 'La saison a bien été crée');
            return $this->redirectToRoute('season_index');
        }

        return $this->render('season/new.html.twig', [
            'season' => $season,
            'form' => $form->createView(),
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/{id}", name="season_show", methods={"GET"})
     * @param Season $season
     * @return Response
     */
    public function show(Season $season): Response
    {
        $navCategories= $this->navbarCategory();
        return $this->render('season/show.html.twig', [
            'season' => $season,
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="season_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Season $season
     * @return Response
     */
    public function edit(Request $request, Season $season): Response
    {
        $navCategories= $this->navbarCategory();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'La saison a bien été mise a jour');
            return $this->redirectToRoute('season_index');
        }

        return $this->render('season/edit.html.twig', [
            'season' => $season,
            'form' => $form->createView(),
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/{id}", name="season_delete", methods={"DELETE"})
     * @param Request $request
     * @param Season $season
     * @return Response
     */
    public function delete(Request $request, Season $season): Response
    {
        if ($this->isCsrfTokenValid('delete'.$season->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($season);
            $entityManager->flush();
        }
        $this->addFlash('danger', 'La saison a bien été supprimée');
        return $this->redirectToRoute('season_index');
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
