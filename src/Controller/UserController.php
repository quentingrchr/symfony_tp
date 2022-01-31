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

class UserController extends AbstractController
{
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
        if($emailIsTaken) return $this->redirectToRoute('app_home_page');


        $user
            ->setName($params->get('name'))
            ->setPhone($params->get('phone'))
            ->setRoles($roles)
            ->setEmail($email)
            ->setPassword($password)
            ->setVotes(0);


        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_home_page');

    }

    /**
     * @Route("/profile", name="app_profile")
     * @param Request $request
     * @return Response
     */
    public function profile(Request $request): Response
    {

        return $this->render('Pages/profile.html.twig', ["user" => array(
            'email' => 'email@email.com',
            'password' => 'password',
            'phone' => '0618171714',
            'isAdmin' => true,
            'name'=> 'name'
        )]);
    }

    /**
     * @Route("/profile/{id}", name="app_update_user", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function updateProfile(Request $request, $id, User $user, EntityManagerInterface $em): Response
    {
        $loggedUser = $this->getUser();

        //dd($loggedUser->getId());


        if($id !== strval($loggedUser->getId()) ){
            return new JsonResponse('UnAuthorized', Response::HTTP_UNAUTHORIZED);
        }


        $user
            ->setEmail($request->request->get("email"))
            ->setName($request->request->get("name"))
            ->setPhone($request->request->get("phone"));

        if($request->request->get('is-admin')){
            $user->addRole('ROLE_ADMIN');
        } else {
            $user->removeRole('ROLE_ADMIN');
        }

        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('app_profile');
        //return $this->render("Pages/profile.html.twig", array(
        //    'flash' => 'Profile updated'
        //));

    }
}