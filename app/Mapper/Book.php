<?php

namespace App\Mapper;

class Book
{
    public ?int $id;
    public bool $isPaid;
    public bool $isRent;
    public ?string $title;
    public ?string $category;
    public ?string $author;
    public ?string $year;
    public int $pricePaid;
    public int $priceRent;
    public bool $isCatalogAllowed;
    public ?string $status;

    public function __construct(
        ?int $id = null,
        bool $isPaid = false,
        bool $isRent = false,
        ?string $title = null,
        ?string $category = null,
        ?string $author = null,
        ?string $year = null,
        ?int $pricePaid = 0,
        ?int $priceRent = 0,
        bool $isCatalogAllowed = false,
        ?string $status = null
    ) {
        $this->id = $id;
        $this->isPaid = $isPaid;
        $this->isRent = $isRent;
        $this->title = $title;
        $this->category = $category;
        $this->author = $author;
        $this->year = $year;
        $this->pricePaid = $pricePaid;
        $this->priceRent = $priceRent;
        $this->isCatalogAllowed = $isCatalogAllowed;
        $this->status = $status;
    }
}