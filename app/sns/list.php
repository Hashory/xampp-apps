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
// 投稿追加
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content']) && $_POST['content'] !== '') {
    $stmt = $pdo->prepare('INSERT INTO sns_posts (content) VALUES (?)');
    $stmt->execute([$_POST['content']]);
    header('Location: list.php');
    exit;
}
$posts = $pdo->query('SELECT * FROM sns_posts ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>SNS 投稿一覧</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="container">
    <h1>SNS 投稿一覧</h1>
    <form method="post">
        <input type="text" name="content" placeholder="新しい投稿" required>
        <button type="submit">投稿</button>
    </form>
    <hr>
    <ul>
        <?php foreach ($posts as $post): ?>
            <li>
                <a href="post.php?id=<?php echo $post['id']; ?>">投稿 #<?php echo $post['id']; ?></a>:
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                <small>(<?php echo $post['created_at']; ?>)</small>
            </li>
        <?php endforeach; ?>
    </ul>
    <p><a href="/">アプリ一覧に戻る</a></p>
</div>
</body>
</html>
