<?php
require_once 'config/db.php';
require_once 'includes/auth.php';
require_login();

$user_id = current_user_id();

// ----- Handle delete request -----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_log_id'])) {
    $delete_id     = (int) $_POST['delete_log_id'];
    $current_focus = $_POST['current_focus'] ?? 'All';

    if ($delete_id > 0) {
        // NOTE: if your PK is not log_id, change log_id to your real column name
        $del = $mysqli->prepare("
            DELETE FROM progress_logs
            WHERE log_id = ? AND user_id = ?
        ");
        if ($del) {
            $del->bind_param('ii', $delete_id, $user_id);
            $del->execute();
            $del->close();
        }
    }

    // Redirect back, preserving the current focus filter
    header('Location: progress_list.php?focus=' . urlencode($current_focus));
    exit;
}

// ----- Focus filter (from dropdown) -----
$selected_focus = $_GET['focus'] ?? 'All';

// build query
$sql = "SELECT 
            log_id,            -- <== PK, used for delete (change if needed)
            log_date, 
            mood, 
            focus, 
            weight_lb, 
            strength_volume, 
            cardio_minutes, 
            rpe, 
            notes
        FROM progress_logs
        WHERE user_id = ?";

$params = [$user_id];
$types  = "i";

if ($selected_focus !== "All" && $selected_focus !== "") {
    $sql .= " AND focus = ?";
    $params[] = $selected_focus;
    $types   .= "s";
}

$sql .= " ORDER BY log_date DESC, created_at DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

include 'includes/header.php';
?>

<div class="page-card wide">
    <h2>Progress History</h2>

    <form method="GET" action="progress_list.php" style="margin-bottom: 10px;">
        <label for="focus">Filter by focus: </label>
        <select name="focus" id="focus">
            <option value="All"         <?php if ($selected_focus === "All")         echo "selected"; ?>>All</option>
            <option value="Strength"    <?php if ($selected_focus === "Strength")    echo "selected"; ?>>Strength</option>
            <option value="Hypertrophy" <?php if ($selected_focus === "Hypertrophy") echo "selected"; ?>>Hypertrophy</option>
            <option value="Cardio"      <?php if ($selected_focus === "Cardio")      echo "selected"; ?>>Cardio</option>
            <option value="Recovery"    <?php if ($selected_focus === "Recovery")    echo "selected"; ?>>Recovery</option>
            <option value="Mixed"       <?php if ($selected_focus === "Mixed")       echo "selected"; ?>>Mixed</option>
        </select>
        <button type="submit">Apply</button>
    </form>

    <?php if ($result->num_rows === 0): ?>
        <p>No progress logs found yet.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Mood</th>
                    <th>Focus</th>
                    <th>Weight (lbs)</th>
                    <th>Strength Volume</th>
                    <th>Cardio (min)</th>
                    <th>RPE</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['log_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['mood']); ?></td>
                        <td><?php echo htmlspecialchars($row['focus']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($row['weight_lb'], 2)); ?></td>
                        <td><?php echo htmlspecialchars($row['strength_volume']); ?></td>
                        <td><?php echo htmlspecialchars($row['cardio_minutes']); ?></td>
                        <td><?php echo htmlspecialchars($row['rpe']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['notes'])); ?></td>
                        <td>
                            <form method="post" style="display:inline;"
                                  onsubmit="return confirm('Delete this progress entry?');">
                                <input type="hidden" name="delete_log_id"
                                       value="<?php echo (int)$row['log_id']; ?>">
                                <input type="hidden" name="current_focus"
                                       value="<?php echo htmlspecialchars($selected_focus); ?>">
                                <button type="submit" class="btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p class="card-note">
        Showing logs for your account<?php
            if ($selected_focus !== "All" && $selected_focus !== "") {
                echo " with focus <strong>" . htmlspecialchars($selected_focus) . "</strong>";
            }
        ?>.
    </p>
</div>

<?php
$stmt->close();
include 'includes/footer.php';
?>
