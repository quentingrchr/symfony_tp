<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Category;
use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

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
     * @Route("post/new", name="app_newPost")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function newPost(Request $request, EntityManagerInterface $entityManager): Response
    {
        #$author = $request->request->get('author');
        #$category = $request->request->get('category');
        #$condition = $request->request->get('condition');
        #$price = $request->request->get('price');
        #$description = $request->request->get('description');
        #$image = $request->request->get('image');


        $user = $entityManager->getReference(User::class, 8);
        $category = $entityManager->getReference(Category::class, 1);
        $created_at = new DateTime();


        $post = (new Post())
            ->setAuthor($user)
            ->setCategory($category)
            ->setCondition("")
            ->setPrice(4)
            ->setTitle("Poster de batman")
            ->setCreatedAt($created_at)
            ->setDescription("Poster de batman 60x130. Très bon état.")
            ->setIsPublished(true)
            ->setImages([]);

        $entityManager->persist($post);
        $entityManager->flush();

        return new Response(sprintf('nouvelle annonce avec le titre : %s', $post->getTitle()));
    }

    /**
     * @Route("/post/{id}", name="app_post")
     * @return response
     */
    public function postPage(string $id, PostRepository $postRepository) :Response
    {
        $post = $postRepository->findOnePostById($id);
        return $this->render('Pages/post.html.twig', ["post" => $post]);


    }
}