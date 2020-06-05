<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Pagination;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/users/{page<\d+>?1}", name="admin_user_index")
     */
    public function index(UserRepository $repo, $page, Pagination $pagination)
    {
        $pagination->setEntityClass(User::class)
        ->setPage($page);

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

        /**
     * permet de supprimer un profil utilisateur
     * 
     * @Route("/admin/users/{id}/delete", name="admin_user_delete")
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(User $user, EntityManagerInterface $manager) {
        $manager->remove($user);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le profil de <strong>{$user->getFullName()}</strong> a bien été supprimé"
        );

    return $this->redirectToRoute('admin_user_index');
    }
}
