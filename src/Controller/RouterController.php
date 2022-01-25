<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/post/{id}", name="app_post")
     * @return response
     */
    public function postPage(string $id) :Response
    {
        return $this->render('Pages/post/html.twig', ["id" => $id]);
    }
}