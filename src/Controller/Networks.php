<?php

namespace App\Controller;

use App\Service\DockerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/networks')]
class Networks extends AbstractController
{
    #[Route('/', name: 'app_docker_networks_index', methods: ['GET'])]
    public function networks(DockerService $dockerService): Response
    {
        return $this->render('docker/networks.html.twig', [
            'items' => $this->getData($dockerService),
            'error'=>'',
        ]);
    }

    #[Route('/remove/{id}', name: 'app_docker_networks_remove', methods: ['GET'])]
    public function remove(string $id, DockerService $dockerService): Response
    {
        $process = new Process(['docker', 'network', 'rm', $id]);
        $error = '';

        $process->run();

        if (!$process->isSuccessful()) {
            $error = $process->getErrorOutput();
        } else{
            $this->addFlash('success', 'Item has been removed');
        }

        return $this->render('docker/networks.html.twig', [
            'items' => $this->getData($dockerService),
            'error' => $error,
        ]);
    }

    private function getData(DockerService $dockerService):array
    {
        $process = new Process(['docker', 'network', 'ls', '--format=json']);
        $process->run();

        return $dockerService->decodeGoJson($process->getOutput());
    }
}