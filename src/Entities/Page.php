<?php

namespace Framewire\Inertia\Entities;

use JsonSerializable;

class Page implements JsonSerializable
{
    private ?string $component;
    private array $props;
    private ?string $url;
    private ?string $version;

    /**
     * @param string|null $component
     * @param array $props
     * @param string|null $url
     * @param string|null $version
     */
    public function __construct(?string $component = null, array $props = [], ?string $url = null, ?string $version = null)
    {
        $this->component = $component;
        $this->props = $props;
        $this->url = $url;
        $this->version = $version;
    }

    public function addProp(string $key, $value = null): self
    {
        $page = clone $this;
        $page->props[$key] = $value;
        return $page;
    }

    public static function create(): self
    {
        return new static();
    }

    public function withVersion(string $version): self
    {
        $page = clone $this;
        $page->version = $version;
        return $page;
    }

    public function withComponent(string $component): self
    {
        $page = clone $this;
        $page->component = $component;
        return $page;
    }

    public function withUrl(string $url): self
    {
        $page = clone $this;
        $page->url = $url;
        return $page;
    }

    public function withProps(array $props): self
    {
        $page = clone $this;
        $page->props = $props;
        return $page;
    }

    public function getComponent(): ?string
    {
        return $this->component;
    }

    public function getProps(): array
    {
        return $this->props;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'component' => $this->getComponent(),
            'props' => $this->getProps(),
            'url' => $this->getUrl(),
            'version' => $this->getVersion(),
        ];
    }
}
