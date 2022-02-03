<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
            $roles[] = 'ROLE_ADMIN';
        }

        if($emailIsTaken) {
            $this->addFlash('error', 'This mail is already associated to an account');
            return $this->redirectToRoute('app_signup');
        };


        $user
            ->setName($params->get('name'))
            ->setPhone($params->get('phone'))
            ->setRoles($roles)
            ->setEmail($email)
            ->setPassword($password)
            ->setVotes(0);


        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('info', 'Welcome ' . $user->getName() . "! You can now login:)");
        return $this->redirectToRoute('app_login');

    }

    /**
     * @Route("/profile", name="app_profile")
     * @param Request $request
     * @return Response
     */
    public function profile(Request $request): Response
    {

        return $this->render('Pages/profile.html.twig');
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

    /**
     * @Route("/user/vote/{id}", name="app_user_vote", methods={"POST"})
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function userChangeRate(User $user, Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        $vote = $request->request->get('vote');
        $redirectRoute = $request->request->get('redirectRoute');
        $redirectId = $request->request->get('redirectID');

        if ($vote === "up") {
            $user->upVotes();
        } else {
            $user->downVotes();
        }
        $entityManager->flush();

        if($redirectId) {
            return $this->redirectToRoute($redirectRoute, ["id" => $redirectId]);
        } else {
            return $this->redirectToRoute($redirectRoute);
        }
    }
}
