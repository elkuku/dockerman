<?php

namespace App\Controller;

use App\Dto\ContainerOptions;
use App\Service\DockerService;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/docker')]
class Docker extends BaseController
{
    #[Route('/containers', name: 'app_docker_containers', methods: ['GET', 'POST'])]
    public function containers(Request $request, DockerService $dockerService): Response
    {
        $options = new ContainerOptions();
        $form = $this->createFormBuilder($options)
            ->add('all', CheckboxType::class, ['required' => false])
            ->add('refresh', SubmitType::class, ['label' => 'Refresh'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $task = $form->getData();

            // ... perform some action, such as saving the task to the database

            // return $this->redirectToRoute('task_success');
        }

        $command = [
            'docker',
            'container',
            'ls',
            '--format=json'
        ];

        if ($options->all) {
            $command[] = '-a';
        }

        $process = new Process($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        return $this->render('docker/containers.html.twig', [
            'containers' => $dockerService->decodeGoJson($process->getOutput()),
            'form' => $form,
        ]);
    }

    #[Route('/images', name: 'app_docker_images', methods: ['GET'])]
    public function images(DockerService $docker): Response
    {
        $process = new Process(['docker', 'image', 'ls']);
        $process2 = new Process(['docker', 'image', 'ls', '--format=json']);
        $process->run();
        $process2->run();

        return $this->render('docker/images.html.twig', [
            'output' => $process->getOutput(),
            'output2' => $process2->getOutput(),
            'images' => $docker->decodeGoJson($process2->getOutput())
        ]);
    }

    #[Route('/volumes', name: 'app_docker_volumes', methods: ['GET'])]
    public function volumes(): Response
    {
        return $this->render('docker/volumes.html.twig');
    }
}