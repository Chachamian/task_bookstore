<?php

require_once ('autoload.php');

$authSystem = new \App\Authorize();
$bookSystem = new \App\BookSystem();
$success    = false;

if (!$authSystem->isAdmin() || empty($_GET['id'])) {
    http_response_code(403);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($bookSystem->editBook($_GET['id'], $_POST)) {
        $success = true;
    }
}

$book = $bookSystem->getBookById($_GET['id']);

if ($book === null) {
    echo 'Не удалось найти книгу';
}

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
        <form action="/logout.php" method="post" class="d-flex align-items-center mb-0">
            <button type="submit" class="btn btn-outline-dark mb-0">Выйти из системы</button>
        </form>
    </div>
</nav>

<form method="post" action="/edit-book.php?id=<?=$_GET['id']?>" class="container">
    <div class="row">
        <div class="col-6 mx-auto">
            <h1 class="mb-3 mt-2">Редактирование книги</h1>
            <?php if (!empty($bookSystem->errors())) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php
                    foreach ($bookSystem->errors() as $error) {
                        echo '<p class="mb-0"> ' . $error . '</p>';
                    }
                    ?>
                </div>
            <?php endif ?>

            <?php if (empty($bookSystem->errors()) && $success === true) : ?>
                <div class="alert alert-success" role="alert">
                    Книга изменена
                </div>
            <?php endif ?>

            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Название</label>
                <input value="<?=$book->title?>"
                    type="text" name="title" class="form-control" id="exampleFormControlInput1" placeholder="Мертвые души">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Автор</label>
                <input value="<?=$book->author?>"
                       type="text" name="author" class="form-control" id="exampleFormControlInput1" placeholder="Гоголь">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Категория</label>
                <input value="<?=$book->category?>"
                       type="text" name="category" class="form-control" id="exampleFormControlInput1" placeholder="Классика">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Год</label>
                <input value="<?=$book->year?>"
                       type="number" name="year" class="form-control" id="exampleFormControlInput1" placeholder="1842">
            </div>
            <div class="form-check">
                <input <?php if($book->isPaid) {echo 'checked';} ?>
                    class="form-check-input" type="checkbox" value="1" id="flexCheckDefault" name="isPaid">
                <label class="form-check-label" for="flexCheckDefault">
                    Можно купить
                </label>
            </div>
            <div class="form-check">
                <input <?php if($book->isRent) {echo 'checked';} ?>
                    class="form-check-input" type="checkbox" value="1" id="flexCheckDefault1" name="isRent">
                <label class="form-check-label" for="flexCheckDefault1">
                    Можно арендовать
                </label>
            </div>
            <div class="form-check">
                <input <?php if($book->isCatalogAllowed) {echo 'checked';} ?>
                    class="form-check-input" type="checkbox" value="1" id="flexCheckDefault2" name="isCatalogAllowed">
                <label class="form-check-label" for="flexCheckDefault2">
                    Доступно в каталоге
                </label>
            </div>
            <div class="form-check mb-3 w-100 p-0 mt-3">
                <label for="exampleFormControlInput1" class="form-label">Статус книги</label>
                <select class="form-select" aria-label="Default select example" name="status">
                    <option value="Новинка" <?php if($book->isPaid) {echo 'selected';} ?>>Новинка</option>
                    <option value="На полках в магазине" <?php if($book->isPaid) {echo 'selected';} ?>>На полках в магазине</option>
                    <option value="Только для аренды" <?php if($book->isPaid) {echo 'selected';} ?>>Только для аренды</option>
                    <option value="От продавца" <?php if($book->isPaid) {echo 'selected';} ?>>От продавца</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Цена покупки</label>
                <input value="<?=$book->pricePaid?>" type="number" name="pricePaid" class="form-control" id="exampleFormControlInput1" placeholder="1300">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Цена аренды за день</label>
                <input value="<?=$book->priceRent?>" type="number" name="priceRent" class="form-control" id="exampleFormControlInput1" placeholder="100">
            </div>
            <button class="btn btn-primary w-100 mb-2">Изменить</button>
        </div>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>