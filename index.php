<!--------w-----------

    Project 4
    Name: Gabriel Datuin
    Date: 
    Description: Displays the homepage of a clothing website.

--------------------->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Home - LUMi</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace&family=Fugaz+One&family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Days+One&display=swap" rel="stylesheet">
    <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
    <meta name="viewport" content="width=device-width">
    <link rel="shortcut icon" href="#">
</head>


    <body id="index">
        
        <div class="elfsight-app-4114d580-7b3f-4432-b30a-d4699aac173d"></div>
    

        <?php include 'header.php';?>

       <main id="indexContent">
       <div class="introImg">
        <img src="images/index-img.png" alt="Homepage image">
       </div>

       <div class="onImg">
        <h1>Welcome to LUMi &#128966; Bienvenue à LUMi</h1>
        <h2>Retro Revived, Future Defined</h2>

            
        <a href="products.php">&#128970; EXPLORE OUR CLOTHING &#128970;</a>
            
       </div>

       <div id="about-us">

        <h1>&#128966; About Us</h1>

        <p> At LUMi, we believe that fashion is not just about clothing, but a form of self-expression. Our curated collection features an eclectic mix of Y2K clothing that empowers you to express your individuality and make a statement with your style. From neon-bright colors to unique fabrics, our clothing is designed to make you stand out and turn heads wherever you go.</p>
        
        <p>Our passion for Y2K fashion extends beyond our products. Our knowledgeable and friendly staff are always ready to assist you with styling tips, size recommendations, and any other questions you may have. We also love to connect with our customers on social media, where we share styling ideas, fashion inspiration, and updates on the latest trends.

        We are excited to be your go-to destination for all your Y2K fashion needs. Step into our store and let us take you on a fashion journey to the era of flip phones, frosted tips, and futuristic fashion. Join us at LUMi and embrace the timeless style of the new millennium!</p>

       </div>

        
        <div class="newestProductsHeader">
         <h1>New Arrivals</h1>
        </div>
        
        
            <div class="newestProductsItems">

            <div class="newItem1">
            <a href="#"><img src="images/blue-cow-top.png" alt="Wavy Crop Top"></a>
            <p><a href="#">Wavy Crop Top</a><br>$17.99</p>
            </div>
            
            <div class="newItem2">
            <a href="#"><img src="images/heart-top.png" alt="Cropped Heart Tank Top"></a>
            <p><a href="#">Cropped Heart Tank Top</a><br>$12.99</p>
            </div>


            <div class="newItem3">
            <a href="#"><img src="images/blue-vest.png" alt="Pastel Blue Vest"></a>
            <p><a href="#">Pastel Blue Vest</a><br>$19.99</p>
            </div>


        </div>
       </main>

        <?php include 'footer.php'; ?>
    </body>
</html>