<?php

namespace App\Twig\Components\Docker;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class Containers
{
    use DefaultActionTrait;

    public function getContainers(): string
    {
        $command = [
            'docker',
            'container',
            'ls',
            //'-a',
            '--format=json'
        ];
        $process = new Process($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $output = $process->getOutput();
        $arr = explode("\n", trim($output));
        $jsonString = '['.implode(',',$arr).']';
        $json = json_validate($jsonString);
        $dd = json_decode($jsonString);
        return $process->getOutput();
    }
}