<?php
global $pdo;
include 'database.php';
$sql = "SELECT * FROM news ORDER BY RAND() LIMIT 10";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$newsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

$newsPerPage = 5;

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) {
    $currentPage = 1;
}

$offset = ($currentPage - 1) * $newsPerPage;

$totalNews = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
$totalPages = ceil($totalNews / $newsPerPage);

$sql = "SELECT * FROM news ORDER BY RAND() LIMIT :offset, :limit";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $newsPerPage, PDO::PARAM_INT);
$stmt->execute();
$newsList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Новости</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
        }

        .news-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .news-item {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 15px;
            width: 300px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .news-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .news-item h2 {
            font-size: 20px;
            margin: 15px;
        }

        .news-item p {
            margin: 15px;
            color: #555;
        }

        .news-item .date {
            font-size: 14px;
            color: #999;
            margin: 15px;
        }

        .pagination {
            text-align: center;
            margin: 20px 0;
        }

        .pagination a {
            display: inline-block;
            margin: 0 5px;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .pagination a.active {
            background-color: #333;
            color: #fff;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        @media (max-width: 768px) {
            .news-item {
                width: 90%;
            }
        }
    </style>
</head>

<body>
    <h1>Последние новости</h1>
    <div class="news-container">
        <?php foreach ($newsList as $news): ?>
            <div class="news-item">
                <img src="<?php echo htmlspecialchars($news['image']); ?>" alt="<?php echo htmlspecialchars($news['title']); ?>">
                <h2><?php echo htmlspecialchars($news['title']); ?></h2>
                <p><?php echo htmlspecialchars($news['content']); ?></p>
                <p class="date"><?php echo date("d.m.Y H:i", strtotime($news['publication_date'])); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination">
        <?php if ($currentPage > 1): ?>
            <a href="?page=<?php echo $currentPage - 1; ?>">&laquo; Предыдущая</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $currentPage ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?php echo $currentPage + 1; ?>">Следующая &raquo;</a>
        <?php endif; ?>
    </div>

</body>

</html>