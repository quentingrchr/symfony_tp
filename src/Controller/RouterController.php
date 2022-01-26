<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Category;
use App\Entity\Post;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

class RouterController extends AbstractController
{
    /**
     * @Route("/", name="app_homePage")
     * @param PostRepository $postRepository
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
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
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
     * @Route("new-post", name="app_newPost")
     * @return Response
     */
    public function newPost(CategoryRepository $categoryRepository) :Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('Pages/newPost.html.twig', ["categories" => $categories]);
    }

    /**
     * @Route("new-post/add", name="app_addPost")
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
            $user = $entityManager->getReference(User::class, 8);

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
     * @Route("/post/{id}", name="app_post")
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
     * @Route("/me", name="app_editUser")
     * @param Request $request
     * @return Response
     */
    public function editUser(Request $request): Response
    {

        return $this->render('Pages/edit-user.html.twig', ["user" => array(
            'email' => 'email@email.com',
            'password' => 'password',
            'phone' => '0618171714',
            'isAdmin' => true,
            'name'=> 'name'
        )]);
    }
}