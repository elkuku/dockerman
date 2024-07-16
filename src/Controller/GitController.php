<?php

namespace App\Controller;

use App\Service\GitInfo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/git')]
class GitController extends AbstractController
{
    #[Route('/', name: 'app_git')]
    public function index(
        #[Autowire('%env(GIT_REPO_DIR)%')]
        string $repoDir
    ): Response
    {
        $directories = (new Finder())
            ->directories()
            ->in($repoDir)
            ->depth('== 0')
            ->sortByCaseInsensitiveName();

        return $this->render('git/index.html.twig', [
            'repoDir' => $repoDir,
            'directories' => $directories,
        ]);
    }

    #[Route('/info/{dir}', name: 'app_git_info')]
    public function info(
        #[Autowire('%env(GIT_REPO_DIR)%')] string $repoDir,
        string                                    $dir
    ): Response
    {
        $path = $repoDir . '/' . $dir;

        $gitInfo = new GitInfo($path);

        $response = new \stdClass();
        $response->dir = $dir;
        $response->path = $path;
        $response->isGitDir = $gitInfo->isGitDir();
        $response->status = $gitInfo->getStatus();

        return $this->json($response);
    }
}
