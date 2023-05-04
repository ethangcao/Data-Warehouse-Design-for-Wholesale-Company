<?php

    include './lib/connection.php';

    $query = "SELECT
                    A .y AS `Year`,
                    A .Year_Total AS `Year Total`,
                    A .Year_Average AS `Average Day Sale`,
                    IFNULL( G .Groundhog, 0) AS `Groundhog Day Sale`
                    FROM
                    (
                    SELECT
                    YEAR(Date) AS y,
                    SUM(Unit_Quantity) AS Year_Total,
                    ROUND(SUM(Unit_Quantity) / 365, 2) AS Year_Average
                    FROM
                    SOLD
                    NATURAL JOIN (SELECT PID FROM CATEGORY_OF WHERE Category_Name = 'Air Conditioner') AC1
                    GROUP BY
                    YEAR(Date)
                    ) A
                    LEFT JOIN
                    (
                    SELECT
                    YEAR(Date) AS y,
                    SUM(Unit_Quantity) AS Groundhog
                    FROM
                    SOLD
                    NATURAL JOIN (SELECT PID FROM CATEGORY_OF WHERE Category_Name = 'Air Conditioner') AC2
                    WHERE
                    DATE_FORMAT(Date, '%m%d') = '0202'
                    GROUP BY
                    YEAR(Date)
                    ) G ON A .y = G .y
                    ORDER BY
                    `Year` ASC";

    $result = mysqli_query($db, $query);

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <title>Air Conditioners on Groundhog Day Report</title>
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
            <legend>Air Conditioners on Groundhog Day Report</legend>
              
            <table>
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Year Total</th>
                        <th>Average Day Sale</th>
                        <th>Groundhog Day Sale</th>
                    </tr>                
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){ ?>
                    <tr>
                        <td><?php echo $row['Year']?></td>
                        <td><?php echo $row['Year Total']?></td>
                        <td><?php echo $row['Average Day Sale']?></td>
                        <td><?php echo $row['Groundhog Day Sale']?></td>
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
