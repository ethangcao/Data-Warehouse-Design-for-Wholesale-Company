<?php

    include './lib/connection.php';

    $query = "
        SELECT 
            TRIM(MANUFACTURER.Manufacturer_Name) AS `Manufacturer Name`,
            MANUFACTURER.Maximum_Discount AS `Maximum Discount`, 
            COUNT(PRODUCT.Product_Name) AS `Product Count`, 
            ROUND(AVG(IFNULL(PRODUCT.Retail_Price, 0.00)), 2)  AS `Avg Retail Price`,
            MAX(IFNULL(PRODUCT.Retail_Price, 0.00)) AS `Max Retail Price`, 
            MIN(IFNULL(PRODUCT.Retail_Price, 0.00)) AS `Min Retail Price` 
        FROM 
            MANUFACTURER 
        LEFT JOIN PRODUCT ON 
            PRODUCT.Manufacturer_Name = MANUFACTURER.Manufacturer_Name 
        GROUP BY 
            MANUFACTURER.Manufacturer_Name 
        ORDER BY 
            `Avg Retail Price` DESC 
        LIMIT 100;
    ";

    $result = mysqli_query($db, $query);

    $manufacturer_name = isset($_GET['manufacturer_name']) ? $_GET['manufacturer_name'] : null;


    if(!empty($manufacturer_name)){
        
        $query = "
            SELECT 
                PRODUCT.PID, 
                PRODUCT.Product_Name AS `Product Name`, 
                PRODUCT.Retail_Price AS `Product Retail Price`, 
                GROUP_CONCAT(
                    CATEGORY_OF.Category_Name ORDER BY CATEGORY_OF.Category_Name ASC 
                ) AS `Product Categories` 
            FROM 
                PRODUCT 
            LEFT JOIN CATEGORY_OF ON 
                PRODUCT.PID = CATEGORY_OF.PID 
            WHERE 
                PRODUCT.Manufacturer_Name = '$manufacturer_name'
            GROUP BY 
                PRODUCT.PID 
            ORDER BY 
                PRODUCT.Retail_Price DESC;
        ";
        
        
        
        $result_selected_row = mysqli_query($db, $query);
        
    } 

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <title>Manufacturer Report</title>
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
            <legend>Manufacturer Report</legend>
              
            <table>
                <thead>
                    <tr>
                        <th>Manufacturer Name</th>
                        <th>Product Count</th>
                        <th>Avg Retail Price</th>
                        <th>Max Retail Price</th>
                        <th>Min Retail Price</th>
                    </tr>                
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){ ?>
                    <tr id="<?php echo $row['Manufacturer Name'] ?>">
                        <td>
                            <a href="?manufacturer_name=<?php echo $row['Manufacturer Name']?>">
                                <?php echo $row['Manufacturer Name']?>
                            </a>           
                        </td>
                        <td><?php echo $row['Product Count']?></td>
                        <td><?php echo '$'.($row['Avg Retail Price']) ?></td>
                        <td><?php echo '$'.($row['Max Retail Price']) ?></td>
                        <td><?php echo '$'.($row['Min Retail Price']) ?></td>
                    </tr>
                    
                     <?php if(!empty($manufacturer_name) &&
                              $row['Manufacturer Name'] == $manufacturer_name &&
                              !empty($result_selected_row)
                             ){ ?>
                    
                    <tr style="background-color:white;">
                        <td colspan="5">
                            <fieldset>
                                <legend>Manufacturer Information</legend>
                                <p><b>Manufacturer Name</b>: <?php echo $row['Manufacturer Name'] ?></p>
                                <p><b>Maximum Discount</b>: <?php echo !empty($row['Maximum Discount']) ? $row['Maximum Discount'] . '%' : '0%' ?></p>  
                            </fieldset>
                            <br/>                    
                            <fieldset>
                                <legend>Summary Information</legend>
                                <p><b>Average Retail Price</b>: <?php echo !empty($row['Avg Retail Price']) ? '$' . $row['Avg Retail Price'] : '$0.00' ?></p>
                                <p><b>Max Retail Price</b>: <?php echo !empty($row['Max Retail Price']) ? '$' . $row['Max Retail Price'] : '$0.00' ?></p>
                                <p><b>Min Retail Price</b>: <?php echo !empty($row['Min Retail Price']) ? '$' . $row['Min Retail Price'] : '$0.00' ?></p> 
                            </fieldset>
                            <br/>
                            
                            <fieldset>
                                <legend>Product Information</legend>
                                <table>
                                    <thead>
                                        <tr>
                                            <th style="background-color: #4879b7;">Product ID</th>
                                            <th style="background-color: #4879b7;">Product Name</th>
                                            <th style="background-color: #4879b7;">Product Retail Price</th>
                                            <th style="background-color: #4879b7;">Product Categories</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php if($result_selected_row->num_rows == 0){  ?>
                                            <tr>
                                                <td colspan="3">No product is found.</td>
                                            </tr>
                                        <?php } else { ?>
                                            <?php while ($row_inner = mysqli_fetch_array($result_selected_row, MYSQLI_ASSOC)){ ?>
                                                <tr>
                                                    <td><?php echo $row_inner['PID']; ?></td>
                                                    <td><?php echo $row_inner['Product Name']; ?></td>
                                                    <td><?php echo '$'.($row_inner['Product Retail Price']); ?></td>
                                                    <td><?php echo $row_inner['Product Categories']; ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </fieldset>
                        </td>
                    </tr>


                        <?php } ?>                   
                    
                    
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
      let drilldownRow = document.getElementById('<?php echo $manufacturer_name ?>');
      if(drilldownRow){
          window.scrollTo(0, drilldownRow.offsetTop);
      }

  </script>
</body>

</html>
<?php mysqli_close($db);  ?>
