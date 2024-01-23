<?php

namespace Framewire\Inertia\Contracts;

use Symfony\Component\HttpFoundation\Response;

interface InertiaInterface
{
    public function render(string $component, array $props = [], int $statusCode = 200, array $headers = []): Response;
    public function version(string $version);
    public function share(string $key, $value = null);
    public function getVersion();
}
