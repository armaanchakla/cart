<?php
  include "includes/db_connect.inc.php";
  session_start();

  /* Creating empty Sessions for Cart Elements if it was not created earlier */
  if(!isset($_SESSION["product_id"])){
    $_SESSION["product_id"] = array();
    $_SESSION["product_name"] = array();
    $_SESSION["product_qty"] = array();
    $_SESSION["product_price"] = array();
    $_SESSION["total"] = "";
  }

  if(!isset($_SESSION["username"])){
    header("Location: login.php");
  }

  if(isset($_GET['cat'])){
    $sqlProducts = "SELECT p.*, c.category_name FROM products p, product_category c where c.id = " . $_GET['cat'] . " AND p.product_category = c.id;";
  }
  else{
    $sqlProducts = "SELECT p.*, c.category_name FROM products p, product_category c where p.product_category = c.id;";
  }

  $resulProducts = mysqli_query($conn, $sqlProducts);
  $resultCount = mysqli_num_rows($resulProducts);

?>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <style>

      aside{
        float:right;
        width:30%;
        margin-right: 30%;
        border: solid 1px;
        margin-top: 3%;
      }

      aside h1{
        text-align: center;
      }

      div.prods{
        float:left;
        width:30%;
      }

    </style>

    <title>Welcome</title>
  </head>
  <body>
    <span style="font-size: 32px">Welcome, <?php echo "<a href='welcome.php'>" . $_SESSION["username"] . "</a>" ; ?> | <a href="logout.php">Logout</a></span>

    <br><br>

    <!-- PRODUCT STARTS ENDS HERE -->
    <div class="prods">
      <?php require_once 'includes/catNav.php'; ?>
      <br><br>
      <?php
        if($resultCount<=0){
          echo "NO ITEM FOUND! <br>";
        }
        else{
          while ($rowProducts = mysqli_fetch_assoc($resulProducts)) { ?>
            <div class="products_to_buy" style="padding: 10px; border: solid 1px;">
              <span class="product_name"><?php echo $rowProducts["product_name"]; ?></span> <br>
              <span>Price: </span><span class="product_price"><?php echo $rowProducts["product_price"]; ?></span> <br>
              <input class="product_qty" type="number" name="" value="1" placeholder="quantity" min="1"> <br><br>
              <button type="button" name="button" id="<?php echo $rowProducts["id"]; ?>" class="add_to_cart_btn">ADD TO CART</button>
            </div>
            <br>
        <?php
          }
        }
        ?>
    </div>
    <!-- PRODUCT LISTING ENDS HERE -->

    <!-- CART STARTS HERE -->
    <aside class="cart_area" style="padding: 10px;">
      <table class="cart_table" width="100%">
        <tr class="cart_row">
          <th colspan="4">Cart</th>
        </tr>
        <tr class="cart_row">
          <th colspan="4"> <hr> </th>
        </tr>
        <tr class="cart_row">
          <th>Item</th>
          <th>Quantity</th>
          <th>Price</th>
          <th>&nbsp;</th>
        </tr>
        <tr class="cart_row">
          <th><hr></th>
          <th><hr></th>
          <th><hr></th>
          <th>&nbsp;</th>
        </tr>
        <!-- If there is session present for cart products, then populate them with loop and table row(s) -->
        <?php
          if (!empty($_SESSION['product_id'])) {
            for ($i=0; $i < count($_SESSION['product_id']) ; $i++) { ?>
              <tr class="cart_row">
              <td align="center"><span class="p_title"><?php echo $_SESSION['product_name'][$i]; ?></span></td>
              <td align="center"><span class="p_quantity"><?php echo $_SESSION['product_qty'][$i]; ?></span></td>
              <td align="center"><span class="p_price"><?php echo $_SESSION['product_price'][$i]; ?></span></td>
              <td align="center"><button type="button" id="<?php echo $_SESSION['product_id'][$i]; ?>" class="remove_from_cart_btn">REMOVE</button></td>
              </tr>
        <?php
            }
          }
        ?>
      </table>

      <hr>

      <!-- TOTAL PRICE FROM THE CART -->
      <div align="right" id="total_price">
        <span>Total: </span>
        <?php if (!isset($_SESSION["total"])) { ?>
            <span id="price_amount">0</span>
        <?php
          }
          else{ ?>
            <span id="price_amount"><?php echo $_SESSION["total"]; ?></span>
        <?php
          }
        ?>
      </div>

    </aside>
    <!-- CART ENDS HERE -->

    <!-- JQUERY (JS TEMPLATE) SOURCE FILE -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>

    <!-- OUR JAVASCRIPT FOR THIS FILE STARTS HERE -->
    <script>

      var addToCartButtons = document.getElementsByClassName('add_to_cart_btn'); // All the "add to cart" buttons gathered in an array
      var removeFromCartButtons = document.getElementsByClassName('remove_from_cart_btn'); // All the "remove" buttons in the cart gathered in an array

      //Going through the `addToCartButtons` array to add eventlistener for all the "add to cart" buttons in the array when the page loads
      for (var i = 0; i < addToCartButtons.length; i++) {
          var buttonAdd = addToCartButtons[i]
          buttonAdd.addEventListener('click', addToCartClicked)
      }

      //Going through the `addToCartButtons` array to add eventlistener for all the "add to cart" buttons in the array when the page loads
      for (var i = 0; i < removeFromCartButtons.length; i++) {
          var buttonRemove = removeFromCartButtons[i]
          buttonRemove.addEventListener('click', removeCartItem)
      }

      //Adding products information in the cart after clicking an "add to cart" button for a product
      function addToCartClicked(event){
        var button = event.target;
        var product = button.parentElement;
        var title = product.getElementsByClassName('product_name')[0].innerText;
        var price = product.getElementsByClassName('product_price')[0].innerText;
        var quantity = product.getElementsByClassName('product_qty')[0].value;
        var pro_id = product.getElementsByClassName('product_id')[0].value;
        var tot = document.getElementById('price_amount').innerText;

        price = price * quantity;
        price = parseFloat(price.toFixed(2)); //parsing the string to float and then keepin it till 2 decimal point

        tot = parseFloat(tot) + price;

        if(addItemToCart(title, quantity, price, pro_id) === true) //if adding a product to the cart is successful
          $('#'+button.id).load('cartSession.php',{t: title, p: price, q: quantity, p_id: pro_id, total: tot}); //product info sent via ajax for adding in session
        updateTotalPrice(); //updating the total of the cart

      }

      //Adding the product and it's html in the page after getting clicked on the "add to cart" for that specific product
      function addItemToCart(title, quantity, price, pro_id){
        var cartTable = document.getElementsByClassName('cart_table')[0];
        var cartRow = document.createElement('tr');
        var existingCartRows = document.getElementsByClassName('cart_row');

        //If the product is already added in the cart, it will not be added again
        for (var i = (existingCartRows.length-1); i >= 4 ; i--) {
          if (existingCartRows[i].getElementsByClassName('remove_from_cart_btn')[0].id == pro_id) {
              alert("Product is already in the cart. Remove from cart first to add it again with different quantity.");
              return false;
          }
        }

        //if the product is added for the first time, it will be added to the cart and the following html script will be generated and appended to the existing html of the cart
        cartRow.classList.add('cart_row');
        var cartRowContents = `
        <td align="center"><span class="p_title">${title}</span></td>
        <td align="center"><span class="p_quantity">${quantity}</span></td>
        <td align="center"><span class="p_price">${price}</span></td>
        <td align="center"><button type="button" id="${pro_id}" class="remove_from_cart_btn">REMOVE</button></td>`;
        cartRow.innerHTML = cartRowContents
        cartTable.append(cartRow);
        cartRow.getElementsByClassName('remove_from_cart_btn')[0].addEventListener('click', removeCartItem);
        return true;
      }

      //Removing a product from the cart (which is in a table row of the HTML script)
      function removeCartItem(event) {
        var buttonClicked = event.target;
        $('#'+buttonClicked.id).load('cartSession.php',{removeId: buttonClicked.id});
        buttonClicked.parentElement.parentElement.remove();
        updateTotalPrice();
      }

      //Updating the total price of the cart
      function updateTotalPrice(){
        var existingCartRows = document.getElementsByClassName('cart_row');
        var total = 0;
        for (var i = (existingCartRows.length-1); i >= 4 ; i--) {
          total = total + parseFloat(existingCartRows[i].getElementsByClassName('p_price')[0].innerText);
      }
      document.getElementById('price_amount').innerText = total;
    }

    </script>
    <!-- OUR JAVASCRIPT FOR THIS FILE ENDS HERE -->

  </body>
</html>
