<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<a>
    	<img class="img-fluid" style="background-size: cover;" src="images/splashpage-banner-dripline-givenchy.png"/>
	</a>
	<?php
    	require_once ("mainNavigation.php");
	?>
</head>
<body style="background-color: white;">
    <div class="container">
        <h1>Welcome!</h1>
        <p>
            <?php
            echo '<h1> Recent Products </h1>';

            $sql="select  * from items INNER JOIN images ON items.ITEM_ID=images.ITEM_ID ORDER BY items.ITEM_ID DESC LIMIT 5;";

            $result=mysqli_query($conn,$sql);

            while($row=mysqli_fetch_assoc($result))
            {
                echo "<a href='./itemPage_AddToCartFINAL.php?prod_id=".$row["ITEM_ID"]."'>";
                echo '<img src="images/Photos/'.$row["IMAGE_FILE_NAME"].'" alt="Product Image" height="100" width="100"></p>'; 
                echo "Product: ".$row["Name"]."<br>Description: ".$row["DESCRIPTION"]."<br>Price: $".$row["PRICE"];
                echo "</a>";
                echo "<br><br>";
                
            }
            
            echo"<br><br><br><br>";
            $sql="select  * from items INNER JOIN images ON items.ITEM_ID=images.ITEM_ID where sex ='m' ORDER BY items.ITEM_ID DESC LIMIT 5";


            echo '<h1> Recent Mens Products </h1>';

            $result=mysqli_query($conn,$sql);

            while($row=mysqli_fetch_assoc($result))
            {
                echo "<a href='./itemPage_AddToCartFINAL.php?prod_id=".$row["ITEM_ID"]."'>";
                echo '<img src="images/Photos/'.$row["IMAGE_FILE_NAME"].'" alt="Product Image" height="100" width="100"></p>'; 
                echo "Product: ".$row["Name"]."<br>Description: ".$row["DESCRIPTION"]."<br>Price: $".$row["PRICE"];
                echo "</a>";

                echo "<br><br>";
            }



            echo"<br><br><br><br>";
            echo '<h1> Recent Women Products </h1>';

            $sql="select  * from items INNER JOIN images ON items.ITEM_ID=images.ITEM_ID where sex ='w' ORDER BY items.ITEM_ID DESC LIMIT 5";

            $result=mysqli_query($conn,$sql);

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
</div>


<br>

</body>
<footer>
</footer>
</html>