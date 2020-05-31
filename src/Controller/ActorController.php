<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Category;
use App\Form\ActorType;
use App\Repository\ActorRepository;
use App\Service\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/actor")
 */
class ActorController extends AbstractController
{
    /**
     * @Route("/", name="actor_index", methods={"GET"})
     * @param ActorRepository $actorRepository
     * @return Response
     */
    public function index(ActorRepository $actorRepository): Response
    {
        $navCategories= $this->navbarCategory();
        return $this->render('actor/index.html.twig', [
            'actors' => $actorRepository->findAll(),
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/new", name="actor_new", methods={"GET","POST"})
     * @param Request $request
     * @param Slugify $slugify
     * @return Response
     */
    public function new(Request $request, Slugify $slugify): Response
    {
        $navCategories= $this->navbarCategory();
        $actor = new Actor();
        $form = $this->createForm(ActorType::class, $actor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $slug = $slugify->generate($actor->getName());
            $actor->setSlug($slug);
            $entityManager->persist($actor);
            $entityManager->flush();

            return $this->redirectToRoute('actor_index');
        }

        return $this->render('actor/new.html.twig', [
            'actor' => $actor,
            'form' => $form->createView(),
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/{slug}", name="actor_show", methods={"GET"})
     * @param Actor $actor
     * @return Response
     */
    public function show(Actor $actor): Response
    {
        $navCategories= $this->navbarCategory();
        return $this->render('actor/show.html.twig', [
            'actor' => $actor,
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="actor_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Actor $actor
     * @param Slugify $slugify
     * @return Response
     */
    public function edit(Request $request, Actor $actor, Slugify $slugify): Response
    {
        $navCategories= $this->navbarCategory();
        $form = $this->createForm(ActorType::class, $actor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugify->generate($actor->getName());
            $actor->setSlug($slug);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('actor_index');
        }

        return $this->render('actor/edit.html.twig', [
            'actor' => $actor,
            'form' => $form->createView(),
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/{slug}", name="actor_delete", methods={"DELETE"})
     * @param Request $request
     * @param Actor $actor
     * @return Response
     */
    public function delete(Request $request, Actor $actor): Response
    {
        if ($this->isCsrfTokenValid('delete'.$actor->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($actor);
            $entityManager->flush();
        }

        return $this->redirectToRoute('actor_index');
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
