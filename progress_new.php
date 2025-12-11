<?php
require_once 'config/db.php';
require_once 'includes/auth.php';
require_login();

$message = "";

// default form values
$log_date        = date('Y-m-d');
$mood            = "";
$focus           = "";
$weight          = "";
$strength_volume = "";
$cardio_minutes  = "";
$rpe             = "";
$notes           = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = current_user_id();

    // grab inputs
    $log_date        = $_POST['log_date'] ?? date('Y-m-d');
    $mood            = trim($_POST['mood'] ?? '');
    $focus           = trim($_POST['focus'] ?? '');
    $weight          = trim($_POST['weight_lb'] ?? '');
    $strength_volume = trim($_POST['strength_volume'] ?? '');
    $cardio_minutes  = trim($_POST['cardio_minutes'] ?? '');
    $rpe             = trim($_POST['rpe'] ?? '');
    $notes           = trim($_POST['notes'] ?? '');

    // basic required fields
    if ($mood === "" || $focus === "" || $weight === "" || $rpe === "") {
        $message = "Please fill in at least Mood, Focus, Weight, and RPE.";
    } elseif (!is_numeric($weight) || ($strength_volume !== "" && !is_numeric($strength_volume)) ||
              ($cardio_minutes !== "" && !is_numeric($cardio_minutes)) ||
              ($rpe !== "" && !is_numeric($rpe))) {
        $message = "Weight, strength volume, cardio minutes, and RPE must be numeric where entered.";
    } else {
        // convert numeric fields
        $weight_float       = (float)$weight;
        $strength_int       = ($strength_volume === "") ? 0 : (int)$strength_volume;
        $cardio_int         = ($cardio_minutes === "") ? 0 : (int)$cardio_minutes;
        $rpe_int            = ($rpe === "") ? 0 : (int)$rpe;

        $query = "INSERT INTO progress_logs
            (user_id, log_date, mood, focus, weight_lb, strength_volume, cardio_minutes, rpe, notes, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $mysqli->prepare($query);
        if ($stmt) {
            $stmt->bind_param(
                "isssdiiis",
                $user_id,
                $log_date,
                $mood,
                $focus,
                $weight_float,
                $strength_int,
                $cardio_int,
                $rpe_int,
                $notes
            );

            if ($stmt->execute()) {
                $message = "Progress logged successfully!";
                // reset fields (keep the date)
                $mood = $focus = $weight = $strength_volume = $cardio_minutes = $rpe = $notes = "";
            } else {
                $message = "Error saving data: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Database error: " . $mysqli->error;
        }
    }
}

include 'includes/header.php';
?>

<div class="page-card">
    <h2>Log Your Workout Progress</h2>

    <?php if ($message): ?>
        <p class="flash-message">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="progress_new.php" class="form-container">
        <label for="log_date">Date*</label>
        <input type="date" id="log_date" name="log_date"
               value="<?php echo htmlspecialchars($log_date); ?>" required>

        <label for="mood">Mood*</label>
        <select id="mood" name="mood" required>
            <option value="">-- Select Mood --</option>
            <option value="Great"    <?php if ($mood === "Great")    echo "selected"; ?>>Great</option>
            <option value="Good"     <?php if ($mood === "Good")     echo "selected"; ?>>Good</option>
            <option value="Okay"     <?php if ($mood === "Okay")     echo "selected"; ?>>Okay</option>
            <option value="Tired"    <?php if ($mood === "Tired")    echo "selected"; ?>>Tired</option>
            <option value="Stressed" <?php if ($mood === "Stressed") echo "selected"; ?>>Stressed</option>
        </select>

        <label for="focus">Workout Focus*</label>
        <select id="focus" name="focus" required>
            <option value="">-- Select Focus --</option>
            <option value="Strength"    <?php if ($focus === "Strength")    echo "selected"; ?>>Strength</option>
            <option value="Hypertrophy" <?php if ($focus === "Hypertrophy") echo "selected"; ?>>Hypertrophy</option>
            <option value="Cardio"      <?php if ($focus === "Cardio")      echo "selected"; ?>>Cardio</option>
            <option value="Recovery"    <?php if ($focus === "Recovery")    echo "selected"; ?>>Recovery</option>
            <option value="Mixed"       <?php if ($focus === "Mixed")       echo "selected"; ?>>Mixed</option>
        </select>

        <label for="weight_lb">Body Weight (lbs)*</label>
        <input type="number" step="0.1" id="weight_lb" name="weight_lb"
               value="<?php echo htmlspecialchars($weight); ?>" required>

        <label for="strength_volume">Strength Volume (total reps × sets)</label>
        <input type="number" id="strength_volume" name="strength_volume"
               value="<?php echo htmlspecialchars($strength_volume); ?>">

        <label for="cardio_minutes">Cardio Minutes</label>
        <input type="number" id="cardio_minutes" name="cardio_minutes"
               value="<?php echo htmlspecialchars($cardio_minutes); ?>">

        <label for="rpe">RPE (Rate of Perceived Exertion 1–10)*</label>
        <input type="number" id="rpe" name="rpe" min="1" max="10"
               value="<?php echo htmlspecialchars($rpe); ?>" required>

        <label for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="3"><?php echo htmlspecialchars($notes); ?></textarea>

        <button type="submit">Save Progress</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>

