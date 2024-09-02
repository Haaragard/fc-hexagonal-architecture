<?php

namespace App\Application;

use RuntimeException;

interface ProductInterface
{
    public const string DISABLED = 'disabled';
    public const string ENABLED = 'enabled';

    /**
     * @throws RuntimeException
     */
    public function validate(): void;

    /**
     * @throws RuntimeException
     */
    public function enable(): void;

    /**
     * @throws RuntimeException
     */
    public function disable(): void;

    public function getId(): ?string;

    public function setId(string $id): void;

    public function getName(): string;

    public function getStatus(): string;

    public function getPrice(): int;

    public function isEnabled(): bool;
}
