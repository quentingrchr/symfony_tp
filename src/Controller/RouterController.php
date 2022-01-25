<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RouterController extends AbstractController
{
    /**
     * @Route("/", name="app_homePage")
     * @return Response
     */
    public function homePage(PostRepository $postRepository) :Response
    {
        $posts = $postRepository->findAllPostsOrderedByNewest();
        return $this->render('Pages/home.html.twig', ["posts" => $posts]);
    }

    /**
     * @Route("/sign-up", name="app_signUp")
     * @return Response
     */
    public function signUpPage() :Response
    {
        return $this->render('Pages/sign-up.html.twig');
    }

    /**
     * @Route("/user", name="app_newUser", methods={"POST"})
     * @return Response
     */
    public function createUser(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository) :Response
    {

        $params = $request->request;
        $password = $params->get('password');
        $email = $params->get('email');
        $emailIsTaken = $userRepository->findOneByEmail($email);
        /* todo add error notification */
        if($emailIsTaken) return $this->redirectToRoute('app_homePage');

        $user = (new User())
            ->setName($params->get('name'))
            ->setPhone($params->get('phone'))
            ->setIsAdmin($params->get('is-admin'))
            ->setEmail($email)
            ->setPassword(password_hash($password,PASSWORD_BCRYPT))
            ->setVotes(0);


        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_homePage');

    }


    /**
     * @Route("/post/{id}", name="app_post")
     * @return response
     */
    public function postPage(Post $post) :Response
    {

        return $this->render('Pages/post.html.twig', ["post" => $post]);

    }
}