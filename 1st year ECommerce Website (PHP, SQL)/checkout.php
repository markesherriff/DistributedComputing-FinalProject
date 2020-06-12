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
        <h1>Checkout</h1>
        <p>

            <h2>Items:</h2>
            <p>
                <?php

                $sql="select  * from order_items INNER JOIN sizes ON order_items.SIZE_ID=sizes.SIZE_ID INNER JOIN colours ON order_items.COLOUR_ID=colours.COLOUR_ID
                        INNER JOIN items ON order_items.ITEM_ID=items.ITEM_ID; ";

                $price = "select  sum(PRICE) from order_items INNER JOIN sizes ON order_items.SIZE_ID=sizes.SIZE_ID INNER JOIN colours ON order_items.COLOUR_ID=colours.COLOUR_ID
                        INNER JOIN items ON order_items.ITEM_ID=items.ITEM_ID; ";


                $result=mysqli_query($conn,$sql);


                //display what is in cart
                if(mysqli_num_rows($result)>0)
                {
                    while($row=mysqli_fetch_assoc($result))
                    {
                        echo "Item: ".$row["ITEM_ID"]."<br>Quantity: ".$row["QUANTITY"]."<br>Size: ".$row["SIZE_DESC"]."<br>Color: ".$row["COLOUR_DESC"]."<br>Price: $".$row["PRICE"];
                        echo "<br><br>";
                    }
                }


                else//If nothing in cart
                {
                    echo "no results found";
                }    


                $totalPrice=mysqli_query($conn,$price);


                //display what is in cart
                if(mysqli_num_rows($totalPrice)>0)
                {
                    while($row=mysqli_fetch_assoc($totalPrice))
                    {
                        echo "<br>Total Price: $".$row["sum(PRICE)"];
                        $fullprice = $row["sum(PRICE)"];
                        echo "<br><br>";
                    }
                }


                else//If nothing in cart
                {
                    echo "";
                }  

                ?>

    <!-- Go back to edit shopping cart -->
    <a href='./cart.html'>Edit Shopping Cart</a>
</p>
<h1>Shipping and Billing Info</h1>

        <div class="form-group row">    
            <label class="col-form-label text-md-right col-md-2">Name:</label>
            <div class="col-md-4">
                <input class="form-control" type="text" id="txtName" name="name" maxlength="50" size="10" autofocus>
            </div>
        </div>
                
        
        <div class="form-group row">     
            <label class="col-form-label text-md-right col-md-2">Address:</label>
            <div class="col-md-4">
                <input class="form-control" type="text" id="txtAddress" name="Address" maxlength="50" size="10">
            </div>         
        </div>

        <div class="form-group row">                
            <label class="col-form-label text-md-right col-md-2" for="numPhone">Phone Number:</label>
            <div class="col-md-4">
                <input class="form-control" type="tel" id="numPhone" name="phonenum" maxlength="20" size="20">
            </div>
        </div>    
        
        <div class="form-group row">
            <label class="col-form-label text-md-right col-md-2" for="email">Email:</label>
            <div class="col-md-4">
                <input class="form-control" type="email" id="email" name="email" maxlength="50" size="20">
            </div>
        </div>    
                
            <br>
            <br>

<div id="paypal-button"></div>
<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<script>
var price = "<?php echo $fullprice ?>"; 

  paypal.Button.render({
    // Configure environment
    env: 'sandbox',
    client: {
      sandbox: 'demo_sandbox_client_id',
      production: 'demo_production_client_id'
    },
    // Customize button (optional)
    locale: 'en_US',
    style: {
      size: 'large',
      color: 'gold',
      shape: 'pill',
    },

    // Enable Pay Now checkout flow (optional)
    commit: true,

    // Set up a payment
    payment: function(data, actions) {
      return actions.payment.create({
        transactions: [{
          amount: {
            total: price,
            currency: 'USD'
          }
        }]
      });
    },
    // Execute the payment
    onAuthorize: function(data, actions) {
      return actions.payment.execute().then(function() {
        // Show a confirmation message to the buyer
        window.alert('Thank you for your purchase!');
        window.location = './products.php';
      });
    }
  }, '#paypal-button');

</script>

</p>

</main>
</div>


<br>

<footer>



    &copy; 2019
</footer>
</body>
</html>