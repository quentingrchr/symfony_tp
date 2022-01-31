<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Question;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    /**
     * @Route ("question/{id}/add", name="app_question_add", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param string $id
     * @return RedirectResponse
     */
    public function addQuestion(Request $request, EntityManagerInterface $entityManager, string $id)
    {
        $userId = 45;
        $postId= $id;
        $content = $request->request->get('content');

        /**
         * todo: check if user Id (JWT)
         */

        if($content) {
            $user = $entityManager->getReference(User::class, $userId);
            $post = $entityManager->getReference(Post::class, $postId);
            $question = (new Question())
                ->setContent($content)
                ->setAuthor($user)
                ->setPost($post);
            $entityManager->persist($question);
            $entityManager->flush();

        }

        return $this->redirectToRoute('app_post', [
            'id' => $postId
        ]);
    }
}
