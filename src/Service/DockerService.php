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
        $json = json_validate($jsonString);
        $dd = json_decode($jsonString);

        return $dd;
    }

    public function getContainerforImages():void
    {
        
    }
}