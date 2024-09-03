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

    public function setName(string $name): void;

    public function getStatus(): string;

    public function setStatus(string $status): void;

    public function getPrice(): int;

    public function setPrice(int $price): void;

    public function isEnabled(): bool;
}
