<?php

  include "includes/db_connect.inc.php";

  $sqlCat = "SELECT * FROM product_category;";
  $resultCat = mysqli_query($conn, $sqlCat);
  $catCount = mysqli_num_rows($resultCat);

  while ($rowCat = mysqli_fetch_assoc($resultCat)) { $catCount--; ?>
    <a href="welcome.php?cat=<?php echo $rowCat['id'] ?>"><?php echo $rowCat['category_name']; ?></a>

<?php
    if($catCount>0){
      echo " | ";
    }
  }
?>
