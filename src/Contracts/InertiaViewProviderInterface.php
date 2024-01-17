<?php

namespace Framewire\Inertia\Contracts;

use Framewire\Inertia\Entities\Page;

interface InertiaViewProviderInterface
{
    public function __invoke(Page $page): String;
}
