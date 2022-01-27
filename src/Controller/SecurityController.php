<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route ("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('Pages/login.html.twig',
            [
                'controller_name' => 'SecurityController',
                'last_username' => $lastUsername,
                'error'         => $error,
            ]

        );
    }



    /**
     * @Route("/sign_up", name="app_newUser", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @return Response
     */
    public function signUp(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordHasherInterface $hasher) :Response
    {

        $params = $request->request;
        $user = new User;
        $password = $hasher->hashPassword($user, $params->get('password'));
        $email = $params->get('email');
        $emailIsTaken = $userRepository->findOneByEmail($email);
        $roles = [];
        if($params->get('is-admin')){
            $roles[] = 'ADMIN';
        }
        /* todo add error notification */
        if($emailIsTaken) return $this->redirectToRoute('app_homePage');


        $user
            ->setName($params->get('name'))
            ->setPhone($params->get('phone'))
            ->setRoles($roles)
            ->setEmail($email)
            ->setPassword($password)
            ->setVotes(0);


        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_homePage');

    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {

    }

}