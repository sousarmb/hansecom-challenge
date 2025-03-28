<?php

namespace App\Controller;

use App\Form\UserEditType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UserController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator
    ) {}

    #[Route('/user', name: 'app_user', methods:['GET'])]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'user_form' => $this->createForm(
                UserEditType::class,
                ['email' => $this->getUser()->getEmail()]
            ),
        ]);
    }

    #[Route('/user', name: 'update_user', methods:['POST'])]
    public function updateUser(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response|RedirectResponse {
        $user = $this->getUser();
        $form = $this->createForm(UserEditType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $userPasswordHasher->hashPassword(
                $user,
                $form->getData()['new_password']
            );
            $user->setPassword($newPassword);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', $this->translator->trans('user_password_changed'));

            return $this->redirect('/user');
        }

        return $this->render(
            'user/index.html.twig', 
            ['user_form' => $form->createView()],
            new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
