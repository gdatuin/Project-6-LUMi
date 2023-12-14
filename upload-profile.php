<?php
session_start();
require_once 'connect.php';

$currentProfilePic = 'images/icon.png'; 

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$statement = $db->prepare("SELECT profile_picture FROM users WHERE user_id = :user_id");
$statement->bindParam(':user_id', $userId);
$statement->execute();
$userData = $statement->fetch(PDO::FETCH_ASSOC);

if (!empty($userData['profile_picture'])) {
    $currentProfilePic = $userData['profile_picture'];
}

if (isset($_POST['remove_picture'])) {

    $statement = $db->prepare("UPDATE users SET profile_picture = NULL WHERE user_id = :user_id");
    $statement->bindParam(':user_id', $userId);
    if ($statement->execute()) {
        echo "<script>alert('Profile picture removed successfully.'); window.location.href='profile.php';</script>";
    } else {
        echo "<script>alert('Error: Unable to remove the profile picture.'); window.location.href='upload-profile.php';</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $allowed = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
    $file_name = $_FILES['profile_picture']['name'];
    $file_type = $_FILES['profile_picture']['type'];
    $file_size = $_FILES['profile_picture']['size'];

    $ext = pathinfo($file_name, PATHINFO_EXTENSION);
    if (!array_key_exists($ext, $allowed)) {
        echo "<script>alert('Error: Please select a valid file format.'); window.location.href='upload-profile.php';</script>"; 
    } elseif ($file_size > 500000) {
        echo "<script>alert('Error: File size is too large.'); window.location.href='upload-profile.php';</script>"; 
    } elseif (!in_array($file_type, $allowed)) {
        echo "<script>alert('Error: Invalid file type.'); window.location.href='upload-profile.php';</script>"; 
    } else {
        $target_dir = "profile_pictures/";
        $target_file = $target_dir . uniqid() . "." . $ext;

        if (file_exists($target_file)) {
             echo "<script>alert('Error: File already exists.'); window.location.href='upload-profile.php';</script>"; 
        } else {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
       
                list($width, $height) = getimagesize($target_file);
                $newWidth = 150;
                $newHeight = 150;
                $thumb = imagecreatetruecolor($newWidth, $newHeight);
                $source = imagecreatefromstring(file_get_contents($target_file));

                imagecopyresized($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                switch ($ext) {
                    case 'jpg':
                    case 'jpeg':
                        imagejpeg($thumb, $target_file);
                        break;
                    case 'png':
                        imagepng($thumb, $target_file);
                        break;
                    case 'gif':
                        imagegif($thumb, $target_file);
                        break;
                }
                imagedestroy($thumb);
                imagedestroy($source);


                $userId = $_SESSION['user_id'];
                $statement = $db->prepare("UPDATE users SET profile_picture = :profile_picture WHERE user_id = :user_id");
                $statement->bindParam(':profile_picture', $target_file);
                $statement->bindParam(':user_id', $userId);
                if ($statement->execute()) {
                    echo "<script>alert('Profile picture uploaded Successfully!'); window.location.href='profile.php';</script>"; 
                } else {
                    echo "<script>alert('Error: Unable to update the profile picture in the database.'); window.location.href='upload-profile.php';</script>"; 
                }
            } else {
                echo "<script>alert('Error: There was a problem uploading your file. Please try again.'); window.location.href='upload-profile.php';</script>"; 
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Upload Profile Picture - LUMi</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace&family=Fugaz+One&family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Days+One&display=swap" rel="stylesheet">
    <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
    <meta name="viewport" content="width=device-width">
    <link rel="shortcut icon" href="#">
</head>

<?php include 'header.php'; ?>
<body id = upload-profile>

<main>
    <h2>Upload Profile Picture</h2>

    <form action="upload-profile.php" method="post" enctype="multipart/form-data" class=upload-profile-form>

    <?php if (!empty($userData['profile_picture'])): ?>
        <div class="current-profile-picture">
        <img src="<?= htmlspecialchars($currentProfilePic) ?>" alt="Current Profile Picture">
    </div>
       <?php endif; ?>
        <input type="file" name="profile_picture" id="profile_picture">
        
        <br><input type="submit" value="Upload Image" name="submit" class="upload-picture">
        <br><input type="submit" value="Remove Image" name="remove_picture" class="remove-picture">
    </form>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
