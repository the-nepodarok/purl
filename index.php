<?php
session_start();

require_once 'classes/UrlHandler.php';

// получение данных пользователя, если он есть
$user = $_SESSION['user'] ?? null;

if ($user) {
    // получение всех ссылок польз-ля
    $urls = \classes\UrlHandler::findAllByUser($user['id']);
}
?>

<html lang="ru">
    <head>
        <title>pURL!</title>
        <link rel="stylesheet" href="/css/main.css">
    </head>
    <body>
        <header>
            <h1 class="visually-hidden">pURL!</h1>
            <img class="logo" src="src/purl.png" width="200" alt="Логотип pURL!">
            <img class="logo" src="src/purl.png" width="200" alt="Логотип pURL!">

            <nav class="nav-bar">
        <?php if ($user): ?>
                <p class="user-email">Пользователь:
                    <span class="user-email_email">
                        <?= $user['email']; ?>
                    </span>
                </p>
                <a class="logout" href="/scenarios/logout.php"></a>
        <?php else: ?>
                <a class="login-button" href="#">
                    Войти
                </a>
        <?php endif; ?>
            </nav>
        </header>

        <main>
            <form id="url_form" method="post">
                <h2>Сократить ссылку</h2>
                <label>
                    Ваш URL:
                    <input id="full_url" type="text" name="full_url">
                    <button class="submit-button" type="submit">
                        Отправить
                    </button>
                </label>
            </form>

            <div class="result_wrapper">
                <label>Короткая ссылка:
                    <input class="result_link" type="text" id="short_url" name="short_url" placeholder="http://purl/" disabled readonly>
                    <button type="button" class="clipboard-button">
                </label>
            </div>

        <?php if ($user): ?>
            <table class="result_table">
                <thead>
                    <tr>
                        <th>Короткая ссылка</th>
                        <th>Полный адрес ссылки</th>
                        <th>Кол-во переходов</th>
                    </tr>
                </thead>
                <tbody>
            <?php foreach ($urls as $url): ?>
                    <tr>
                        <td>
                            <a href="<?= 'http://purl/' . $url['token']; ?>" target="_blank"><?= 'http://purl/' . $url['token']; ?></a>
                        </td>
                        <td><?= $url['full_url']; ?></td>
                        <td><?= $url['view_count']; ?></td>
                    </tr>
            <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>

    <div class="modal hidden-section">
        <form class="auth-form" method="post" id="login_form">
            <h2>Войти или зарегистрироваться:</h2>
            <label>
                E-Mail
                <input type="email" name="email" required>
            </label>

            <label>
                Пароль
                <input class="input-password" type="password" name="password" required>
            </label>

            <button type="submit">
                Отправить
            </button>
            <span class="close-button"></span>
        </form>
    </div>
    <script src="/src/index.js"></script>
    </body>
</html>
