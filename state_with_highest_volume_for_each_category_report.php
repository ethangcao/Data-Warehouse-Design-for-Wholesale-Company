<?php

    include './lib/connection.php';

    $cdate = isset($_POST['cdate']) ? $_POST['cdate'] : null;

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <title>State Highest Volume Report</title>
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
            <legend>State Highest Volume Report</legend>
            
              <form method='post' action=''>
                  Please select Year and Month (required)
                  <select name="cdate" required>
                      <option selected="selected"><?php echo $cdate ?></option>
                      <?php
                      $query1 = "SELECT DATE_FORMAT(DATE.Date, '%Y-%m') AS 'YearMonth' 
                        FROM DATE
                        GROUP BY DATE_FORMAT(DATE.Date, '%Y-%m');";
                      $result1 = mysqli_query($db, $query1);
                      while ($row1 = mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
                      echo "<option value='" . $row1['YearMonth'] . "'>" . $row1['YearMonth'] . "</option>";
                      }
                      ?>
                  </select>
                  <input type='submit' name='but_search' value='Search'>
              </form>
              
              <br/>


              <table>
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Max Sale Quantity</th>
                        <th>State</th>
                    </tr>                
                </thead>
                <tbody>
                    <tr>
                        <?php
                        if(empty($cdate)) {
                        print "<tr>";
                        print "<td colspan='5'>Please select Year and Month.</td>";
                        print "</tr>";
                        }
                        else {
                        $query = "SELECT 
                            D.Category_Name,
                            D.Max_Sale_Qty,
                            E.City_State
                        FROM 
                            (
                                SELECT 
                                    C.Category_Name,
                                    MAX(C.Sale_Qty) AS Max_Sale_Qty
                                FROM 
                                    (
                                        SELECT 
                                            A.Category_Name,
                                            SUM(A.Unit_Quantity) as Sale_Qty,
                                            DATE_FORMAT(A.Date, '%Y-%m'),
                                            B.City_State
                                         FROM 
                                             (
                                                 SELECT 
                                                     SOLD.PID,
                                                     CATEGORY_OF.Category_Name,
                                                     SOLD.Unit_Quantity,
                                                     SOLD.Date,
                                                     SOLD.Store_Number
                                                 FROM 
                                                     SOLD
                                                 NATURAL JOIN 
                                                     CATEGORY_OF
                                                 WHERE 
                                                 DATE_FORMAT(SOLD.Date, '%Y-%m') = '$cdate'
                                             ) A
                                         LEFT JOIN
                                             (
                                                 SELECT 
                                                     STORE.Store_Number, 
                                                     CITY.City_State
                                                 FROM 
                                                     STORE
                                                 NATURAL JOIN 
                                                     CITY
                                             ) B ON A.Store_Number = B.Store_Number
                                         GROUP BY 
                                             A.Category_Name, B.City_State, DATE_FORMAT(SOLD.Date, '%Y-%m')
                                    ) C
                               GROUP BY 
                                   C.Category_Name
                            ) D
                        LEFT JOIN
                            (
                                SELECT 
                                    A.Category_Name, 
                                    SUM(A.Unit_Quantity) as Sale_Qty, 
                                    DATE_FORMAT(A.Date, '%Y-%m'),
                                    B.City_State
                                 FROM 
                                     (
                                          SELECT 
                                              SOLD.PID, 
                                              CATEGORY_OF.Category_Name, 
                                              SOLD.Unit_Quantity, 
                                              SOLD.Date, 
                                              SOLD.Store_Number
                                          FROM 
                                              SOLD
                                          NATURAL JOIN 
                                              CATEGORY_OF
                                          WHERE 
                                          DATE_FORMAT(SOLD.Date, '%Y-%m') = '$cdate'
                                     ) A


                                 LEFT JOIN
                                     (
                                         SELECT 
                                             STORE.Store_Number, 
                                             CITY.City_State
                                         FROM 
                                             STORE
                                         NATURAL JOIN 
                                             CITY
                                     ) B ON 
                                     A.Store_Number = B.Store_Number
                                 GROUP BY 
                                     A.Category_Name, B.City_State, DATE_FORMAT(A.Date, '%Y-%m')
                            ) E ON  
                            D.Category_Name = E.Category_Name AND 
                            D.Max_Sale_Qty = E.Sale_Qty
                        ORDER BY 
                            D.Category_Name ASC;";

                        $result = mysqli_query($db, $query);
                        $count = mysqli_num_rows($result);
                        if ($count>0){
                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                        print "<tr>";
                        print "<td>{$row['Category_Name']}</td>";
                        print "<td>{$row['Max_Sale_Qty']}</td>";
                        print "<td>{$row['City_State']}</td>";
                        print "</tr>";
                        }
                        }
                        else{
                        print "<tr>";
                        print "<td colspan='5'>No record is found.</td>";
                        print "</tr>";
                        }
                        $result->free();
                        }
                        ?>
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
