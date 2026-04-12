<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("ID not found");
}

$id = $_GET['id'];

// FETCH QUESTION
$stmt = $pdo->prepare("SELECT * FROM questions WHERE id=?");
$stmt->execute([$id]);
$question = $stmt->fetch();

// FETCH OPTIONS
$stmtOpt = $pdo->prepare("SELECT * FROM options WHERE question_id=?");
$stmtOpt->execute([$id]);
$options = $stmtOpt->fetchAll();

// UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $q = $_POST['question'];
    $opts = $_POST['options'];
    $correct = $_POST['correct'];

    // update question
    $pdo->prepare("UPDATE questions SET question=? WHERE id=?")
        ->execute([$q, $id]);

    // update options
    foreach ($opts as $opt_id => $text) {
        $is_correct = ($correct == $opt_id) ? 1 : 0;

        $pdo->prepare("UPDATE options SET option_text=?, is_correct=? WHERE id=?")
            ->execute([$text, $is_correct, $opt_id]);
    }

    header("Location: manage_questions.php?updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Question</title>

<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
/* reuse same root */
:root {
    --primary: #1d4ed8;
    --sidebar: #0f172a;
    --bg: #f1f5f9;
    --white: #ffffff;
    --border: #e5e7eb;
    --text: #111827;
    --text-light: #6b7280;
    --success: #16a34a;
}

/* layout */
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: var(--bg);
    display: flex;
    height: 100vh;
}

/* sidebar same */
.nav-bar {
    width: 240px;
    background: var(--sidebar);
    display: flex;
    flex-direction: column;
    padding: 20px 10px;
}

.nav-bar button {
    background: none;
    border: none;
    color: #cbd5f5;
    text-align: left;
    padding: 12px 15px;
    margin: 5px 0;
    border-radius: 8px;
    cursor: pointer;
}

.nav-bar button:hover {
    background: #1e293b;
    color: white;
}

.nav-bar .active {
    background: var(--primary);
    color: white;
}

/* content */
.container {
    flex: 1;
    padding: 30px;
}

/* card */
.card {
    background: var(--white);
    padding: 25px;
    border-radius: 12px;
    border: 1px solid var(--border);
    max-width: 700px;
}

/* form */
label {
    display: block;
    margin-top: 15px;
    font-weight: 500;
}

input[type="text"] {
    width: 100%;
    padding: 10px;
    margin-top: 6px;
    border-radius: 6px;
    border: 1px solid var(--border);
}

.option {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 10px;
}

.option input[type="text"] {
    flex: 1;
}

button {
    margin-top: 20px;
    padding: 12px;
    width: 100%;
    border: none;
    background: var(--success);
    color: white;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="nav-bar">

    <button onclick="window.location.href='admin_panel.php'">
        <i class="fas fa-user-cog"></i> Admin Panel
    </button>

    <button onclick="window.location.href='dashboard.php'">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </button>

    <button onclick="window.location.href='add_question.php'">
        <i class="fas fa-question-circle"></i> Add Question
    </button>

    <button class="active">
        <i class="fas fa-edit"></i> Edit Question
    </button>

    <button onclick="window.location.href='manage_questions.php'">
        <i class="fas fa-list"></i> Manage Questions
    </button>

</div>

<!-- CONTENT -->
<div class="container">

    <h2><i class="fas fa-edit"></i> Edit Question</h2>

    <div class="card">

        <form method="POST">

            <label>Question</label>
            <input type="text" name="question"
                   value="<?= htmlspecialchars($question['question']) ?>" required>

            <h3>Options</h3>

            <?php foreach ($options as $opt): ?>
                <div class="option">
                    <input type="text"
                           name="options[<?= $opt['id'] ?>]"
                           value="<?= htmlspecialchars($opt['option_text']) ?>" required>

                    <input type="radio"
                           name="correct"
                           value="<?= $opt['id'] ?>"
                           <?= $opt['is_correct'] ? 'checked' : '' ?>>
                </div>
            <?php endforeach; ?>

            <button type="submit">
                <i class="fas fa-save"></i> Update Question
            </button>

        </form>

    </div>

</div>

</body>
</html>