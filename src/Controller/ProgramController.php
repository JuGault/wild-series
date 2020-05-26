<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use App\Form\ProgramType;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/program")
 */
class ProgramController extends AbstractController
{
    /**
     * @Route("/", name="program_index", methods={"GET"})
     * @param ProgramRepository $programRepository
     * @return Response
     */
    public function index(ProgramRepository $programRepository): Response
    {
        $navCategories= $this->navbarCategory();
        return $this->render('program/index.html.twig', [
            'programs' => $programRepository->findAll(),
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/new", name="program_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $navCategories= $this->navbarCategory();
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($program);
            $entityManager->flush();

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/new.html.twig', [
            'program' => $program,
            'form' => $form->createView(),
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/{id}", name="program_show", methods={"GET"})
     * @param Program $program
     * @return Response
     */
    public function show(Program $program): Response
    {
        $navCategories= $this->navbarCategory();
        return $this->render('program/show.html.twig', [
            'program' => $program,
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="program_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Program $program
     * @return Response
     */
    public function edit(Request $request, Program $program): Response
    {
        $navCategories= $this->navbarCategory();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/edit.html.twig', [
            'program' => $program,
            'form' => $form->createView(),
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/{id}", name="program_delete", methods={"DELETE"})
     * @param Request $request
     * @param Program $program
     * @return Response
     */
    public function delete(Request $request, Program $program): Response
    {
        $navCategories= $this->navbarCategory();
        if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($program);
            $entityManager->flush();
        }

        return $this->redirectToRoute('program_index');
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
