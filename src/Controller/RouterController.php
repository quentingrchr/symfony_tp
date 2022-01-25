<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @Route("/post/{id}", name="app_post")
     * @return response
     */
    public function postPage(Post $post) :Response
    {
        return $this->render('Pages/post.html.twig', ["post" => $post]);
    }

    /**
     * @Route("/post/{id}/vote", name="app_post_vote", methods={"POST"})
     *
     */
    public function postVote(Post $post, Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        $vote = $request->request->get('vote');
        $user = $post->getAuthor();
        if ($vote === "up") {
            $user->upVotes();
        } else {
            $user->downVotes();
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_post', ["id" => $post->getId()]);
    }
}