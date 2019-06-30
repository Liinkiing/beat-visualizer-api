<?php


namespace App\Traits;


trait PaginatedItemTrait
{
    protected $id;

    public function getId(): string
    {
        return $this->id;
    }
}
