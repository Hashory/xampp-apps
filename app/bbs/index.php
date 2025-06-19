<?php
$host = 'db';
$db   = 'bbs';
$user = 'bbsuser';
$pass = 'bbspass';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// 投稿処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name']) && !empty($_POST['message'])) {
    $stmt = $pdo->prepare('INSERT INTO posts (name, message) VALUES (?, ?)');
    $stmt->execute([$_POST['name'], $_POST['message']]);
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// 投稿一覧取得
$posts = $pdo->query('SELECT * FROM posts ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>シンプル掲示板</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="container">
    <h1>シンプル掲示板</h1>
    <form method="post">
        名前: <input type="text" name="name" required>
        <br>
        メッセージ: <input type="text" name="message" required>
        <br>
        <button type="submit">投稿</button>
    </form>
    <hr>
    <h2>投稿一覧</h2>
    <?php foreach ($posts as $post): ?>
        <div>
            <strong><?php echo htmlspecialchars($post['name']); ?></strong>:
            <?php echo nl2br(htmlspecialchars($post['message'])); ?>
        </div>
        <hr>
    <?php endforeach; ?>
    <p><a href="/">ポータルに戻る</a></p>
</div>
</body>
</html>
