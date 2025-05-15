<?php
include '../config/db.php';
session_start();

// Assign variables and escape for HTML use
$id = htmlspecialchars($user['id'] ?? '');
$profile_photo = htmlspecialchars($user['profile_photo'] ?? '');
$first_name = htmlspecialchars($user['first_name'] ?? '');
$last_name = htmlspecialchars($user['last_name'] ?? '');
$show_last_name = !empty($user['show_last_name']) ? 'checked' : '';
$age = htmlspecialchars($user['age'] ?? '');
$location = htmlspecialchars($user['location'] ?? '');
$gender = $user['gender'] ?? '';
$orientation = htmlspecialchars($user['orientation'] ?? '');
$interests = htmlspecialchars($user['interests'] ?? '');
$bio = htmlspecialchars($user['bio'] ?? '');
// Determine which gender and relationship goal are selected
$gender_options = [
    'Male' => $gender === 'Male' ? 'selected' : '',
    'Female' => $gender === 'Female' ? 'selected' : '',
    'Other' => $gender === 'Other' ? 'selected' : ''
];

?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
 <div class="profile-wrapper">
  <h2>User Profile</h2>
  <form id="profileForm" method="POST" action="save_profile.php" enctype="multipart/form-data">
    <!-- your form fields here -->
     <form id="profileForm" method="POST" action="save_profile.php" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $id ?>">

    <label>Profile Photo:</label>
    <input type="file" name="profile_photo"><br>
    <?php if (!empty($profile_photo) && file_exists("../$profile_photo")): ?>
        <img src="../<?= $profile_photo ?>" width="100"><br>
    <?php endif; ?>

    <label>First Name:</label>
    <input type="text" name="first_name" value="<?= $first_name ?>"><br>

    <label>Last Name:</label>
    <input type="text" name="last_name" value="<?= $last_name ?>"><br>

    <label><input type="checkbox" name="show_last_name" <?= $show_last_name ?>> Show Last Name</label><br>

    <label>Age:</label>
    <input type="number" name="age" value="<?= $age ?>"><br>

    <label>Location:</label>
    <input type="text" name="location" value="<?= $location ?>"><br>

    <label>Gender:</label>
    <select name="gender">
        <option value="Male" <?= $gender_options['Male'] ?>>Male</option>
        <option value="Female" <?= $gender_options['Female'] ?>>Female</option>
        <option value="Other" <?= $gender_options['Other'] ?>>Other</option>
    </select><br>

    <label>Orientation:</label>
    <input type="text" name="orientation" value="<?= $orientation ?>"><br>

    <label>Interests (comma separated):</label>
    <input type="text" name="interests" value="<?= $interests ?>"><br>

    <label>About Me:</label>
    <textarea name="bio"><?= $bio ?></textarea><br>

    <button type="submit">Save Profile</button>
  </form>
</div>
</body>
</html>
