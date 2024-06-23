<?php

namespace App\Controller;

use App\Dto\ContainerOptions;
use App\Service\DockerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/containers')]
class Containers extends AbstractController
{
    #[Route('/', name: 'app_docker_containers_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DockerService $dockerService): Response
    {
        $options = new ContainerOptions();
        $hasFilters = false;

        $form = $this->createFormBuilder($options)
            ->add('onlyRunning', CheckboxType::class, ['required' => false])
            ->add('filterName', TextType::class, [
                'label' => 'Name',
                'required' => false,
                'attr' => ['placeholder' => 'Name'],
                'row_attr' => ['class' => 'form-floating'],
            ])
            ->add('filterAncestor', TextType::class, [
                'label' => 'Ancestor',
                'required' => false,
                'attr' => ['placeholder' => 'Ancestor'],
                'row_attr' => ['class' => 'form-floating'],
            ])
            ->add('refresh', SubmitType::class, ['label' => 'Refresh'])
            ->getForm();

        $form->handleRequest($request);

        $command = [
            'docker',
            'container',
            'ls',
            '--format=json'
        ];

        if ($options->filterName) {
            $command[] = '--filter';
            $command[] = 'name=' . $options->filterName;
            $hasFilters = true;
        }

        if ($options->filterAncestor) {
            $command[] = '--filter';
            $command[] = 'ancestor=' . $options->filterAncestor;
            $hasFilters = true;
        }

        if ($options->onlyRunning) {
            $hasFilters = true;
        } else {
            $command[] = '-a';
        }

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->addFlash('danger', $process->getErrorOutput());
        }

        return $this->render('docker/containers.html.twig', [
            'containers' => $dockerService->decodeGoJson($process->getOutput()),
            'form' => $form,
            'hasFilters' => $hasFilters,
        ]);
    }

    #[Route('/logs/{id}', name: 'app_docker_containers_logs', methods: ['GET'])]
    public function logs(string $id): Response
    {
        $process = new Process(['docker', 'container', 'logs', $id]);
        $process->run();

        if (!$process->isSuccessful()) {
            return new Response(
                sprintf("\n\nOutput:\n================\n%s\n\nError Output:\n================\n%s",
                    $process->getOutput(),
                    $process->getErrorOutput()
                )
            );
        }

        return new Response($process->getOutput());
    }

    #[Route('/inspect/{id}', name: 'app_docker_containers_inspect', methods: ['GET'])]
    public function inspect(string $id): Response
    {
        $process = new Process(['docker', 'container', 'inspect', $id]);
        $process->run();

        if (!$process->isSuccessful()) {
            return new Response(
                sprintf("\n\nOutput:\n================\n%s\n\nError Output:\n================\n%s",
                    $process->getOutput(),
                    $process->getErrorOutput()
                )
            );
        }

        return new Response($process->getOutput());
    }

    #[Route('/start/{id}', name: 'app_docker_containers_start', methods: ['GET'])]
    public function start(string $id): JsonResponse
    {
        $process = new Process(['docker', 'container', 'start', $id]);
        $error = '';

        $process->run();

        if (!$process->isSuccessful()) {
            $error = $process->getErrorOutput();
        }

        return $this->json([
            'status' => 'OK',
            'message' => $process->getOutput(),
            'error' => $error,
            ]);
    }

    #[Route('/stop/{id}', name: 'app_docker_containers_stop', methods: ['GET'])]
    public function stop(string $id): JsonResponse
    {
        $process = new Process(['docker', 'container', 'stop', $id]);
        $error = '';

        $process->run();

        if (!$process->isSuccessful()) {
            $error = $process->getErrorOutput();
        }

        return $this->json([
            'status' => 'OK',
            'message' => $process->getOutput(),
            'error' => $error,
        ]);
    }

    #[Route('/remove/{id}', name: 'app_docker_containers_remove', methods: ['GET'])]
    public function remove(string $id): JsonResponse
    {
        $process = new Process(['docker', 'container', 'rm', $id]);
        $error = '';

        $process->run();

        if (!$process->isSuccessful()) {
            $error = $process->getErrorOutput();
        }

        return $this->json([
            'status' => 'OK',
            'message' => $process->getOutput(),
            'error' => $error,
        ]);
    }
}