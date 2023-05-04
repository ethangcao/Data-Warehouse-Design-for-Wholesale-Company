<?php

    include './lib/connection.php';

    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;

    if(!empty($start_date) && !empty($end_date)){
        $query = "
            SELECT 
                P.PID, 
                P.Product_Name AS `Product Name`, 
                P.Retail_Price AS `Product Retail Price`,
                P.Total_Quantity AS `Units Sold`,
                P.Total_Quantity - IFNULL(S.Sale_Quantity, 0) AS `Units Sold at Retail Price`,
                IFNULL(S.Sale_Quantity, 0) AS `Units Sold at Sale Price`,
                ROUND( (P.Total_Quantity - IFNULL(S.Sale_Quantity, 0) ) * P.Retail_Price + IFNULL(S.Sale_Quantity, 0) * 0.75 * P.Retail_Price, 2) AS `Predicted Revenue`,
                ROUND( (P.Total_Quantity - IFNULL(S.Sale_Quantity, 0) ) * P.Retail_Price + IFNULL(S.Sale_Total, 0), 2) AS `Actual Revenue`,
                ROUND( IFNULL(S.Sale_Total, 0) - IFNULL(S.Sale_Quantity, 0) * 0.75 * P.Retail_Price, 2) AS `Revenue Difference`
            FROM
                (
                    SELECT
                        SOLD.PID AS PID, 
                        PRODUCT.Product_Name,
                        PRODUCT.Retail_Price, 
                        SUM(SOLD.Unit_Quantity) AS Total_Quantity
                    FROM 
                        SOLD NATURAL JOIN PRODUCT
                    WHERE 
                        SOLD.PID IN (SELECT PID FROM CATEGORY_OF WHERE Category_Name = 'GPS') AND  
                        (SOLD.Date BETWEEN '$start_date' AND '$end_date' )
                    GROUP BY 
                        SOLD.PID
                ) AS P LEFT JOIN
                (
                    SELECT
                        SOLD.PID, 
                        SUM(SOLD.Unit_Quantity) AS Sale_Quantity, 
                        SUM( ON_SALE.Sale_Price* SOLD.Unit_Quantity ) AS Sale_Total
                    FROM 
                        SOLD JOIN ON_SALE ON (SOLD.Date, SOLD.PID) = (ON_SALE.Date, ON_SALE.PID)
                        JOIN PRODUCT ON PRODUCT.PID = ON_SALE.PID
                    WHERE 
                        SOLD.PID IN (SELECT PID FROM CATEGORY_OF WHERE Category_Name = 'GPS') AND 
                        (SOLD.Date BETWEEN '$start_date' AND '$end_date' )
      
                    GROUP BY 
                        SOLD.PID
                ) AS S ON P.PID = S.PID 
            WHERE 
                ABS(  IFNULL(S.Sale_Total, 0) -  IFNULL(S.Sale_Quantity, 0) * 0.75 * P.Retail_Price  ) >= 5000
            ORDER BY 
                `Revenue Difference` DESC;       

        ";

        $result = mysqli_query($db, $query);        
        
    }


?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <title>Actual vs. Predicted Revenue Report</title>
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
            <legend>Actual vs. Predicted Revenue for GPS Units Report</legend>
            
              <form name="my_form" method="POST" onsubmit="return validateForm()">
                Start Date: <input type="date" id="start_date" name="start_date" value="<?php echo $start_date ?>">
              
                End Date: <input type="date" id="end_date" name="end_date"  value="<?php echo $end_date ?>">
                  
                <input type="submit" value="Submit">
              
              </form>
              
              <br/>

              <?php if(!empty($start_date) && !empty($end_date)){  ?>

              <table>
                <thead>
                    <tr>
                        <th>PID</th>
                        <th>Product Name</th>
                        <th>Product Retail Price</th>
                        <th>Units Sold</th>
                        <th>Units Sold at Retail Price</th>
                        <th>Units Sold at Sale Price</th>
                        <th>Predicted Revenue</th>
                        <th>Actual Revenue</th>
                        <th>Revenue Difference</th>
                    </tr>                
                </thead>
                <tbody>
                    <?php if($result->num_rows == 0){ ?>
                        <tr>
                            <td colspan="9">No record is found.</td>
                        </tr>
                    <?php } else { ?>
                        <?php while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){ ?>
                        <tr>
                            <td><?php echo $row['PID']?></td>
                            <td><?php echo $row['Product Name']?></td>
                            <td><?php echo '$'.$row['Product Retail Price']?></td>
                            <td><?php echo $row['Units Sold']?></td>
                            <td><?php echo $row['Units Sold at Retail Price']?></td>
                            <td><?php echo $row['Units Sold at Sale Price']?></td>
                            <td><?php echo '$'.$row['Predicted Revenue']?></td>
                            <td><?php echo '$'.$row['Actual Revenue']?></td>
                            <td><?php echo $row['Revenue Difference']?></td>
                        </tr>

                    
                        <?php } ?>
                    <?php } ?>
                    

                
                </tbody>
              </table>
              
              <?php } ?>

              

          </fieldset>
          <p><a href="./">[Back to Main Menu]</a></p>

        
      </section>
    </main>
    <footer role="contentinfo">
      <p>© 2020 CS6400 Team 042. All rights reserved.</p>
    </footer>
  </div>
  <script>
    function validateForm() {
      let start_date = document.forms["my_form"]["start_date"].value;
      let end_date = document.forms["my_form"]["end_date"].value;

      if (!start_date || !end_date) {
        alert("Dates cannot be empty!");
        return false;
      }
        
      if(start_date > end_date){
        alert("Start date cannot be greater than end date!");
        return false;
      } 
    }
  </script>
</body>

</html>
<?php mysqli_close($db);  ?>
