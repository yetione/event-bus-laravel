<?php


namespace Yetione\EventBus\Contracts;


interface EventContract
{
    public function source(): string;

    public function scope(): ?string;

    public function name(): string;

    public function params(): array;

    public function payload(): array;
}
