<?php
// Create a new workout plan

require_once 'config/db.php';
require_once 'includes/auth.php';
require_login();

$user_id = current_user_id();

$valid_difficulties = ['Beginner', 'Intermediate', 'Advanced'];
$valid_targets      = ['Full Body', 'Upper Body', 'Lower Body', 'Core'];

$name          = '';
$difficulty    = '';
$target_muscle = '';
$errors        = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name          = trim($_POST['name'] ?? '');
    $difficulty    = $_POST['difficulty'] ?? '';
    $target_muscle = $_POST['target_muscle'] ?? '';

    // very simple validation
    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if (!in_array($difficulty, $valid_difficulties, true)) {
        $errors[] = 'Please choose a valid difficulty.';
    }

    if (!in_array($target_muscle, $valid_targets, true)) {
        $errors[] = 'Please choose a valid target muscle group.';
    }

    if (empty($errors)) {
        $stmt = $mysqli->prepare(
            "INSERT INTO workout_plans (user_id, name, difficulty, target_muscle)
             VALUES (?, ?, ?, ?)"
        );

        if (!$stmt) {
            die("Insert failed: " . $mysqli->error);
        }

        $stmt->bind_param('isss', $user_id, $name, $difficulty, $target_muscle);
        $stmt->execute();
        $stmt->close();

        // go back to main workouts page after saving
        header('Location: workouts.php');
        exit;
    }
}

include 'includes/header.php';
?>

<div class="page-card">
    <h2>Create Workout Plan</h2>

    <p style="margin-bottom: 10px;">
        <a href="workouts.php">&larr; Back to My Workout Plans</a>
    </p>

    <?php if (!empty($errors)): ?>
        <div class="flash-message" style="color: #b30000;">
            <ul style="margin: 0; padding-left: 18px; text-align:left;">
                <?php foreach ($errors as $e): ?>
                    <li><?php echo htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" class="form-container">
        <label for="name">Name*</label>
        <input
            type="text"
            id="name"
            name="name"
            value="<?php echo htmlspecialchars($name); ?>"
            placeholder="e.g. Push Day A"
        >

        <label for="difficulty">Difficulty*</label>
        <select id="difficulty" name="difficulty">
            <option value="">Select difficulty</option>
            <?php foreach ($valid_difficulties as $d): ?>
                <option value="<?php echo $d; ?>"
                    <?php if ($difficulty === $d) echo 'selected'; ?>>
                    <?php echo $d; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="target_muscle">Target Muscle Group*</label>
        <select id="target_muscle" name="target_muscle">
            <option value="">Select target</option>
            <?php foreach ($valid_targets as $t): ?>
                <option value="<?php echo $t; ?>"
                    <?php if ($target_muscle === $t) echo 'selected'; ?>>
                    <?php echo $t; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Save Workout Plan</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
