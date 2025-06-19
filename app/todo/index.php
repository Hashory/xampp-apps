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

// タスク追加
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task']) && $_POST['task'] !== '') {
    $stmt = $pdo->prepare('INSERT INTO todos (task) VALUES (?)');
    $stmt->execute([$_POST['task']]);
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// タスク完了/未完了切り替え
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare('UPDATE todos SET is_done = 1 - is_done WHERE id = ?')->execute([$id]);
    header('Location: ./');
    exit;
}

// タスク削除
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare('DELETE FROM todos WHERE id = ?')->execute([$id]);
    header('Location: ./');
    exit;
}

// タスク一覧取得
$todos = $pdo->query('SELECT * FROM todos ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>TODOリスト</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="container">
    <h1>TODOリスト</h1>
    <form method="post">
        <input type="text" name="task" placeholder="新しいタスク" required>
        <button type="submit">追加</button>
    </form>
    <hr>
    <ul>
        <?php foreach ($todos as $todo): ?>
            <li>
                <form method="get" style="display:inline">
                    <button type="submit" name="toggle" value="<?php echo $todo['id']; ?>">
                        <?php echo $todo['is_done'] ? '☑' : '☐'; ?>
                    </button>
                </form>
                <span style="<?php echo $todo['is_done'] ? 'text-decoration:line-through;' : ''; ?>">
                    <?php echo htmlspecialchars($todo['task']); ?>
                </span>
                <a href="?delete=<?php echo $todo['id']; ?>" onclick="return confirm('削除しますか？');">削除</a>
            </li>
        <?php endforeach; ?>
    </ul>
    <p><a href="/">アプリ一覧に戻る</a></p>
</div>
</body>
</html>
