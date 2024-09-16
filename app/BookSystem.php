<?php

namespace App;

use App\Exception\NullableObject;
use App\Exception\ValidateException;
use App\Mapper\Book;
use App\Mapper\User;

class BookSystem
{
    private FileSystem $_fileSystem;
    private array $_errors;

    public function __construct()
    {
        $this->_fileSystem = new FileSystem();
        $this->_errors    = [];
    }

    public function notification(): array
    {
        $transactions = $this->_fileSystem->getTransactionFileData();
        $notification  = [];

        foreach ($transactions as $transaction) {
            if ($transaction->dateExpired !== null && time() + 3600 * 24 * 3 > $transaction->dateExpired) {
                $book = $this->getBookById($transaction->book_id);
                if ($book == null) {
                    continue;
                }

                $notification[] = "Заканчивается время аренды книги: {$book->title}. Успейти прочитать до " . date('d.m.Y H:i', $transaction->dateExpired);
            }
        }

        return $notification;
    }

    public function isBookExistForUser(?User $user, Book $book): bool
    {
        if ($user === null) {
            return false;
        }

        foreach ($this->_fileSystem->getTransactionFileData() as $transaction) {
            if ($book->id === $transaction->book_id && $user->id === $transaction->user_id) {
                return true;
            }
        }

        return false;
    }

    public function isCanPaidBook(Book $book): bool
    {
        return $book->isPaid && $book->pricePaid > 0;
    }

    public function isCanRentBook(Book $book): bool
    {
        return $book->isRent && $book->priceRent > 0;
    }

    public function getBookById(int $id): ?Book
    {
        $books = $this->_fileSystem->getBooksFileData();
        foreach ($books as $book) {
            if ($book->id == $id) {
                return $book;
            }
        }

        return null;
    }

    /**
     * @param string|null $sorting
     * @return Book[]
     */
    public function getBooks(?string $sorting = null): array
    {
        $books = $this->_fileSystem->getBooksFileData();
        $books = array_filter($books, function (Book $book) {
            if (!$book->isCatalogAllowed) {
                return false;
            }
            return true;
        });

        if ($sorting !== null) {
            usort($books, function($object1,$object2) use ($sorting){
                if($object1->$sorting == $object2->$sorting) return 0;
                return ($object1->$sorting > $object2->$sorting) ? 1 : -1;});
        }

        return $books;
    }

    public function createBook(array $data): bool
    {
        $this->_errors = [];
        $books = $this->_fileSystem->getBooksFileData();
        try {
            $books[] = $this->_tryInitializeBook($data);
        } catch (NullableObject|ValidateException $e) {
            $this->_errors[] = $e->getMessage();
            return false;
        }

        $this->_fileSystem->writeBooksFileData($books);
        return true;
    }

    public function editBook(int $id, array $data): bool
    {
        $this->_errors = [];
        $books = $this->_fileSystem->getBooksFileData();
        try {
            $book = $this->_tryInitializeBook($data);
            $book->id = $id;
            $books = array_map(function ($bookLoaded) use ($book) {
                if ($bookLoaded->id === $book->id) {
                    return $book;
                }

                return $bookLoaded;
            }, $books);
        } catch (NullableObject|ValidateException $e) {
            $this->_errors[] = $e->getMessage();
            return false;
        }

        $this->_fileSystem->writeBooksFileData($books, true);
        return true;
    }

    public function errors()
    {
        return $this->_errors;
    }

    private function _tryInitializeBook(array $data): Book
    {
        $book = new Book();

        if (empty($data)) {
            throw new NullableObject('Поля не переданы');
        }

        $book->id = $this->_fileSystem->GetIncrement('books');

        if (empty($data['title'])) {
            throw new ValidateException('Заголовок обязателен к заполнению');
        }
        if (empty($data['status'])) {
            throw new ValidateException('Статус обязателен к заполнению');
        }
        if (!empty($data['year']) && !is_numeric($data['year'])) {
            throw new ValidateException('Год должен быть числом');
        }

        $book->title            = $data['title'];
        $book->status           = $data['status'];
        $book->author           = $data['author'] ?? null;
        $book->category         = $data['category'] ?? null;
        $book->isRent           = $data['isRent'] ?? false;
        $book->isPaid           = $data['isPaid'] ?? false;
        $book->isCatalogAllowed = $data['isCatalogAllowed'] ?? false;
        $book->year             = $data['year'] ?? null;
        $book->priceRent        = empty($data['priceRent']) ? 0 : intval($data['priceRent']);
        $book->pricePaid        = empty($data['pricePaid']) ? 0 : intval($data['pricePaid']);

        return $book;
    }
}