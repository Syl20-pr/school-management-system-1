<?php
// app/DataTransferObjects/BulletinFilters.php

namespace App\DataTransferObjects;

class BulletinFilters
{
    public function __construct(
        public readonly ?int $yearId,
        public readonly ?int $classId,
        public readonly ?int $termTypeId
    ) {}
    
    public static function fromRequest($request): self
    {
        return new self(
            $request->year_id,
            $request->class_id,
            $request->term_type_id
        );
    }
    
    public function toArray(): array
    {
        return [
            'year_id' => $this->yearId,
            'class_id' => $this->classId,
            'term_type_id' => $this->termTypeId
        ];
    }
}