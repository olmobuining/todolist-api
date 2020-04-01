<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Task;
use App\Form\CategoryTaskType;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class CategoryController extends BaseController
{
    /**
     * @Rest\Get("/category")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repository->findall();

        return $this->response($categories);
    }

    /**
     * @Rest\Post("/category")
     */
    public function postAction(Request $request)
    {
        $category = new Category();

        return $this->patchAndPost($request, $category);
    }

    /**
     * @Rest\Patch("/category/{id}")
     */
    public function patchAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $category = $repository->find($id);
        if (!$category) {
            return $this->notFound('Category not found');
        }

        return $this->patchAndPost($request, $category);
    }

    /**
     * @Rest\Get("/category/{id}/task")
     */
    public function getTasksAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(Task::class);
        $tasks = $repository->findAllByCategoryId($id);

        return $this->response($tasks);
    }

    /**
     * @Rest\Post("/category/{id}/task/{task_id}")
     */
    public function postTasksAction(Request $request, $id, $task_id)
    {
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $category = $repository->find($id);
        if (!$category) {
            return $this->notFound('Category not found');
        }

        $repository = $this->getDoctrine()->getRepository(Task::class);
        $task = $repository->find($task_id);
        if (!$task) {
            return $this->notFound('Task not found');
        }
        $category->addTask($task);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->response(['success' => true]);
    }

    /**
     * @Rest\Delete("/category/{id}/task/{task_id}")
     */
    public function deleteTasksAction(Request $request, $id, $task_id)
    {
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $category = $repository->find($id);
        if (!$category) {
            return $this->notFound('Category not found');
        }

        $repository = $this->getDoctrine()->getRepository(Task::class);
        $task = $repository->find($task_id);
        if (!$task) {
            return $this->notFound('Task not found');
        }
        $category->removeTask($task);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->response(['success' => true]);
    }


    /**
     * @Rest\Delete("/category/{id}")
     */
    public function deleteAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $category = $repository->find($id);
        if (!$category) {
            return $this->notFound('Category not found');
        }


        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        return $this->response(['success' => true]);
    }

    private function patchAndPost(Request $request, Category $category)
    {
        $form = $this->createForm(CategoryType::class, $category);
        $data = json_decode($request->getContent(), true);

        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->response($category);
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
