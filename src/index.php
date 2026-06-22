<?php
// src/index.php

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Запазваме атрибутите 'recipe_name' и 'instructions' от HTML формата,
    // но ги записваме в новите колони на таблица `destinations`
    $destination_name = trim($_POST['recipe_name']);
    $description = trim($_POST['instructions']);

    if (!empty($destination_name) && !empty($description)) {
        $stmt = $pdo->prepare("
            INSERT INTO destinations (destination_name, description)
            VALUES (?, ?)
        ");
        $stmt->execute([$destination_name, $description]);
    }

    header("Location: index.php");
    exit;
}

try {
    // Селектираме от новата таблица
    $recipes = $pdo->query("
        SELECT id, destination_name AS recipe_name, description AS instructions, created_at
        FROM destinations
        ORDER BY created_at DESC
    ")->fetchAll();
} catch (PDOException $e) {
    $recipes = [];
    $error_msg = "Неуспешно извличане на дестинациите.";
}
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Приключенец - Сподели локация за следващото пътуване</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>

<div class="container">

    <h1>✈️ Моят Пътеводител</h1>
    <p class="subtitle">Споделете скрито кътче или намерете вдъхновение за следващото приключение</p>

    <form method="POST" action="index.php">
        <input
            type="text"
            name="recipe_name"
            placeholder="Име на дестинацията (напр. Седемте рилски езера)"
            required
        >

        <textarea
            name="instructions"
            placeholder="Как да стигнем, какво да видим и полезни съвети за мястото..."
            required
        ></textarea>

        <button type="submit">Добави Дестинация</button>
    </form>

    <div class="recipes-feed">
        <?php if (isset($error_msg)): ?>
            <div class="error-msg"><?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <?php if (empty($recipes)): ?>
            <p class="no-data">Все още няма добавени дестинации. Бъдете първия пътешественик!</p>
        <?php else: ?>
            <?php foreach($recipes as $recipe): ?>
                <div class="recipe-card">
                    <h3>📍 <?= htmlspecialchars($recipe['recipe_name']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($recipe['instructions'])) ?></p>
                    <?php if (!empty($recipe['created_at'])): ?>
                        <span class="recipe-meta">Споделено на: <?= htmlspecialchars($recipe['created_at']) ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
