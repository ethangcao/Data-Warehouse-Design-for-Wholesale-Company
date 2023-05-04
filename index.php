<?php

    include './lib/connection.php';

    $query = "
    SELECT 
    (SELECT COUNT(STORE.Store_Number) FROM STORE) AS `Stores`, 
    (SELECT COUNT(MANUFACTURER.Manufacturer_Name) FROM MANUFACTURER) AS `Manufacturers`, 
    (SELECT COUNT(MEMBERSHIP.Member_ID) FROM MEMBERSHIP) AS `Memberships`, 
    (SELECT COUNT(PRODUCT.PID) FROM PRODUCT) AS `Products`;
    ";

    $result = mysqli_query($db, $query);

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <title>PricePalace Data Warehouse</title>
</head>

<body>
  <div id="top" class="page" role="document">
    <header role="banner">
      <h1>PricePalace Data Warehouse</h1>
      <p>Hello and welcome! This is the main menu for the project in CS6400. This project is brought to you by Team 042.
      </p>
    </header>
    <main role="main">

            <table>
                <thead>
                    <tr>
                        <th>Store Count</th>
                        <th>Manufacturer Count</th>
                        <th>Membership Count</th>
                        <th>Product Count</th>
                    </tr>                
                </thead>
                <tbody>
                     <?php while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){ ?>
                    <tr>
                        <td><?php echo $row['Stores']?></td>  
                        <td><?php echo $row['Manufacturers']?></td>
                        <td><?php echo $row['Memberships']?></td>
                        <td><?php echo $row['Products']?></td>
                    </tr>  
                    <?php } ?>
                </tbody>
              </table>

      <section>
          <fieldset>
            <legend>Main Menu</legend>

            <div class="row">
              <div class="column" style="background-color:#aaa;">
                <a href="./manufacturer_report.php">
                  <h2>Manufacturer’s Product Report</h2>
                </a>
              </div>

              <div class="column" style="background-color:#c4e0e5;">
                <a href="./category_report.php">
                  <h2>Category Report</h2>
                </a>
              </div>

              <div class="column" style="background-color:#ccc;">
                <a href="./actual_vs_predicted_revenue_for_gps_units_report.php">
                  <h2>Actual versus Predicted Revenue for GPS units Report</h2>
                </a>
              </div>

              <div class="column" style="background-color:#eecda3;">
                <a href="./store_revenue_by_year_by_state_report.php">
                  <h2>Store Revenue by Year by State Report</h2>
                </a>
              </div>

              <div class="column" style="background-color:#ffedbc;">
                <a href="./air_conditioners_on_groundhog_day_report.php">
                  <h2>Air Conditioners on Groundhog Day Report</h2>
                </a>
              </div>

              <div class="column" style="background-color:#00cdac;">
                <a href="./state_with_highest_volume_for_each_category_report.php">
                  <h2>State with Highest Volume for each Category Report</h2>
                </a>
              </div>

              <div class="column" style="background-color:#e29587;">
                <a href="./revenue_by_population_report.php">
                  <h2>Revenue by Population Report</h2>
                </a>
              </div>

              <div class="column" style="background-color:#ddd6f3;">
                <a href="./membership_trends_report.php">
                  <h2>Membership Trends Report</h2>
                </a>
              </div>

              <div class="column" style="background-color:#ffaf7b">
                <a href="./maintain_holidays.php">
                  <h2>Maintain Holidays</h2>
                </a>
              </div>

              <div class="column" style="background-color:#c4e0e5;">
                <a href="./city_population.php">
                  <h2>City Population</h2>
                </a>
              </div>

            </div>
          </fieldset>

      </section>
    </main>
    <footer role="contentinfo">
      <p>© 2020 CS6400 Team 042. All rights reserved.</p>
    </footer>
  </div>
</body>

</html>
