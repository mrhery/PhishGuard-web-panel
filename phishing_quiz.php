<?php
include 'config.php';

$questions = [];

$stmt = $pdo->query("SELECT * FROM questions");

while ($row = $stmt->fetch()) {

    $q = [
        'id' => $row['id'],
        'question' => $row['question'],
        'options' => []
    ];

    $opt = $pdo->prepare("SELECT * FROM options WHERE question_id=?");
    $opt->execute([$row['id']]);

    while ($o = $opt->fetch()) {
        $q['options'][] = $o['option_text'];
    }

    $questions[] = $q;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Quiz</title>

<style>
body {
    font-family: 'Segoe UI';
    background: #f1f5f9;
}

.container {
    max-width: 800px;
    margin: auto;
    padding: 20px;
}

.question {
    background: white;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
}

button {
    width: 100%;
    padding: 12px;
    background: #1d4ed8;
    color: white;
    border: none;
    border-radius: 8px;
}
</style>
</head>

<body>

<div class="container">
<h1>Security Awareness Quiz</h1>

<form action="submit_quiz.php" method="POST">

<?php foreach ($questions as $i => $q): ?>
<div class="question">

<h3><?= ($i+1) ?>. <?= $q['question'] ?></h3>

<?php foreach ($q['options'] as $j => $opt): ?>
<label>
<input type="radio" name="q<?= $i ?>" value="<?= $j ?>" required>
<?= $opt ?>
</label><br>
<?php endforeach; ?>

</div>
<?php endforeach; ?>

<button type="submit">Submit Quiz</button>

</form>

</div>

</body>
</html>