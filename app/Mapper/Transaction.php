<?php

namespace App\Mapper;

class Transaction
{
    public ?int $id;
    public ?int $user_id;
    public ?int $book_id;
    public ?int $price_spent;
    public bool $is_rent;
    public bool $is_paid;
    public ?int $dateExpired;

    public function __construct($id = null, $user_id = null, $book_id = null, $price_spent = null, $is_rent = false, $is_paid = false, $dateExpired = null)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->book_id = $book_id;
        $this->price_spent = $price_spent;
        $this->is_rent = $is_rent;
        $this->is_paid = $is_paid;
        $this->dateExpired = $dateExpired;
    }
}