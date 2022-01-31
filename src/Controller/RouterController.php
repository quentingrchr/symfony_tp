<?php

namespace App\Controller;


use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Post;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use PhpParser\Node\Stmt\Return_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

class RouterController extends AbstractController
{
    /**
     * @Route("/", name="app_home_page")
     * @param PostRepository $postRepository
     * @return Response
     */
    public function homePage(Request $req, PostRepository $postRepository, CategoryRepository $categoryRepository) :Response
    {
        $categories = $categoryRepository->findAll();
        $queryParam = $req->query;
        $filterCategory = $queryParam->get('category');
        $posts  = [];



        if($filterCategory ) {
            $posts = $postRepository->findManyByCategory($filterCategory);

        } else {
            // No filter
            $posts = $postRepository->findAllPostsOrderedByNewest();
        }
        return $this->render('Pages/home.html.twig', [
            "categories" => $categories,
            "filterCategory" => $filterCategory,
            "posts" => $posts
        ]);
    }

    /**
     * @Route("/sign-up", name="app_signup", methods={"GET"})
     *
     * @return Response
     */
    public function signUpPage() :Response
    {
        return $this->render('Pages/sign-up.html.twig');
    }

}