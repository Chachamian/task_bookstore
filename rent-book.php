<?php

require_once ('autoload.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flash = new \App\FlashSystem();
    try {
        $bookId = $_GET['book_id'] ?? null;
        $userId = $_GET['user_id'] ?? null;
        $term = $_GET['term'] ?? null;
        $isPaid = $_GET['isPaid'] ?? false;

        if ($bookId === null || $userId === null) {
            $flash->setMessage('Покупка не удалась');
            header('Location: /');
        }

        $fileSystem = new \App\FileSystem();
        $bookSystem = new \App\BookSystem();
        $book = $bookSystem->getBookById($bookId);
        $transactions = $fileSystem->getTransactionFileData();

        $transactions[] = new \App\Mapper\Transaction(
            $fileSystem->GetIncrement('transactions'),
            $userId,
            $bookId,
            $book->priceRent * $term,
            !$isPaid,
            $isPaid,
            $isPaid ? null : time() + 3600 * 24 * $term
        );

        $fileSystem->writeTransactionFileData($transactions);
        $flash->setMessage('Покупка выполнена успешна');
        header('Location: /');
    } catch (Throwable $t) {
        $flash->setMessage('Произошла ошибка во время покупки! ' . $t->getMessage());
        header('Location: /');
    }
}