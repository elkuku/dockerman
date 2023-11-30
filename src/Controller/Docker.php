<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/docker')]
class Docker extends BaseController
{
    #[Route('/containers', name: 'app_docker_containers', methods: ['GET'])]
    public function containers(): Response
    {
        return $this->render('docker/containers.html.twig');
    }

    #[Route('/images', name: 'app_docker_images', methods: ['GET'])]
    public function images(): Response
    {
        return $this->render('docker/images.html.twig');
    }

    #[Route('/volumes', name: 'app_docker_volumes', methods: ['GET'])]
    public function volumes(): Response
    {
        return $this->render('docker/volumes.html.twig');
    }
}