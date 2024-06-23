<?php

namespace App\Dto;

class ContainerOptions
{
    public bool $onlyRunning = false;

    public ?string $filterName = '';

    public ?string $filterAncestor = '';
}