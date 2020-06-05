<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Service\Pagination;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comment_index")
     */
    public function index(CommentRepository $repo, $page, Pagination $pagination)
    {
        $pagination->setEntityClass(Comment::class)
                    ->setPage($page)
                    ->setLimit(5);
        
        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * permet de modifier un commentaire
     * 
     * @Route("/admin/comments/{id}/edit", name="admin_comment_edit")
     *
     * @return Response
     */
    public function edit(Comment $comment, Request $request, EntityManagerInterface $manager) {

        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$comment->getId()}</strong> a bien été modifié"
            );

            return $this->redirectToRoute("admin_comment_index");
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * permet de supprimer une annonce
     * 
     * @Route("/admin/comments/{id}/delete", name="admin_comment_delete")
     *
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Comment $comment, EntityManagerInterface $manager) {
            $manager->remove($comment);
            $manager->flush();
    
            $this->addFlash(
                'success',
                "Le commentaire de <strong>{$comment->getAuthor()->getFullName()}</strong> a bien été supprimé"
            );

        return $this->redirectToRoute('admin_comment_index');
    }
}
