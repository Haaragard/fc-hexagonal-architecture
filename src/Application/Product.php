<?php

namespace App\Application;

use RuntimeException;

class Product implements ProductInterface
{
    /**
     * @throws RuntimeException
     */
    public function __construct(
        public ?string $id,
        public string $name,
        public int $price,
        public string $status = self::DISABLED
    ) {
        $this->validate();
    }

    /**
     * @inheritDoc
     */
    public function validate(): void
    {
        if (! is_null($this->id) && empty($this->id)) {
            throw new RuntimeException('The product ID is required.');
        }
        if (empty($this->name)) {
            throw new RuntimeException('The product name is required.');
        }
        if ($this->price < 0) {
            throw new RuntimeException('The product price must be zero or greater to be valid.');
        }
        if ($this->status !== self::ENABLED && $this->status !== self::DISABLED) {
            throw new RuntimeException('The product status is invalid.');
        }
    }

    /**
     * @inheritDoc
     */
    public function enable(): void
    {
        if ($this->price <= 0) {
            throw new RuntimeException('The price must be greater than zero to enable the product.');
        }

        $this->status = self::ENABLED;
    }

    /**
     * @inheritDoc
     */
    public function disable(): void
    {
        if ($this->status !== self::ENABLED) {
            throw new RuntimeException('The product must be enabled to disable it.');
        }

        $this->status = self::DISABLED;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;

        $this->validate();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;

        $this->validate();
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;

        $this->validate();
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;

        $this->validate();
    }

    public function isEnabled(): bool
    {
        return $this->status === self::ENABLED;
    }
}
