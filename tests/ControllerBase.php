<?php

namespace App\Tests;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Route;
use UnexpectedValueException;

/**
 * Controller "smoke" test
 */
abstract class ControllerBase extends WebTestCase
{
    /**
     * Must be set in extending class.
     */
    protected string $controllerRoot = '';

    /**
     * @var array<int, string>
     */
    protected array $ignoredFiles = [];

    /**
     * @var array<string, array<string, array<string, int|string>>>
     */
    protected array $exceptions = [];

    /**
     * @var array<string, array<string, array<string, int|string>>>
     */
    private array $usedExceptions = [];

    abstract public function testAllRoutesAreProtected(): void;

    protected function runTests(KernelBrowser $client): void
    {
        /**
         * @var DelegatingLoader $routeLoader
         */
        $routeLoader = static::bootKernel()->getContainer()
            ->get('routing.loader');

        if ($this->controllerRoot === '' || $this->controllerRoot === '0') {
            throw new UnexpectedValueException(
                'Please set a controllerRoot directory!'
            );
        }

        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->controllerRoot)
        );

        $it->rewind();
        while ($it->valid()) {
            if (!$it->isDot()
                && !in_array($it->getSubPathName(), $this->ignoredFiles, true)
            ) {
                $sub = $it->getSubPath() ? $it->getSubPath() . '\\' : '';

                $routerClass = 'App\Controller\\' . $sub . basename(
                        (string) $it->key(),
                        '.php'
                    );
                $routes = $routeLoader->load($routerClass)->all();

                $this->processRoutes($routes, $client);
            }

            $it->next();
        }

        self::assertEquals($this->exceptions, $this->usedExceptions);
    }

    /**
     * @param array<Route> $routes
     */
    private function processRoutes(
        array         $routes,
        KernelBrowser $browser,
    ): void
    {
        foreach ($routes as $routeName => $route) {
            $expectedStatusCodes = [];
            $path = str_replace('{id}', '1', $route->getPath());

            if (array_key_exists($routeName, $this->exceptions)) {
                if (array_key_exists(
                    'statusCodes',
                    $this->exceptions[$routeName]
                )
                ) {
                    $expectedStatusCodes = $this->exceptions[$routeName]['statusCodes'];
                    $this->usedExceptions[$routeName]['statusCodes'] = $this->exceptions[$routeName]['statusCodes'];
                }

                if (array_key_exists(
                    'params',
                    $this->exceptions[$routeName]
                )
                ) {
                    $this->usedExceptions[$routeName]['params'] = $this->exceptions[$routeName]['params'];
                    foreach (
                        $this->exceptions[$routeName]['params'] as $param =>
                        $paramReplacement
                    ) {
                        $path = str_replace(
                            $param,
                            (string)$paramReplacement,
                            $path
                        );
                    }
                }
            }

            $methods = $route->getMethods();

            if (!$methods) {
                echo sprintf(
                        'No methods set in controller "%s"',
                        $route->getPath()
                    ) . PHP_EOL;
                $methods = ['GET'];
            }

            foreach ($methods as $method) {
                $expectedStatusCode = array_key_exists(
                    $method,
                    $expectedStatusCodes
                ) ? $expectedStatusCodes[$method] : 302;

                $browser->request($method, $path);

                self::assertEquals(
                    $expectedStatusCode,
                    $browser->getResponse()->getStatusCode(),
                    sprintf(
                        'failed: %s (%s) with method: %s',
                        $routeName,
                        $path,
                        $method
                    )
                );
            }
        }
    }
}