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
use DateTime;

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
        $user = $entityManager->getReference(User::class, $this->getUser()->getId());
        $userRole = $user->getRoles();
        $title = $request->request->get('title');
        $categoryId = $request->request->get('category');
        $price = $request->request->get('price');
        $condition = $request->request->get('condition');
        $description = $request->request->get('description');
        $files = $request->files->all();
        $publication = $request->request->get('publication');


        if(in_array("ROLE_ADMIN", $userRole)) {

            if ($title && $categoryId && $categoryId !== '0' && $price && $condition && $description && $publication) {

                /* Category */
                $intCategoryId = intval($categoryId);
                $category = $entityManager->getReference(Category::class, $intCategoryId);

                /* Date */
                $created_at = new DateTime();

                /* Price */
                $floatPrice = floatval($price);

                /* Publication */
                $boolPublication = false;

                if ($publication === "on") {
                    $boolPublication = true;
                }

                /* Images */
                $images = [];

                foreach ($files as $file) {
                    if ($file !== null ) {

                        if($file->getError() === 0) {

                            $fileName = uniqid() . '-' . $file->getClientOriginalName();
                            $localPathname = 'uploads/' . $fileName;
                            $from = $file->getPathname();
                            $to = $request->server->get('DOCUMENT_ROOT') . $localPathname;

                            move_uploaded_file($from, $to);
                            $images[] = $localPathname;
                        } else {
                            dd("L'un de vos fichiers n'est pas valide.");
                        }
                    }
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
                    ->setImages($images);

                $entityManager->persist($post);
                $entityManager->flush();
                $postId = $post->getId();

                return $this->redirectToRoute('app_post', [
                    'id' => $postId
                ]);
            } else {
                dd('Il manque des informations à votre annonce.');
            }
        } else {
            dd('Vous n\'êtes pas autorisé à poster une annonce.');
        }
    }


    /**
     * @Route("/post/{id}", name="app_post", methods={"GET"})
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
     * @Route("/post/delete/{id}", name="app_post_delete", methods={"GET"})
     * @param Post $post
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
    public function postDelete(Post $post, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $loggedUser = $this->getUser();
        $isAuthor = $this->getUser()->getId() == $post->getAuthor()->getId();
        $isAdmin = in_array('ROLE_ADMIN',$loggedUser->getRoles());
        if($isAdmin || $isAuthor){
            $em->remove($post);
            $em->flush();
            return $this->redirect($this->generateUrl('app_home_page'));

        } else {
            return $this->redirect($this->generateUrl('app_home_page'));
        }

    }

    /**
     * @Route("/post/changeStatus/{id}", name="app_post_status", methods={"POST"})
     * @param Post $post
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     * @param string $id
     */
    public function changePostStatus(Post $post, Request $request, EntityManagerInterface $em, string $id): RedirectResponse
    {

        $newStatus = $request->request->get('status');
        $redirectRoute = $request->request->get('redirectTo');
        $redirectId = $request->request->get('redirectID');

        if($newStatus === 'published'){
            $post->setIsPublished(true);
        } else if($newStatus === "onDraft") {
            $post->setIsPublished(false);
        }

        $em->flush();

        if($redirectId) {
            return $this->redirect($this->generateUrl($redirectRoute, ["id" => $redirectId]));
        } else {
            return $this->redirect($this->generateUrl($redirectRoute, ["id" => $redirectId]));
        }

    }
}
