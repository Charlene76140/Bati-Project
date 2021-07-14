<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
#[Route('/task')]
class TaskController extends AbstractController
{
    // #[Route('/', name: 'task_index', methods: ['GET'])]
    // public function index(TaskRepository $taskRepository): Response
    // {
    //     return $this->render('task/index.html.twig', [
    //         'tasks' => $taskRepository->findAll(),
    //     ]);
    // }

    #[Route('/new/{id}', name: 'task_new', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function new(int $id=1, ProjectRepository $projectRepository, Request $request): Response
    {
        $project = $projectRepository->find($id);

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setCreationDate(new \DateTime());
            $task->setStatus("en cours");
            $task->setProject($project);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash(
                "success", 
                "Nouvelle tâche enregistrée"
            );

            return $this->redirectToRoute('project_show', ["id" => $task->getProject()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    // #[Route('/{id}', name: 'task_show', methods: ['GET'])]
    // public function show(Task $task): Response
    // {
    //     return $this->render('task/show.html.twig', [
    //         'task' => $task,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('task_index', [], Response::HTTP_SEE_OTHER);
    }
}
