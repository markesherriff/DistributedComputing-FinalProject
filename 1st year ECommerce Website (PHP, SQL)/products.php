<?php
    require_once('mainNavigation.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Full Fits</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/bootstrap.min.css">
    <link rel="stylesheet" href="styles/slide.css">
    <link rel="stylesheet" href="styles/formstyle.css">
    <link rel="stylesheet" href="styles/style.css">



    <script type="text/javascript" src="js/popper.min.js">
    </script>
    <script type="text/javascript" src="js/jquery-3.3.1.min.js">
    </script>
    <script type="text/javascript" src="js/bootstrap.min.js">
    </script>

</head>


<body>

<br>

<div class="container">
    <header>

    </header>

    <main>
        <p>
            <?php

            $sql="select  * from items INNER JOIN images ON items.ITEM_ID=images.ITEM_ID ".$_GET['type_id']." ORDER BY items.ITEM_ID;";

            $result=mysqli_query($conn,$sql);

            if($_GET['type_id'] == "")
            {
                echo '<h1>All Products </h1>';
            }elseif ($_GET['type_id'] == "where sex = &quot;m&quot;") {
                echo '<h1>All Products </h1>';
            }elseif ($_GET['type_id'] == "where sex = &quot;w&quot;") {
                echo '<h1>All Products </h1>';
            }


            while($row=mysqli_fetch_assoc($result))
            {


                echo "<a href='./itemPage_AddToCartFINAL.php?prod_id=".$row["ITEM_ID"]."'>";
                echo '<img src="images/Photos/'.$row["IMAGE_FILE_NAME"].'" alt="Product Image" height="100" width="100"></p>'; 
                echo "Product: ".$row["Name"]."<br>Description: ".$row["DESCRIPTION"]."<br>Price: $".$row["PRICE"];
                echo "</a>";
                
                echo "<br><br>";
                
            }
            

            ?>
        </p>

    </main>
</div>


<br>
</body>
</html>