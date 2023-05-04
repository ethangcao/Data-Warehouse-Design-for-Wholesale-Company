<?php

    include './lib/connection.php';
    $year = htmlspecialchars($_GET["Year"]);
    $city = htmlspecialchars($_GET["City_Name"]);
    $state = htmlspecialchars($_GET["State"]);

    $query = "
    SELECT
        STORE.Store_Number AS StoreNumber,
        STORE.Street_Address AS StreetAddress,
        CITY.City_Name AS CityName,
        CITY.City_State AS CityState,
        COUNT(MEMBERSHIP.Member_ID) AS Membership_Sold
    FROM
        MEMBERSHIP
    LEFT JOIN STORE ON MEMBERSHIP.Store_Number = STORE.Store_Number
    RIGHT JOIN CITY ON STORE.City_Name = CITY.City_Name and STORE.City_State = CITY.City_State
    WHERE
        YEAR(MEMBERSHIP.Date) = '$year' AND
        STORE.City_Name = '$city' AND
        STORE.City_State = '$state'
    GROUP BY
        MEMBERSHIP.Store_Number;
    ";

    $result = mysqli_query($db, $query);

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <title>City Store Membership Report</title>
</head>

<body>
  <div id="top" class="page" role="document">
    <header role="banner">
      <h1>PricePalace Data Warehouse</h1>
    </header>
    <main role="main">

      <section>
          <p><a href="./city_memberships_sold.php?Year=<?php echo htmlspecialchars($_GET["Year"])?>">[Back to City Memberships Sold Report]</a>
          <a href="./membership_trends_report.php">[Back to Membership Trends Report]</a>
          <a href="./">[Back to Main Menu]</a></p>
          <div align="center">
              <header role="banner">
                    <h1>Year - <?php echo htmlspecialchars($_GET["Year"]); ?></h1>
              </header>
          </div>
          <fieldset>
            <legend>City Store Report</legend>

            <table>
                <thead>
                    <tr>
                        <th>Store Number</th>
                        <th>Street Address</th>
                        <th>City Name</th>
                        <th>City State</th>
                        <th>Total Memberships Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){ ?>
                    <tr id="<?php echo $row['CityName'] ?>">
                        <td><?php echo $row['StoreNumber']?></td>
                        <td><?php echo $row['StreetAddress']?></td>
                        <td><?php echo $row['CityName']?></td>
                        <td><?php echo $row['CityState']?></td>
                        <td><?php echo $row['Membership_Sold']?></td>
                     <?php } ?>
                </tbody>
              </table>
          </fieldset>
          <p><a href="./city_memberships_sold.php?Year=<?php echo htmlspecialchars($_GET["Year"])?>">[Back to City Memberships Sold Report]</a>
          <a href="./membership_trends_report.php">[Back to Membership Trends Report]</a>
          <a href="./">[Back to Main Menu]</a></p>
      </section>
    </main>
    <footer role="contentinfo">
      <p>© 2020 CS6400 Team 042. All rights reserved.</p>
    </footer>
  </div>
  <script>
     <!-- window.scrollTo(0, document.getElementById('<?php echo $year ?>').offsetTop); -->
  </script>
</body>

</html>
<?php mysqli_close($db);  ?>
