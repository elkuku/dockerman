<?php

namespace App\Service;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

readonly class GitInfo
{
    public function __construct(private string $dir)
    {
    }

    public function isGitDir(): bool
    {
        return is_dir($this->dir . '/.git');
    }

    public function getStatus(): array
    {
        $status = [
            'modified' => [],
            'deleted' => [],
            'renamed' => [],
            'unversioned' => [],
        ];

        if (false === $this->isGitDir()) {
            return $status;
        }

        $process = new Process(['git', 'status', '--porcelain=v1'], $this->dir);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();

        $lines = explode("\n", $output);

        foreach ($lines as $line) {
            if (!$line) {
                continue;
            }
            $type = trim(substr($line, 0, 2));
            $file = substr($line, 3);
            switch ($type) {
                case 'M':
                    $status['modified'][] = $file;
                    break;
                case '??':
                    $status['unversioned'][] = $file;
                    break;
                case 'D':
                    $status['deleted'][] = $file;
                    break;
                case 'R':
                    $status['renamed'][] = $file;
            }
        }

        return $status;
    }
}