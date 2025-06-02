<?php
session_start();
require_once '../config/db.php'; // Your PDO connection should be in this file

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Join `account` and `users` tables
$stmt = $pdo->prepare("
    SELECT 
        a.username, a.email, 
        u.profile_photo, u.first_name, u.last_name, u.age, u.location, u.gender, u.bio
    FROM 
        account a
    JOIN 
        users u ON a.id = u.account_id
    WHERE 
        a.id = ?
");

$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Profile not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - AfroLove</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #5D3A9B;
            --secondary: #FF6B6B;
            --accent: #FFD166;
            --dark: #1A1A2E;
            --light: #F8F9FA;
            --text: #2D3436;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.12);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            color: var(--text);
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }
        
        .profile-container {
            width: 90%;
            max-width: 800px;
            margin: 3rem auto;
            padding: 3rem;
            background: #fff;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .profile-container:hover {
            transform: translateY(-5px);
        }
        
        .profile-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 10px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
        }
        
        .profile-picture-container {
            position: relative;
            width: 200px;
            height: 200px;
            margin: 0 auto 1.5rem;
        }
        
        .profile-picture-frame {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 8px solid white;
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: linear-gradient(45deg, #f3f4f6, #e5e7eb);
            position: relative;
            cursor: pointer;
        }
        
        .profile-picture {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .default-profile-icon {
            font-size: 5rem;
            color: var(--primary);
            transition: all 0.3s ease;
        }
        
        .profile-picture-frame:hover .profile-picture,
        .profile-picture-frame:hover .default-profile-icon {
            transform: scale(1.1);
        }
        
        .profile-picture-frame::after {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            border-radius: 50%;
            border: 2px dashed var(--accent);
            animation: rotate 20s linear infinite;
            pointer-events: none;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .profile-name {
            font-family: 'Playfair Display', serif;
            color: var(--primary);
            font-size: 2.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }
        
        .profile-location {
            color: var(--secondary);
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        
        .profile-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .info-section {
            background: var(--light);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
        }
        
        .section-title {
            font-family: 'Playfair Display', serif;
            color: var(--primary);
            font-size: 1.3rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(93, 58, 155, 0.1);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-item {
            margin-bottom: 1rem;
            display: flex;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--dark);
            min-width: 100px;
            display: inline-block;
        }
        
        .info-value {
            color: var(--text);
            flex: 1;
        }
        
        .bio-text {
            line-height: 1.8;
            color: var(--text);
        }
        
        .edit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 2.5rem;
            padding: 1rem 2.5rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(107, 61, 145, 0.3);
            border: none;
            cursor: pointer;
            font-size: 1rem;
            gap: 8px;
        }
        
        .edit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(107, 61, 145, 0.4);
            background: linear-gradient(45deg, var(--secondary), var(--primary));
        }
        
        .edit-btn:active {
            transform: translateY(1px);
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            position: relative;
        }

        .close-modal {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--primary);
        }

        .upload-btn {
            margin-top: 1rem;
            padding: 0.8rem 1.5rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .upload-btn:hover {
            background: var(--secondary);
        }

        /* Loading spinner */
        .spinner {
            display: none;
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top: 4px solid var(--primary);
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 1rem auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .profile-container {
                width: 95%;
                padding: 2rem 1.5rem;
            }
            
            .profile-picture-container {
                width: 160px;
                height: 160px;
            }
            
            .profile-name {
                font-size: 1.8rem;
            }
            
            .profile-info {
                grid-template-columns: 1fr;
            }
            
            .edit-btn {
                padding: 0.8rem 2rem;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-picture-container">
                <div class="profile-picture-frame" id="profilePictureFrame">
                    <?php if (!empty($user['profile_photo'])): ?>
                        <img src="<?= htmlspecialchars($user['profile_photo'], ENT_QUOTES, 'UTF-8') ?>" alt="Profile Picture" class="profile-picture" id="profilePicture">
                    <?php else: ?>
                        <i class="fas fa-user-circle default-profile-icon" id="defaultProfileIcon"></i>
                    <?php endif; ?>
                </div>
            </div>
            <h1 class="profile-name" id="profileName"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name'], ENT_QUOTES, 'UTF-8') ?></h1>
            <div class="profile-location">
                <i class="fas fa-map-marker-alt"></i>
                <span id="profileLocation"><?= htmlspecialchars($user['location'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>
        
        <div class="profile-info">
            <div class="info-section">
                <h3 class="section-title"><i class="fas fa-user-circle"></i> Basic Info</h3>
                <div class="info-item">
                    <span class="info-label">Username:</span>
                    <span class="info-value" id="profileUsername"><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value" id="profileEmail"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Age:</span>
                    <span class="info-value" id="profileAge"><?= htmlspecialchars($user['age'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Gender:</span>
                    <span class="info-value" id="profileGender"><?= htmlspecialchars($user['gender'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>
            
            <div class="info-section">
                <h3 class="section-title"><i class="fas fa-heart"></i> About Me</h3>
                <p class="bio-text" id="profileBio"><?= nl2br(htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
        </div>
        
        <center>
            <a href="./profile.php" class="edit-btn">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
        </center>
    </div>

    <!-- Profile Picture Upload Modal -->
    <div class="modal" id="profileModal">
        <div class="modal-content">
            <span class="close-modal" id="closeModal">&times;</span>
            <h2>Update Profile Picture</h2>
            <div id="imagePreviewContainer" style="margin: 1rem 0; display: none;">
                <img id="imagePreview" src="#" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px;">
            </div>
            <input type="file" id="profileImageUpload" accept="image/*" style="display: none;">
            <button class="upload-btn" id="chooseImageBtn">Choose Image</button>
            <button class="upload-btn" id="uploadImageBtn" style="display: none;">Upload Image</button>
            <div class="spinner" id="uploadSpinner"></div>
            <p id="uploadStatus" style="margin-top: 1rem; color: var(--primary);"></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const profilePictureFrame = document.getElementById('profilePictureFrame');
            const profileModal = document.getElementById('profileModal');
            const closeModal = document.getElementById('closeModal');
            const chooseImageBtn = document.getElementById('chooseImageBtn');
            const profileImageUpload = document.getElementById('profileImageUpload');
            const uploadImageBtn = document.getElementById('uploadImageBtn');
            const imagePreview = document.getElementById('imagePreview');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            const uploadSpinner = document.getElementById('uploadSpinner');
            const uploadStatus = document.getElementById('uploadStatus');

            // Click on profile picture to open modal
            profilePictureFrame.addEventListener('click', function() {
                profileModal.style.display = 'flex';
                document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
            });

            // Close modal
            function closeProfileModal() {
                profileModal.style.display = 'none';
                document.body.style.overflow = 'auto'; // Re-enable scrolling
                resetUploadForm();
            }

            closeModal.addEventListener('click', closeProfileModal);

            // Click anywhere outside modal to close it
            profileModal.addEventListener('click', function(event) {
                if (event.target === profileModal) {
                    closeProfileModal();
                }
            });

            // Choose image button
            chooseImageBtn.addEventListener('click', function() {
                profileImageUpload.click();
            });

            // Image selection handler
            profileImageUpload.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    const file = e.target.files[0];
                    
                    // Validate file type
                    const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        uploadStatus.textContent = 'Please select a valid image file (JPEG, PNG, GIF)';
                        uploadStatus.style.color = 'red';
                        return;
                    }
                    
                    // Validate file size (max 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        uploadStatus.textContent = 'Image must be less than 2MB';
                        uploadStatus.style.color = 'red';
                        return;
                    }

                    const reader = new FileReader();

                    reader.onload = function(event) {
                        imagePreview.src = event.target.result;
                        imagePreviewContainer.style.display = 'block';
                        chooseImageBtn.style.display = 'none';
                        uploadImageBtn.style.display = 'inline-block';
                        uploadStatus.textContent = '';
                    };

                    reader.readAsDataURL(file);
                }
            });

            // Upload image button
            uploadImageBtn.addEventListener('click', function() {
                if (!profileImageUpload.files[0]) return;

                // Show loading spinner
                uploadSpinner.style.display = 'block';
                uploadImageBtn.disabled = true;
                uploadStatus.textContent = '';

                // Simulate upload delay
                setTimeout(function() {
                    // In a real application, you would upload to server here
                    // For demo purposes, we'll just update the preview
                    const profilePicture = document.getElementById('profilePicture');
                    const defaultProfileIcon = document.getElementById('defaultProfileIcon');

                    if (profilePicture) {
                        profilePicture.src = imagePreview.src;
                    } else {
                        // Create image element if default icon was showing
                        const frame = document.getElementById('profilePictureFrame');
                        frame.innerHTML = '';
                        const newImg = document.createElement('img');
                        newImg.src = imagePreview.src;
                        newImg.alt = 'Profile Picture';
                        newImg.className = 'profile-picture';
                        newImg.id = 'profilePicture';
                        frame.appendChild(newImg);
                    }

                    // Hide default icon if it exists
                    if (defaultProfileIcon) {
                        defaultProfileIcon.style.display = 'none';
                    }

                    // Show success message
                    uploadStatus.textContent = 'Profile picture updated successfully!';
                    uploadStatus.style.color = 'green';

                    // Hide spinner and reset form
                    uploadSpinner.style.display = 'none';
                    setTimeout(function() {
                        closeProfileModal();
                    }, 1500);
                }, 2000);
            });

            // Helper function to reset upload form
            function resetUploadForm() {
                profileImageUpload.value = '';
                imagePreview.src = '#';
                imagePreviewContainer.style.display = 'none';
                chooseImageBtn.style.display = 'inline-block';
                uploadImageBtn.style.display = 'none';
                uploadImageBtn.disabled = false;
                uploadSpinner.style.display = 'none';
                uploadStatus.textContent = '';
            }

            // Animation for profile elements on load
            animateProfileElements();
            
            function animateProfileElements() {
                const elements = [
                    document.querySelector('.profile-picture-frame'),
                    document.querySelector('.profile-name'),
                    document.querySelector('.profile-location'),
                    ...document.querySelectorAll('.info-section'),
                    document.querySelector('.edit-btn')
                ];
                
                elements.forEach((el, index) => {
                    if (el) {
                        el.style.opacity = '0';
                        el.style.transform = 'translateY(20px)';
                        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        
                        setTimeout(() => {
                            el.style.opacity = '1';
                            el.style.transform = 'translateY(0)';
                        }, 100 + (index * 100));
                    }
                });
            }

            // Escape key to close modal
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && profileModal.style.display === 'flex') {
                    closeProfileModal();
                }
            });
        });
    </script>
</body>
</html>