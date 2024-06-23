<?php

namespace App\Tests\Controller;

use App\Tests\ControllerBase;

class ControllerAccessTest extends ControllerBase
{
    protected string $controllerRoot = __DIR__ . '/../../src/Controller';

    /**
     * @var array<int, string>
     */
    protected array $ignoredFiles
        = [
            '.gitignore',
            'BaseController.php',
            'Security/GoogleController.php',
            'Security/GitHubController.php',
            'Security/GitLabController.php',
        ];

    /**
     * @var array<string, array<string, array<string, int>>>
     */
    protected array $exceptions
        = [
            'app_default' => [
                'statusCodes' => ['GET' => 200],
            ],
            'app_docker_containers_index' => [
                'statusCodes' => ['GET' => 200, 'POST' => 200],
            ],
            'app_docker_images' => [
                'statusCodes' => ['GET' => 200, 'POST' => 200],
            ],
            'app_docker_volumes_index' => [
                'statusCodes' => ['GET' => 200, 'POST' => 200],
            ],
            'app_docker_volumes_remove' => [
                'statusCodes' => ['GET' => 200, 'POST' => 200],
            ],
            'app_docker_networks_index' => [
                'statusCodes' => ['GET' => 200, 'POST' => 200],
            ],
            'app_docker_networks_remove' => [
                'statusCodes' => ['GET' => 200, 'POST' => 200],
            ],
            'app_docker_containers_logs' => [
                'statusCodes' => ['GET' => 200, 'POST' => 200],
            ],
            'app_docker_containers_inspect' => [
                'statusCodes' => ['GET' => 200, 'POST' => 200],
            ],
            'app_docker_containers_start' => [
                'statusCodes' => ['GET' => 200, 'POST' => 200],
            ],
            'app_docker_containers_stop' => [
                'statusCodes' => ['GET' => 200, 'POST' => 200],
            ],
            'app_docker_containers_remove' => [
                'statusCodes' => ['GET' => 200, 'POST' => 200],
            ],
        ];

    #[\Override]
    public function testAllRoutesAreProtected(): void
    {
        $this->runTests(static::createClient());
    }
}
