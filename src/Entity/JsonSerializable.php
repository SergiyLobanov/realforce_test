<?php

namespace App\Entity;

abstract class JsonSerializable
{
    /**
     * Entity serialization
     *
     * @return array
     */
    abstract public function jsonSerialize(): array;

    /**
     * @return int|null
     */
    abstract public function getId(): ?int;
}