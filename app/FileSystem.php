<?php

namespace App;

use App\Mapper\Book;
use App\Mapper\Transaction;
use App\Mapper\User;

class FileSystem
{
    private const APP_BD_PREFIX = '/bd/';

    /** @var Transaction[]|null */
    private ?array $_cacheTransaction = null;

    /**
     * @return User[]
     */
    public function getUsersFileData(): array
    {
        $users =  $this->getInfoInDb('users') ?? [];

        return array_map(function($user) {
            return new User(
                $user->id,
                $user->userName,
                $user->password,
                $user->role,
            );
        }, $users);
    }

    public function writeUsersFileData(array $data)
    {
        $this->writeInfoInDb('users', $data);
    }

    /**
     * @return Transaction[]
     */
    public function getTransactionFileData(): array
    {
        if ($this->_cacheTransaction != null && is_array($this->_cacheTransaction)) {
            return $this->_cacheTransaction;
        }

        $transactions = $this->getInfoInDb('transactions') ?? [];
        $transactions = array_map(function($transaction) {
            return new Transaction(
                $transaction->id,
                $transaction->user_id,
                $transaction->book_id,
                $transaction->price_spent,
                $transaction->is_rent,
                $transaction->is_paid,
                $transaction->dateExpired,
            );
        }, $transactions);

        $this->_cacheTransaction = $transactions;

        return $transactions;
    }

    public function writeTransactionFileData(array $data): void
    {
        $this->writeInfoInDb('transactions', $data);
        $this->_cacheTransaction = $data;
    }

    /**
     * @return Book[]
     */
    public function getBooksFileData(): array
    {
        $booksData = $this->getInfoInDb('books') ?? [];

        return array_map(function($bookData) {
            return new Book(
                $bookData->id,                // id книги
                $bookData->isPaid,            // оплачена ли книга
                $bookData->isRent,            // арендована ли книга
                $bookData->title,             // название книги
                $bookData->category,          // категория книги
                $bookData->author,            // автор книги
                $bookData->year,              // год выпуска
                $bookData->pricePaid,         // цена за покупку
                $bookData->priceRent,         // цена аренды
                $bookData->isCatalogAllowed,   // разрешено ли добавление в каталог
                $bookData->status              // статус книги
            );
        }, $booksData);
    }

    public function writeBooksFileData(array $data, bool $isUpdate = false)
    {
        $this->writeInfoInDb('books', $data, $isUpdate);
    }

    public function AddIncrement(string $table): void
    {
        $data = $this->getInfoInDb('increment', true);
        $increment = $data[$table]['value'] ?? 0;
        $data[$table]['value'] = $increment + 1;
        $this->writeInfoInDb('increment', $data);
    }

    public function GetIncrement(string $table)
    {
        $data = $this->getInfoInDb('increment', true);
        $increment = $data[$table]['value'] ?? 0;
        return $increment + 1;
    }

    public function getInfoInDb($fileName, $isArray = false)
    {
        return json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . self::APP_BD_PREFIX . "{$fileName}.json"), $isArray);
    }

    public function writeInfoInDb($fileName, $data, $isUpdate = false): void
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . self::APP_BD_PREFIX . "{$fileName}.json", json_encode($data, JSON_UNESCAPED_UNICODE));
        if ($fileName !== 'increment' && !$isUpdate) {
            $this->AddIncrement($fileName);
        }
    }
}