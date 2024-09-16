<?php
require_once 'autoload.php';

$auth = new \App\Authorize();
if ($auth->user() !== null) {
    header('Location: /');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? htmlspecialchars(trim($_POST['username'])) : '';
    $password = isset($_POST['password']) ? htmlspecialchars(trim($_POST['password'])) : '';

    $auth = new \App\Authorize($username, $password);
    $auth->auth();
}
?>

<html lang="en">
<head>
    <title>Авторизация</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
<form method="post" action="/login.php" class="container">
    <div class="row">
        <div class="col-6 mx-auto">
            <h1 class="mb-3 mt-2">Авторизация</h1>
            <?php if (!empty($auth->getError())) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php
                    foreach ($auth->getError() as $error) {
                        echo '<p class="mb-0"> ' . $error . '</p>';
                    }
                    ?>
                </div>
            <?php endif ?>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Имя (ник)</label>
                <input type="text" name="username" class="form-control" id="exampleFormControlInput1" placeholder="Ivan">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Пароль</label>
                <input type="password" name="password" class="form-control" id="exampleFormControlInput1">
            </div>
            <button class="btn btn-primary w-100 mb-2">Войти</button>
            <a href="/register.php" class="btn btn-secondary w-100">Регистрация</a>
        </div>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>


