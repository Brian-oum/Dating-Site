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
$gender = $user['gender'] ?? '';
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
    <style>
        .profile-wrapper {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: #2d3748;
            font-size: 1.8rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4a5568;
            font-weight: 500;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        select,
        textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
        }

        .profile-photo-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .profile-photo-preview {
            width: 120px;
            height: 120px;
            margin: 0 auto 1rem;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e2e8f0;
        }

        .photo-upload-label {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #4299e1;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .photo-upload-label:hover {
            background-color: #3182ce;
        }

        .photo-upload-input {
            display: none;
        }

        .toggle-switch {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .toggle-switch input[type="checkbox"] {
            display: none;
        }

        .toggle-label {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
            background-color: #e2e8f0;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .toggle-label:after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: white;
            top: 2px;
            left: 2px;
            transition: all 0.3s;
        }

        .toggle-switch input[type="checkbox"]:checked + .toggle-label {
            background-color: #4299e1;
        }

        .toggle-switch input[type="checkbox"]:checked + .toggle-label:after {
            left: calc(100% - 22px);
        }

        button[type="submit"] {
            width: 100%;
            padding: 0.75rem;
            background-color: #4299e1;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button[type="submit"]:hover {
            background-color: #3182ce;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }
    </style>
</head>
<body>
    <div class="profile-wrapper">
        <h2>User Profile</h2>
        <form id="profileForm" method="POST" action="save_profile.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $id ?>">

            <div class="form-group profile-photo-container">
                <?php if (!empty($profile_photo) && file_exists("../$profile_photo")): ?>
                    <img src="../<?= $profile_photo ?>" class="profile-photo-preview" id="photoPreview">
                <?php else: ?>
                    <div class="profile-photo-preview" id="photoPreview" style="background-color: #e2e8f0; display: flex; align-items: center; justify-content: center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="#718096">
                            <path d="M9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9zm3 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>
                <?php endif; ?>
                <label class="photo-upload-label">
                    Choose Photo
                    <input type="file" name="profile_photo" class="photo-upload-input" id="photoUpload" accept="image/*">
                </label>
            </div>

            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?= $first_name ?>" placeholder="Enter your first name">
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?= $last_name ?>" placeholder="Enter your last name">
                <div class="toggle-switch">
                    <input type="checkbox" id="show_last_name" name="show_last_name" <?= $show_last_name ?>>
                    <label for="show_last_name" class="toggle-label"></label>
                    <span>Show Last Name</span>
                </div>
            </div>

            <div class="form-group">
                <label for="age">Age</label>
                <input type="number" id="age" name="age" value="<?= $age ?>" placeholder="Enter your age" min="1" max="120">
            </div>

            <div class="form-group">
               <label for="location">Location</label>
               <input type="text" id="location" name="location" value="<?= htmlspecialchars($location ?? 'Not Set') ?>" placeholder="Enter your location">
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender">
                    <option value="Male" <?= $gender_options['Male'] ?>>Male</option>
                    <option value="Female" <?= $gender_options['Female'] ?>>Female</option>
                    <option value="Other" <?= $gender_options['Other'] ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="bio">About Me</label>
                <textarea id="bio" name="bio" placeholder="Tell us about yourself"><?= $bio ?></textarea>
            </div>

            <button type="submit">Save Profile</button>
        </form>
    </div>

    <script>
        // Preview uploaded photo
        document.getElementById('photoUpload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('photoPreview');
                    preview.innerHTML = '';
                    preview.style.backgroundImage = 'none';
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.className = 'profile-photo-preview';
                    preview.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>git