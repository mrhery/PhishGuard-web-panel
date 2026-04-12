<?php
include 'config.php';

$score = 0;
$total = 0;

// ambil jawapan betul
$stmt = $pdo->query("SELECT * FROM questions");

$i = 0;

while ($row = $stmt->fetch()) {

    $opt = $pdo->prepare("SELECT * FROM options WHERE question_id=?");
    $opt->execute([$row['id']]);

    $correct_index = null;
    $index = 0;

    while ($o = $opt->fetch()) {
        if ($o['is_correct'] == 1) {
            $correct_index = $index;
        }
        $index++;
    }

    if (isset($_POST["q$i"])) {
        if ($_POST["q$i"] == $correct_index) {
            $score++;
        }
    }

    $total++;
    $i++;
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Result</title>

<style>
body {
    font-family: 'Segoe UI';
    text-align: center;
    margin-top: 100px;
}
</style>
</head>

<body>

<h1>Quiz Result</h1>

<h2><?= $score ?> / <?= $total ?></h2>

<?php
$percentage = ($score / $total) * 100;

if ($percentage >= 70) {
    echo "<h3 style='color:green;'>Low Risk</h3>";
} else {
    echo "<h3 style='color:red;'>High Risk</h3>";
}
?>

<br><br>
<a href="phishing_quiz.php">Try Again</a>

</body>
</html>