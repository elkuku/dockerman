<?php

namespace App\Controller;

use App\Dto\ContainerOptions;
use App\Service\DockerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;

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

    #[Route('/volumes', name: 'app_docker_volumes', methods: ['GET'])]
    public function volumes(): Response
    {
        return $this->render('docker/volumes.html.twig');
    }

    #[Route('/networks', name: 'app_docker_networks', methods: ['GET'])]
    public function networks(DockerService $docker): Response
    {
        $process = new Process(['docker', 'network', 'ls']);
        $process2 = new Process(['docker', 'network', 'ls', '--format=json']);
        $process->run();
        $process2->run();

        return $this->render('docker/networks.html.twig', [
            'output' => $process->getOutput(),
            'output2' => $process2->getOutput(),
            'items' => $docker->decodeGoJson($process2->getOutput())
        ]);
    }
}