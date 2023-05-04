<?php

   include './lib/connection.php';

    $query = "
        SELECT
            CO.Category_Name as `Category Name`,
            COUNT( IFNULL(P.PID, 0)) AS `Number of Products`,
            ROUND(AVG(IFNULL(P.Retail_Price, 0)), 2) AS `Average Retail Price`,
            COUNT(DISTINCT P.Manufacturer_Name) AS `Number of Unique Manufacturers`
        FROM
            CATEGORY_OF AS CO 
        LEFT JOIN PRODUCT AS P ON
            CO.PID = P.PID
        GROUP BY 
            CO.Category_Name
        ORDER BY
            CO.Category_Name ASC
    ";

    $result = mysqli_query($db, $query);

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <title>Category Report</title>
</head>

<body>
  <div id="top" class="page" role="document">
    <header role="banner">
      <h1>PricePalace Data Warehouse</h1>
    </header>
    <main role="main">

      <section>      

        
          <p><a href="./">[Back to Main Menu]</a></p>

          <fieldset>
            <legend>Category Report</legend>
              
            <table>
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Number of Products</th>
                        <th>Average Retail Price</th>
                        <th>Number of Unique Manufacturers</th>
                    </tr>                
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){ ?>
                    <tr>
                        <td><?php echo $row['Category Name']?></td>
                        <td><?php echo $row['Number of Products']?></td>
                        <td><?php echo '$'.($row['Average Retail Price'])?></td>
                        <td><?php echo $row['Number of Unique Manufacturers']?></td>
                    </tr>

                    
                    <?php } ?>
                    

                
                </tbody>
              </table>

              

          </fieldset>
          <p><a href="./">[Back to Main Menu]</a></p>

        
      </section>
    </main>
    <footer role="contentinfo">
      <p>© 2020 CS6400 Team 042. All rights reserved.</p>
    </footer>
  </div>
  <script>
    
  </script>
</body>

</html>
<?php mysqli_close($db);  ?>
