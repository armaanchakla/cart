<?php
session_start();

//If a product is removed from the cart, that product should also be removed from the session. Otherwise, multiple identical product will appear in the cart
if(isset($_POST['removeId'])){
  $i = 0;
  foreach ($_SESSION['product_id'] as $key => $value) {
    if($value == $_POST['removeId']){
      break;
    }
    $i++;
  }

  $_SESSION['total'] = $_SESSION['total'] - $_SESSION['product_price'][$i];

  unset($_SESSION['product_id'][$i]);
  $_SESSION['product_id'] = array_values($_SESSION['product_id']);

  unset($_SESSION['product_name'][$i]);
  $_SESSION['product_name'] = array_values($_SESSION['product_name']);

  unset($_SESSION['product_qty'][$i]);
  $_SESSION['product_qty'] = array_values($_SESSION['product_qty']);

  unset($_SESSION['product_price'][$i]);
  $_SESSION['product_price'] = array_values($_SESSION['product_price']);

  echo "ADD TO CART";
}
//If a product is added in the cart then the following script is applicable
else{
  array_push($_SESSION['product_id'], $_POST['p_id']);
  array_push($_SESSION['product_name'], $_POST['t']);
  array_push($_SESSION['product_qty'], $_POST['q']);
  array_push($_SESSION['product_price'], $_POST['p']);
  $_SESSION['total'] = $_POST['total'];

  echo "ADD TO CART";
}

?>
