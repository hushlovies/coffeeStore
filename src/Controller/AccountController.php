<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/account/{id}', name: 'account')]
    public function edit(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $user = $this->userRepository->find($id);
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setNom($form->get('nom')->getData());
            $user->setPrenom($form->get('prenom')->getData());
            $user->setAdress($form->get('adress')->getData());
            $user->setCp($form->get('cp')->getData());
            $user->setVille($form->get('ville')->getData());
            $user->setPays($form->get('pays')->getData());

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Your information has been modified'
            );
            return $this->redirectToRoute('app_home_index');
        }



        return $this->render('user/index.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}
