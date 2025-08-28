<?php

declare(strict_types=1);

use Devonab\FilamentEasyFooter\Services\SemverVersionComparator;

beforeEach(function () {
    $this->cmp = new SemverVersionComparator;
});

it('treats identical versions as equal', function () {
    expect($this->cmp->isEqual('1.2.3', '1.2.3'))->toBeTrue()
        ->and($this->cmp->compare('1.2.3', '1.2.3'))->toBe(0);
});

it('normalizes leading v and compares correctly', function () {
    expect($this->cmp->isEqual('v1.0.0', '1.0.0'))->toBeTrue()
        ->and($this->cmp->isLower('v1.0.0', '1.0.1'))->toBeTrue()
        ->and($this->cmp->isHigher('V1.2.0', '1.1.9'))->toBeTrue();
});

it('handles pre-release semantics with version_compare', function () {
    // Pre-release should be lower than the final release
    expect($this->cmp->isLower('1.0.0-rc.1', '1.0.0'))->toBeTrue()
        ->and($this->cmp->isHigher('1.0.0-rc.2', '1.0.0-rc.1'))->toBeTrue();
});

it('orders major/minor/patch as expected', function () {
    expect($this->cmp->isHigher('2.0.0', '1.9.9'))->toBeTrue() // major
        ->and($this->cmp->isHigher('1.10.0', '1.9.9'))->toBeTrue() // minor
        ->and($this->cmp->isHigher('1.2.4', '1.2.3'))->toBeTrue();  // patch

});

it('returns null compare and false booleans when any side is null/empty', function () {
    expect($this->cmp->compare(null, '1.0.0'))->toBeNull()
        ->and($this->cmp->compare('1.0.0', null))->toBeNull()
        ->and($this->cmp->compare('', '1.0.0'))->toBeNull()
        ->and($this->cmp->isLower(null, '1.0.0'))->toBeFalse()
        ->and($this->cmp->isEqual('1.0.0', null))->toBeFalse()
        ->and($this->cmp->isHigher('', ''))->toBeFalse();
});
