<?php

namespace App\Controller;

use App\Entity\Project;
use App\Enity\User;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class ProjectController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    #[Route('/project', name: 'project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        $projects= $projectRepository->findUserProjects($this->getUser()->getId());
        return $this->render('project/index.html.twig', [
            'projects' => $projects
        ]);
    }

    #[Route('/new', name: 'project_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setCreationDate(new \DateTime());
            $project->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            $this->addFlash(
                "success", 
                "Votre projet a bien été ouvert"
            );

            return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }


    #[Route('project/{id}', name: 'project_show', methods: ['GET', 'POST'])]
    public function show(Project $project, TaskRepository $taskRepository): Response
    {
        if(!empty($_POST) AND isset($_POST['finir'])){
            $task = $taskRepository->find($_POST['finir']);
            $task->setStatus("Terminé");
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash(
                "success", 
                "Votre tâche est maintenant terminé"
            );

            return $this->redirectToRoute('project_show', ["id" => $task->getProject()->getId()], Response::HTTP_SEE_OTHER);
        };

        if($project->getUser()==$this->getUser()){ 
            return $this->render('project/show.html.twig', [
            'project' => $project,
            ]);
        }
        else {
            return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
        }
    }




    #[Route('project/{id}/edit', name: 'project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                "success", 
                "Votre projet a bien été modifié"
            );

            return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('project/{id}/delete', name: 'project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($project);
            $entityManager->flush();

            $this->addFlash(
                "danger", 
                "Votre projet a bien été supprimé"
            );
        }

        return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
    }
}
