<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{

    /**
     * @Route("/post/new", name="app_new_post", methods={"GET"})
     * @return Response
     */
    public function newPost(CategoryRepository $categoryRepository) :Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('Pages/newPost.html.twig', ["categories" => $categories]);
    }

    /**
     * @Route("post/new", name="app_add_post", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function addPost(Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        $title = $request->request->get('title');
        $categoryId = $request->request->get('category');
        $price = $request->request->get('price');
        $condition = $request->request->get('condition');
        $description = $request->request->get('description');
        $image = $request->request->get('image');
        $publication = $request->request->get('publication');


        if (!$title || !$categoryId || $categoryId === '0' || !$price || !$condition || !$description || !$publication)
        {
            dd($request->request);
        }
        else
        {
            $user = $entityManager->getReference(User::class, $this->getUser()->getId());

            $intCategoryId = intval($categoryId);
            $category = $entityManager->getReference(Category::class, $intCategoryId);

            $created_at = new DateTime();

            $floatPrice = floatval($price);

            if ($publication === "on")
            {
                $boolPublication = true;
            }
            else
            {
                $boolPublication = false;
            }

            $post = (new Post())
                ->setAuthor($user)
                ->setCategory($category)
                ->setCondition($condition)
                ->setPrice($floatPrice)
                ->setTitle($title)
                ->setCreatedAt($created_at)
                ->setDescription($description)
                ->setIsPublished($boolPublication)
                ->setImages($image);

            $entityManager->persist($post);
            $entityManager->flush();
            $postId = $post->getId();

            return $this->redirectToRoute('app_post', [
                'id' => $postId
            ]);
        }
    }


    /**
     * @Route("/post/{id}", name="app_post" methods="{GET}")
     * @param string $id
     * @param PostRepository $postRepository
     * @return Response
     */
    public function postPage(string $id, PostRepository $postRepository) :Response
    {

        $post = $postRepository->findOnePostById($id);
        return $this->render('Pages/post.html.twig', ["post" => $post]);
    }

    /**
     * @Route("/post/{id}/vote", name="app_post_vote", methods={"POST"})
     * @param Post $post
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
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

    /**
     * @Route("/post", name="app_post_delete", methods={"DELETE"})
     * @param Post $post
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function postDelete(Post $post, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        /* todo implements delete
        DELETE methods are not allowed from browsers
        can't use a form
        can't use a link
        */
        $loggedUser = $this->getUser();
        dd($post);
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($post);
        $em->flush();

        dd($post);

    }




}
