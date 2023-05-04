<?php

    include './lib/connection.php';

    $state = isset($_POST['state']) ? $_POST['state'] : null;

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <title>Store Revenue by Year by State Report</title>
</head>

<body>
  <div id="top" class="page" role="document">
    <header role="banner">
      <h1>PricePalace Data Warehouse</h1>
      </p>
    </header>
    <main role="main">

      <section>      

        
          <p><a href="./">[Back to Main Menu]</a></p>

          <fieldset>
            <legend>Store Revenue by Year by State Report</legend>

              <form method='post' action=''>
                  Please select a state (required)
                  <select name="state" required>
                      <option selected="selected"><?php echo $state ?></option>
                      <?php
                      $query1 = "SELECT
                        DISTINCT CITY .City_State AS State
                        FROM CITY
                        ORDER BY State;";
                      $result1 = mysqli_query($db, $query1);
                      while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
                      echo "<option value='" . $row1['State'] . "'>" . $row1['State'] . "</option>";
                      }
                      ?>
                  </select>
                  <input type='submit' name='but_search' value='Search'>
              </form>
              
              <br/>


              <table>
                <thead>
                    <tr>
                        <th>Store Number</th>
                        <th>Store Address</th>
                        <th>City</th>
                        <th>Sales Year</th>
                        <th>Total Revenue</th>
                    </tr>                
                </thead>
                <tbody>
                    <tr>
                        <?php
                        if(empty($state)) {
                            print "<tr>";
                            print "<td colspan='5'>Please select a state.</td>";
                            print "</tr>";
                        } else {
                            $query = "SELECT
                                Store_Number AS `Store Number`,
                                Street_Address AS `Store Address`,
                                City_Name AS `City`,
                                YEAR( R .Date) AS `Sales Year`,
                                ROUND(SUM((IFNULL(ON_SALE.Sale_Price, Retail_Price) ) * Unit_Quantity), 2)
                                AS `Total Revenue`
                                FROM
                                (
                                SELECT
                                SOLD .PID,
                                Store_Number,
                                Date,
                                Unit_Quantity,
                                Retail_Price
                                FROM
                                SOLD
                                NATURAL JOIN PRODUCT
                                ) R
                                LEFT JOIN ON_SALE ON
                                ( R .Date, R .PID) = ( ON_SALE .Date, ON_SALE .PID)
                                NATURAL JOIN STORE
                                WHERE
                                STORE .City_State = '$state'
                                GROUP BY
                                Store_Number, YEAR( R .Date)
                                ORDER BY
                                `Sales Year` ASC, `Total Revenue` DESC;
                            ";
                            $result = mysqli_query($db, $query);
                            $count = mysqli_num_rows($result);
                            if ($count>0){
                                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                                    print "<tr>";
                                    print "<td>{$row['Store Number']}</td>";
                                    print "<td>{$row['Store Address']}</td>";
                                    print "<td>{$row['City']}</td>";
                                    print "<td>{$row['Sales Year']}</td>";
                                    echo "<td>"."$".$row['Total Revenue']."</td>";
                                    print "</tr>";
                                }
                            } else{
                                print "<tr>";
                                print "<td colspan='5'>No record found</td>";
                                print "</tr>";
                            }
                            $result->free();
                        } ?>
                    </tr>
                    

                
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
</body>

</html>
<?php mysqli_close($db);  ?>
