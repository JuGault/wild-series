<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Season;
use App\Form\EpisodeType;
use App\Repository\EpisodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/episode")
 */
class EpisodeController extends AbstractController
{
    /**
     * @Route("/", name="episode_index", methods={"GET"})
     * @param EpisodeRepository $episodeRepository
     * @return Response
     */
    public function index(EpisodeRepository $episodeRepository): Response
    {
        $navCategories= $this->navbarCategory();
        return $this->render('episode/index.html.twig', [
            'episodes' => $episodeRepository->findBy([],['season'=>'ASC', 'number'=> 'ASC']),
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/new", name="episode_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $navCategories= $this->navbarCategory();
        $episode = new Episode();
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($episode);
            $entityManager->flush();

            return $this->redirectToRoute('episode_index');
        }

        return $this->render('episode/new.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/{id}", name="episode_show", methods={"GET"})
     * @param Episode $episode
     * @return Response
     */
    public function show(Episode $episode): Response
    {
        $navCategories= $this->navbarCategory();
        return $this->render('episode/show.html.twig', [
            'episode' => $episode,
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="episode_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Episode $episode
     * @return Response
     */
    public function edit(Request $request, Episode $episode): Response
    {
        $navCategories= $this->navbarCategory();
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('episode_index');
        }
        return $this->render('episode/edit.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/{id}", name="episode_delete", methods={"DELETE"})
     * @param Request $request
     * @param Episode $episode
     * @return Response
     */
    public function delete(Request $request, Episode $episode): Response
    {
        if ($this->isCsrfTokenValid('delete'.$episode->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($episode);
            $entityManager->flush();
        }

        return $this->redirectToRoute('episode_index');
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
    public function  getSeasonName(Episode $episode)
    {
        return (($episode -> getSeason()) -> getProgram()) -> getTitle();
    }
}
