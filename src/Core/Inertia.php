<?php

namespace Framewire\Inertia\Core;

use Framewire\Inertia\Contracts\InertiaInterface;
use Framewire\Inertia\Contracts\InertiaViewProviderInterface;
use Framewire\Inertia\Entities\Page;
use Framewire\Inertia\Exceptions\MissingInertiaConfigException;
use Framewire\Inertia\Traits\ReflectsProperties;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Inertia implements InertiaInterface
{
    use ReflectsProperties;

    private Request $request;
    private ?InertiaViewProviderInterface $viewProvider;
    private ?ContainerInterface $container;
    private Page $page;

    /**
     * @param Request $request
     * @param InertiaViewProviderInterface|null $viewProvider
     * @param ContainerInterface|null $container
     */
    public function __construct(Request $request, ?InertiaViewProviderInterface $viewProvider = null, ?ContainerInterface $container = null)
    {
        $this->request = $request;
        $this->viewProvider = $viewProvider;
        $this->container = $container;
        $this->page = Page::create();
    }

    /**
     * @throws ReflectionException|ContainerExceptionInterface
     */
    public function render(string $component, array $props = []): Response
    {
        $this->page = $this->page->withComponent($component)->withUrl($this->request->getUri());

        if ($this->request->headers->has('X-Inertia-Partial-Data')) {
            $only = explode(',', $this->request->headers->get('X-Inertia-Partial-Data'));
            $props = ($only && $this->request->headers->get('X-Inertia-Partial-Component') === $component)
                ? array_intersect_key($props, array_flip($only))
                : $props;
        }

        array_walk_recursive($props, function (&$prop) {
            if ($prop instanceof \Closure) {
                $prop = $prop();
            }
        });

        $this->page = $this->page->withProps($props);

        if ($this->request->headers->has('X-Inertia')) {
            $json = json_encode($this->page);
            return $this->createResponse($json, 'application/json');
        }

        $rootViewProvider = $this->viewProvider;

        if (is_null($rootViewProvider)) {
            $requiredTypes = [
                $this->getExpectedClassType($this, 'container'),
                $this->getExpectedClassType($this, 'viewProvider'),
            ];

            if (is_null($this->container)) {
                throw MissingInertiaConfigException::fromMessage(
                    "Either a $requiredTypes[0] or $requiredTypes[1] instance must be passed. If using a $requiredTypes[0], pass it; otherwise, pass a $requiredTypes[1] instance."
                );
            }

            try {
                $rootViewProvider = $this->container->get(InertiaViewProviderInterface::class);
            } catch (NotFoundExceptionInterface $e) {
                throw MissingInertiaConfigException::fromMessage(
                    sprintf(
                        'No %s instance provided inside %s',
                        $requiredTypes[1],
                        $requiredTypes[0]
                    )
                );
            }
        }

        $html = $rootViewProvider($this->page);
        return $this->createResponse($html, 'text/html; charset=UTF-8');
    }

    public function version(string $version): Page
    {
        return $this->page->withVersion($version);
    }

    public function share(string $key, $value = null)
    {
        $this->page = $this->page->addProp($key, $value);
    }

    public function getVersion(): ?string
    {
        return $this->page->getVersion();
    }

    private function createResponse(string $data, string $contentType): StreamedResponse
    {
        return new StreamedResponse(fn () => printf($data), 200, ['Content-Type' => $contentType]);
    }
}
