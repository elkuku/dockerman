<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'app_default', methods: ['GET'])]
class DefaultController extends AbstractController
{
    public function __invoke(): Response
    {
        $process = new Process(['docker', 'info']);
        $error = '';

        try {
            $process->mustRun();
        } catch (ProcessFailedException $processFailedException) {
            $error = $processFailedException->getMessage();
        }

        return $this->render('default/index.html.twig', [
            'error' => $error,
            'consoleOutput' => $process->getOutput(),
        ]);
    }
}
