<?php

use Framewire\Inertia\Entities\Page;

it('constructs with default parameters', function () {
    $page = new Page();

    expect($page->getComponent())->toBeNull()
        ->and($page->getProps())->toBeEmpty()
        ->and($page->getUrl())->toBeNull()
        ->and($page->getVersion())->toBeNull();
});

it('constructs with given values', function () {
    $component = 'MyComponent';
    $props = ['foo' => 'bar'];
    $url = '/home';
    $version = '12345';

    $page = new Page($component, $props, $url, $version);

    expect($page->getComponent())->toBeString()->toBe($component)
        ->and($page->getProps())->toBeArray()->toBe($props)
        ->and($page->getUrl())->toBeString()->toBe($url)
        ->and($page->getVersion())->toBeString()->toBe($version);
});

test('the create method creates a new instance', function () {
    $page = Page::create();

    expect($page)->toBeObject()->toBeInstanceOf(Page::class);
});

test('new props can be added and a new instance is created after that', function () {
    $page = new Page();
    $newPage = $page->addProp('name', 'John Doe');

    expect($newPage)->toBeObject()
        ->toBeInstanceOf(Page::class)
        ->not->toEqual($page)
        ->and($newPage->getProps())->toEqual(['name' => 'John Doe'])
        ->and($page->getProps())->toBeEmpty();
});

it('can serialize json serialize and its array contains required properties', function () {
    $page = new Page();

    expect($page)->toBeInstanceOf(JsonSerializable::class)
        ->and($page->jsonSerialize())->toBeArray()->toHaveKeys(['component', 'props', 'url', 'version']);
});
