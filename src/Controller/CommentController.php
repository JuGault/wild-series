<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/", name="comment_index", methods={"GET"})
     * @param CommentRepository $commentRepository
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(CommentRepository $commentRepository): Response
    {
        $navCategories= $this->navbarCategory();
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/new", name="comment_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $navCategories= $this->navbarCategory();
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('comment_index');
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/{id}", name="comment_show", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param Comment $comment
     * @return Response
     */
    public function show(Comment $comment): Response
    {
        $navCategories= $this->navbarCategory();
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="comment_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param Comment $comment
     * @return Response
     */
    public function edit(Request $request, Comment $comment): Response
    {
        $navCategories= $this->navbarCategory();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comment_index');
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
            'nav_categories' => $navCategories,
        ]);
    }

    /**
     * @Route("/{id}", name="comment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('comment_index');
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
