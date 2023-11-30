<?php

namespace App\Controller;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'app_default', methods: ['GET'])]
class DefaultController extends BaseController
{
    public function __invoke(
        #[Autowire('%kernel.project_dir%')] string $projectDir,
    ): Response
    {
      //  $process = new Process(['docker', 'info', '--format=json']);
       $process = new Process(['docker', 'info']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'php_version' => PHP_VERSION,
            'symfony_version' => Kernel::VERSION,
            'project_dir' => $projectDir,
            'consoleOutput'=> $process->getOutput(),
        ]);
    }
}
