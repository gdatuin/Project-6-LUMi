<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'content_manager'])) {
    die('You do not have permission to delete products.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_post'])) {
    $post_id = filter_input(INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT);


    $statement = $db->prepare("SELECT blog_image FROM blog_posts WHERE post_id = :post_id");
    $statement->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $statement->execute();
    $post = $statement->fetch(PDO::FETCH_ASSOC);
    $imageFile = $post['blog_image'];

    $statement = $db->prepare("DELETE FROM blog_posts WHERE post_id = :post_id");
    $statement->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $statement->execute();


    if ($statement->rowCount() > 0) {

        if (file_exists("blog_images/" . $imageFile)) {
            unlink("blog_images/" . $imageFile);
        }
        $_SESSION['message'] = 'Post deleted successfully.';
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['error_message'] = 'Error: Could not delete the post.';
        header('Location: edit-post.php?id=' . $post_id);
        exit;
    }
} else {

    header('Location: index.php');
    exit;
}
?>
