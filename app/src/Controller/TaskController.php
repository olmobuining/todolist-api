<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class TaskController extends BaseController
{
    /**
     * @Rest\Get("/task")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Task::class);
        $tasks = $repository->findall();

        return $this->response($tasks, ['Default', 'Categories']);
    }

    /**
     * @Rest\Post("/task")
     */
    public function postAction(Request $request)
    {
        $task = new Task();

        return $this->patchAndPost($request, $task);
    }

    /**
     * @Rest\Patch("/task/{id}")
     */
    public function patchAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(Task::class);
        $task = $repository->find($id);
        if (!$task) {
            return $this->notFound('Task not found');
        }

        return $this->patchAndPost($request, $task);
    }

    /**
     * @Rest\Delete("/task/{id}")
     */
    public function deleteAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(Task::class);
        $task = $repository->find($id);
        if (!$task) {
            return $this->notFound('Task not found');
        }


        $em = $this->getDoctrine()->getManager();
        $em->remove($task);
        $em->flush();

        return $this->response(['success' => true]);
    }

    private function patchAndPost(Request $request, Task $task)
    {
        $form = $this->createForm(TaskType::class, $task);
        $data = json_decode($request->getContent(), true);

        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->response($task);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = [
                'field' => $error->getOrigin()->getName(),
                'message' => $error->getMessage(),
            ];
        }

        return $this->badRequest($errors);
    }
}
