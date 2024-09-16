<?php

require_once 'autoload.php';

use App\Authorize;

$authSystem = new Authorize();
$user = $authSystem->user();
$bookSystem = new \App\BookSystem();
$sorting = $_GET['sort'] ?? null;
$books = $bookSystem->getBooks($sorting);

$flash = new \App\FlashSystem();
$flashMessage = $flash->tryGetFlashMessage();
?>

<html lang="en">
<head>
    <title>Книги</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body style="background-color: #d9e2ea">
<nav class="navbar navbar-light bg-light">
    <div class="container">
        <a href="/" class="navbar-brand d-flex align-items-center">
            <img src="https://getbootstrap.com/docs/5.0/assets/brand/bootstrap-logo.svg" alt="" width="30" height="24">
            Книги
        </a>
        <?php if($user !== null) : ?>
        <form action="/logout.php" method="post" class="d-flex align-items-center mb-0">
            <button type="submit" class="btn btn-outline-dark mb-0">Выйти из системы</button>
        </form>
        <?php else : ?>
        <div class="d-flex">
            <a href="/login.php" class="btn btn-outline-dark" style="margin-right: 10px">Войти</a>
            <a href="/register.php" class="btn btn-outline-dark">Регистрация</a>
        </div>

        <?php endif;?>
    </div>
</nav>

<div class="container">
    <?php if (!empty($bookSystem->notification())) : ?>
        <div class="alert alert-danger" role="alert">
            <?php
            foreach ($bookSystem->notification() as $notify) {
                echo '<p class="mb-0"> ' . $notify . '</p>';
            }
            ?>
        </div>
    <?php endif ?>
    <?php if ($flashMessage !== null) : ?>
        <div class="alert alert-info" role="alert">
            <p class="mb-0"><?=$flashMessage?></p>
        </div>
    <?php endif ?>

    <div class="btn-group my-4" role="group" aria-label="Basic outlined example">
        <a href="/?sort=category" type="button" class="btn
        <?php if ($sorting === 'category') {echo ' btn-dark';} else {echo 'btn-outline-dark';} ?>">Категория</a>
        <a href="/?sort=author" type="button" class="btn
        <?php if ($sorting === 'author') {echo ' btn-dark';} else {echo 'btn-outline-dark';} ?>">Автор</a>
        <a href="/?sort=year" type="button" class="btn
        <?php if ($sorting === 'year') {echo ' btn-dark';} else {echo 'btn-outline-dark';} ?>">Год</a>
    </div>
    <div class="row mt-3 g-3">
        <?php foreach ($books as $book) : ?>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?=$book->title?>(<?=$book->year ?? 'YYYY'?>)</h5>
                    <p class="card-text mb-1"><?=$book->author ?? 'Неизвестный автор'?></p>
                    <p class="card-text mb-1"><?=$book->category ?? 'Неизвестная категория'?></p>
                    <p class="card-text mb-1"><?=$book->status?></p>
                    <p class="card-text mb-1">Цена покупки: <?=$book->pricePaid?></p>
                    <p class="card-text mb-1">Цена аренды: <?=$book->priceRent?></p>
                    <div class="row mt-4 justify-content-center">
                        <?php if (!$bookSystem->isBookExistForUser($user, $book)) : ?>
                        <?php if ($bookSystem->isCanRentBook($book) && $user !== null) : ?>
                            <form method="post" action="/rent-book.php?book_id=<?=$book->id?>&user_id=<?=$user->id?>&term=14">
                                <button type="submit" class="btn btn-outline-primary col-sm-12">Аренда 2 недели</button>
                            </form>
                                <form method="post" action="/rent-book.php?book_id=<?=$book->id?>&user_id=<?=$user->id?>&term=30">
                                    <button type="submit" class="btn btn-outline-primary col-sm-12">Аренда месяц</button>
                                </form>
                                <form method="post" action="/rent-book.php?book_id=<?=$book->id?>&user_id=<?=$user->id?>&term=90">
                                    <button type="submit" class="btn btn-outline-primary col-sm-12">Аренда 3 месяца</button>
                                </form>
                        <?php endif;?>
                        <?php if ($bookSystem->isCanPaidBook($book) && $user !== null) : ?>
                                <form method="post" action="/rent-book.php?book_id=<?=$book->id?>&user_id=<?=$user->id?>&isPaid=true">
                                    <button type="submit" class="btn btn-outline-primary col-sm-12">Купить навсегда</button>
                                </form>
                        <?php endif; ?>
                        <?php else: ?>
                            <a class="btn btn-outline-primary col-sm-11">Книга уже куплена или находится в аренде</a>
                        <?php endif;?>
                    </div>

                    <?php if($user !== null && $user->role === 1) :?>
                        <a class="btn btn-primary d-flex w-100 mt-3 justify-content-center" href="/edit-book.php?id=<?=$book->id?>">Редактировать</a>
                    <?php endif;?>

                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
