<?php

namespace App\Service;

class DockerService
{
    /**
     * @return array<string>
     */
    public function decodeGoJson(string $string): array
    {
        $arr = explode("\n", trim($string));
        $jsonString = '[' . implode(',', $arr) . ']';
        if (!json_validate($jsonString)) {
            throw new \InvalidArgumentException('Invalid JSON string');
        }

        return json_decode($jsonString, null, 512, JSON_THROW_ON_ERROR);
    }

    public function getContainerForImages(): void
    {

    }
}