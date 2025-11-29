<?php

declare(strict_types=1);


beforeEach(function () {
    $this->manager = freshHookManager();
});

test('can register filter', function () {
    $this->manager->addFilter('content.render', function (string $content) {
        return $content . ' - Modified';
    });

    $result = $this->manager->applyFilters('content.render', 'Original');

    expect($result)->toBe('Original - Modified');
});

test('filters chain correctly', function () {
    $this->manager->addFilter('text.transform', function (string $text) {
        return strtoupper($text);
    }, 10);

    $this->manager->addFilter('text.transform', function (string $text) {
        return $text . '!';
    }, 20);

    $result = $this->manager->applyFilters('text.transform', 'hello');

    expect($result)->toBe('HELLO!');
});

test('respects filter priority', function () {
    $this->manager->addFilter('number.modify', function (int $num) {
        return $num * 2;
    }, 20);

    $this->manager->addFilter('number.modify', function (int $num) {
        return $num + 10;
    }, 10);

    $result = $this->manager->applyFilters('number.modify', 5);

    // First: 5 + 10 = 15, Then: 15 * 2 = 30
    expect($result)->toBe(30);
});

test('can pass additional arguments to filters', function () {
    $this->manager->addFilter('price.calculate', function (float $price, float $tax, float $discount) {
        return ($price + $tax) - $discount;
    });

    $result = $this->manager->applyFilters('price.calculate', 100.0, [20.0, 10.0]);

    expect($result)->toBe(110.0);
});

test('handles non-existent filters gracefully', function () {
    $result = $this->manager->applyFilters('non.existent', 'original');

    expect($result)->toBe('original');
});

test('filters can change value type', function () {
    $this->manager->addFilter('convert.to.array', function (string $value) {
        return explode(',', $value);
    });

    $result = $this->manager->applyFilters('convert.to.array', 'a,b,c');

    expect($result)->toBe(['a', 'b', 'c']);
});
