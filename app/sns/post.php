<?php
// 投稿詳細ページ
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
// 投稿取得
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT * FROM sns_posts WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) {
    http_response_code(404);
    echo '投稿が見つかりません';
    exit;
}
// 返信追加
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply']) && $_POST['reply'] !== '') {
    $stmt = $pdo->prepare('INSERT INTO sns_replies (post_id, content) VALUES (?, ?)');
    $stmt->execute([$id, $_POST['reply']]);
    header('Location: post.php?id=' . $id);
    exit;
}
// 返信一覧取得
$stmt = $pdo->prepare('SELECT * FROM sns_replies WHERE post_id = ? ORDER BY id ASC');
$stmt->execute([$id]);
$replies = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>投稿 #<?php echo $post['id']; ?> の詳細</title>
</head>
<body>
    <h1>投稿 #<?php echo $post['id']; ?> の詳細</h1>
    <div>
        <strong>投稿内容:</strong><br>
        <?php echo nl2br(htmlspecialchars($post['content'])); ?><br>
        <small>(<?php echo $post['created_at']; ?>)</small>
    </div>
    <hr>
    <h2>返信</h2>
    <form method="post">
        <input type="text" name="reply" placeholder="返信を書く" required>
        <button type="submit">返信</button>
    </form>
    <ul>
        <?php foreach ($replies as $reply): ?>
            <li><?php echo nl2br(htmlspecialchars($reply['content'])); ?> <small>(<?php echo $reply['created_at']; ?>)</small></li>
        <?php endforeach; ?>
    </ul>
    <p><a href="list.php">投稿一覧に戻る</a></p>
</body>
</html>
