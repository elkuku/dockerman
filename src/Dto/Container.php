<?php

namespace App\Dto;

class Container
{
    public string $id = '';

    public string $names = '';

    public string $image = '';

    public string $created = '';

    public string $status = '';

    public string $ports = '';

    public function fromJson(string $data): self
    {
        return $this;
    }

}