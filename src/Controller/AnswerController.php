<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnswerController extends AbstractController
{
    /**
     * @Route ("anwser/{id}/add", name="app_answer_add", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param string $id
     * @return RedirectResponse
     */
    public function addAnwser(Request $request, EntityManager $entityManager, string $id): RedirectResponse
    {
        $content = $request->request->get('content');
        $postId = $request->request->get('postId');
        $questionId= $id;

        if($content) {
            $question = $entityManager->getReference(Question::class, $questionId);
            $answer = (new Answer())->setContent($content)->setQuestion($question);
            $entityManager->persist($answer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post', [
            'id' => $postId
        ]);
    }
}
