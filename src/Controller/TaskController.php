<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{

    #[Route('/tasks', name: 'task_list')]
    public function listAllAction(EntityManagerInterface $em, TaskRepository $taskRepository, Request $request)
    {
        $user = $this->getUser();
        $tasks = $taskRepository->findAll();

        $uri = $request->getUri();

        return $this->render('task/list.html.twig', [
            'tasks' => $tasks,
            'uri' => $uri,
        ]);
    }

    #[Route('/tasks/done', name: 'task_list_done')]
    public function listAction(EntityManagerInterface $em, TaskRepository $taskRepository, Request $request)
    {
        $user = $this->getUser();
        $tasks = $taskRepository->findBy(['isDone' => true]);

        $uri = $request->getUri();

        return $this->render('task/list.html.twig', [
            'tasks' => $tasks,
            'uri' => $uri,
        ]);
    }

    #[Route('/tasks/create', name: 'task_create')]
    public function createAction(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($user);

            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/tasks/edit/{id}', name: 'task_edit')]
    #[IsGranted('TASK_MANAGE', 'task', 'Access denied')]
    public function editAction(Task $task, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/toggle/{id}', name: 'task_toggle')]
    public function toggleTaskAction(Task $task, EntityManagerInterface $em)
    {
        $task->toggle(!$task->isDone());
        $em->flush();

        $this->addFlash('success', sprintf('Le status de la tâche %s a bien été changé.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/delete/{id}', name: 'task_delete')]
    #[IsGranted('TASK_DELETE', 'task', 'Access denied')]
    public function deleteTaskAction(Task $task, EntityManagerInterface $em)
    {
        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
