<?php
session_start();
require_once './config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Get current preferences
try {
    $stmt = $db->prepare("SELECT * FROM user_preferences WHERE user_id = :userId");
    $stmt->execute([':userId' => $userId]);
    $preferences = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Error getting preferences: " . $e->getMessage());
    $preferences = null;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $minAge = (int)$_POST['min_age'];
        $maxAge = (int)$_POST['max_age'];
        $genderPref = $_POST['gender_preference'] ?? null;
        $locationPref = $_POST['location_preference'] ?? null;
        
        // Validate ages
        if ($minAge < 18) $minAge = 18;
        if ($maxAge > 120) $maxAge = 120;
        if ($minAge > $maxAge) $minAge = $maxAge;
        
        // Update or insert preferences
        $stmt = $db->prepare(
            "INSERT INTO user_preferences (user_id, min_age, max_age, gender_preference, location_preference) 
             VALUES (:userId, :minAge, :maxAge, :genderPref, :locationPref)
             ON DUPLICATE KEY UPDATE 
                min_age = :minAge2,
                max_age = :maxAge2,
                gender_preference = :genderPref2,
                location_preference = :locationPref2"
        );
        
        $stmt->execute([
            ':userId' => $userId,
            ':minAge' => $minAge,
            ':maxAge' => $maxAge,
            ':genderPref' => $genderPref,
            ':locationPref' => $locationPref,
            ':minAge2' => $minAge,
            ':maxAge2' => $maxAge,
            ':genderPref2' => $genderPref,
            ':locationPref2' => $locationPref
        ]);
        
        $_SESSION['message'] = "Preferences updated successfully!";
        header("Location: preferences.php");
        exit();
    } catch (PDOException $e) {
        error_log("Error updating preferences: " . $e->getMessage());
        $_SESSION['message'] = "Error updating preferences. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AfroLove - Preferences</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .preferences-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .preferences-form .form-group {
            margin-bottom: 20px;
        }
        
        .age-range-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .age-range-display {
            font-weight: 500;
            min-width: 100px;
            text-align: center;
        }
        
        input[type="range"] {
            flex-grow: 1;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="preferences-container">
        <h1>Your Preferences</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert-message"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <form class="preferences-form" method="POST">
            <div class="form-group">
                <label>Age Range</label>
                <div class="age-range-container">
                    <input type="range" id="min_age" name="min_age" min="18" max="120" 
                           value="<?= htmlspecialchars($preferences['min_age'] ?? 18) ?>">
                    <span class="age-range-display" id="ageRangeDisplay">
                        <?= htmlspecialchars($preferences['min_age'] ?? 18) ?> - <?= htmlspecialchars($preferences['max_age'] ?? 99) ?>
                    </span>
                    <input type="range" id="max_age" name="max_age" min="18" max="120" 
                           value="<?= htmlspecialchars($preferences['max_age'] ?? 99) ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="gender_preference">Gender Preference</label>
                <select id="gender_preference" name="gender_preference" class="form-control">
                    <option value="">No preference</option>
                    <option value="Male" <?= ($preferences['gender_preference'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= ($preferences['gender_preference'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= ($preferences['gender_preference'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="location_preference">Location Preference</label>
                <input type="text" id="location_preference" name="location_preference" 
                       value="<?= htmlspecialchars($preferences['location_preference'] ?? '') ?>" 
                       placeholder="Preferred location (city or country)">
            </div>
            
            <button type="submit" class="btn btn-primary">Save Preferences</button>
        </form>
    </div>
    
    <script>
         // Update age range display when sliders change
        const minAgeSlider = document.getElementById('min_age');
        const maxAgeSlider = document.getElementById('max_age');
        const ageRangeDisplay = document.getElementById('ageRangeDisplay');
        
        function updateAgeDisplay() {
            const minAge = minAgeSlider.value;
            const maxAge = maxAgeSlider.value;
            
            // Ensure min doesn't exceed max
            if (parseInt(minAge) > parseInt(maxAge)) {
                minAgeSlider.value = maxAge;
            }
            
            ageRangeDisplay.textContent = `${minAgeSlider.value} - ${maxAgeSlider.value}`;
        }
        
        minAgeSlider.addEventListener('input', updateAgeDisplay);
        maxAgeSlider.addEventListener('input', updateAgeDisplay);
    </script>
</body>
</html>