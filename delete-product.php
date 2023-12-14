<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'content_manager'])) {
    die('You do not have permission to delete products.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_product'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);


    $statement = $db->prepare("SELECT image FROM products WHERE product_id = :product_id");
    $statement->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $statement->execute();
    $product = $statement->fetch(PDO::FETCH_ASSOC);
    $imageFile = $product['image'];

    $statement = $db->prepare("DELETE FROM products WHERE product_id = :product_id");
    $statement->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $statement->execute();


    if ($statement->rowCount() > 0) {

        if (file_exists("images/" . $imageFile)) {
            unlink("images/" . $imageFile);
        }
        $_SESSION['message'] = 'Product deleted successfully.';
        header('Location: products.php');
        exit;
    } else {
        $_SESSION['error_message'] = 'Error: Could not delete the product.';
        header('Location: edit-product.php?id=' . $product_id);
        exit;
    }
} else {

    header('Location: products.php');
    exit;
}
?>
