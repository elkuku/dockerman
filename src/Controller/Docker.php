<?php

namespace App\Controller;

use App\Service\DockerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/docker')]
class Docker extends AbstractController
{
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
}