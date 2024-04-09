<?php

namespace App\Service;

class ServiceExceptionData
{
    public function __construct(protected int $statusCode, protected string $type) 
    {

    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode($StatusCode): void
    {
        $this->statusCode = $StatusCode;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType($type): void
    {
        $this->type = $type;
    }

    public function toArray(): array
    {
        return [
            "type" => $this->type,
        ];
    }
}

?>