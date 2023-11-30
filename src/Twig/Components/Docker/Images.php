<?php

namespace App\Twig\Components\Docker;

use Symfony\Component\Process\Process;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class Images
{
    use DefaultActionTrait;

    public function getRandomNumber(): int
    {
        return rand(0, 1000);
    }

    public function getContainers(): string
    {
        $process = new Process(['docker', 'image', 'ls']);
        $process->run();

        return $process->getOutput();
    }
}