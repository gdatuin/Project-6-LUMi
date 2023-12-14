<?php

require_once 'connect.php';

$postsHtml = '';
$createButtonHtml = '';

$statement = $db->query("SELECT *, CONCAT(u.first_name, ' ', u.last_name) AS author_full_name FROM blog_posts bp LEFT JOIN users u ON bp.user_id = u.user_id ORDER BY post_date DESC");

while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    $imageClass = !empty($row['blog_image']) ? 'with-image' : 'without-image';
    $postsHtml .= '<div class="blog-post ' . $imageClass . '">';
    if (!empty($row['blog_image'])) {
        $postsHtml .= '<img src="blog_images/' . htmlspecialchars($row['blog_image']) . '" alt="Blog image" class="post-image">';
    }
    $postsHtml .= '<div class="post-text">';
    $postsHtml .= '<h2 class="post-title">' . htmlspecialchars($row['title']) . '</h2>';
  
    $postsHtml .= '<p class="post-date">Posted on ' . htmlspecialchars($row['post_date']) . ' by ' . htmlspecialchars($row['author_full_name']) . '</p>';
    $postsHtml .= '<p class="post-content">' . ($row['content']) . '</p>';

    if (isset($_SESSION['loggedin']) && in_array($_SESSION['role'], ['admin', 'content_manager'])) {
        $postsHtml .= '<a href="edit-post.php?post_id=' . $row['post_id'] . '" class="edit-post-button">Edit Post</a>';
    }
    $postsHtml .= '</div>'; 
    $postsHtml .= '</div>'; 
}

if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'content_manager'])){
    $createButtonHtml = '<a href="create-post.php" class="create-post-button">Create Post</a>';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Blog - LUMi</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace&family=Fugaz+One&family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Days+One&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

    <main>
        <div class= "frame-container">
            <?php echo $createButtonHtml; ?>
            <div class="blog-container">
                <?php echo $postsHtml; ?>
            </div>
        </div>
    </main>

</body>
</html>