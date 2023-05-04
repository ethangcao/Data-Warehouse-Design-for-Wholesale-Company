<?php

    include './lib/connection.php';
    $year = isset($_GET['Year']) ? htmlspecialchars($_GET["Year"]) : null;

    if(empty($year)){
        echo 'Parameter "Year" is missing. Please try again.'; 
        die;
    }

    $queryDesc = "
    SELECT
        STORE.City_Name AS CityName,
        STORE.City_State AS State,
        COUNT(MEMBERSHIP.Member_ID) AS Membership_Sold,
        COUNT(DISTINCT MEMBERSHIP.Store_Number) AS Store_Count
    FROM
        MEMBERSHIP
    LEFT JOIN STORE ON MEMBERSHIP.Store_Number = STORE.Store_Number
    WHERE
        YEAR(MEMBERSHIP.Date) = '$year'
    GROUP BY
        STORE.City_Name, STORE.City_State
    ORDER BY
        Membership_Sold DESC
    LIMIT 25;
    ";

    $resultDesc = mysqli_query($db, $queryDesc);

    $queryAsc = "
        SELECT
        STORE.City_Name AS CityName,
        STORE.City_State AS State,
        COUNT(MEMBERSHIP.Member_ID) AS Membership_Sold,
        COUNT(DISTINCT MEMBERSHIP.Store_Number) AS Store_Count
    FROM
        MEMBERSHIP
    LEFT JOIN STORE ON MEMBERSHIP.Store_Number = STORE.Store_Number
    WHERE
        YEAR(MEMBERSHIP.Date) = '$year'
    GROUP BY
        STORE.City_Name, STORE.City_State
    ORDER BY
        Membership_Sold ASC
    LIMIT 25;
        ";

    $resultAsc = mysqli_query($db, $queryAsc);
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <title>City Memberships Sold Report</title>
</head>

<body>
  <div id="top" class="page" role="document">
    <header role="banner">
      <h1>PricePalace Data Warehouse</h1>
    </header>
    <main role="main">

      <section>
          <p><a href="./membership_trends_report.php">[Back to Membership Trends Report]</a> <a href="./">[Back to Main Menu]</a></p>
          <div align="center">
              <header role="banner">
                    <h1>Year - <?php echo htmlspecialchars($year); ?></h1>
              </header>
          </div>
          <fieldset>
            <legend>City Memberships Sold Report - Most</legend>

            <table>
                <thead>
                    <tr>
                        <th>City Name</th>
                        <th>State Name</th>
                        <th>Total Memberships Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_array($resultDesc, MYSQLI_ASSOC)){ ?>
                    <tr id="<?php echo $row['CityName'] ?>">
                        <td><?php echo $row['CityName']?></td>
                        <td><?php echo $row['State']?></td>
                        <?php if($row['Store_Count'] > 1) {?>
                            <?php if($row['Membership_Sold'] >= 250) {?>
                                <td style="background-color: #ff4646">
                                    <a href="./city_store.php?City_Name=<?php echo $row['CityName']?>&State=<?php echo $row['State']?>&Year=<?php echo htmlspecialchars($_GET["Year"])?>">
                                        <?php echo $row['Membership_Sold']?>
                                    </a>
                                </td>
                            <?php } else if($row['Membership_Sold'] <= 30) { ?>
                                <td style="background-color: YELLOW">
                                    <a href="./city_store.php?City_Name=<?php echo $row['CityName']?>&State=<?php echo $row['State']?>&Year=<?php echo htmlspecialchars($_GET["Year"])?>">
                                        <?php echo $row['Membership_Sold']?>
                                    </a>
                                </td>
                            <?php } else { ?>
                                <td>
                                    <a href="./city_store.php?City_Name=<?php echo $row['CityName']?>&State=<?php echo $row['State']?>&Year=<?php echo htmlspecialchars($_GET["Year"])?>">
                                        <?php echo $row['Membership_Sold']?>
                                    </a>
                                </td>
                            <?php }?>
                        <?php } else { ?>
                                <?php if($row['Membership_Sold'] >= 250) {?>
                                    <td style="background-color: RED"><?php echo $row['Membership_Sold']?></td>
                                <?php } else if($row['Membership_Sold'] <= 30) { ?>
                                    <td style="background-color: YELLOW"><?php echo $row['Membership_Sold']?></td>
                                <?php } else { ?>
                                    <td><?php echo $row['Membership_Sold']?></td>
                                <?php }?>
                        <?php }?>
                    </tr>
                     <?php } ?>
                </tbody>
              </table>
          </fieldset>
          <br>
          <fieldset>
              <legend>City Memberships Sold Report - Least</legend>

              <table>
                  <thead>
                      <tr>
                          <th>City Name</th>
                          <th>State Name</th>
                          <th>Total Memberships Sold</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php while ($row = mysqli_fetch_array($resultAsc, MYSQLI_ASSOC)){ ?>
                      <tr id="<?php echo $row['CityName'] ?>">
                          <td><?php echo $row['CityName']?></td>
                          <td><?php echo $row['State']?></td>
                          <?php if($row['Store_Count'] > 1) {?>
                              <?php if($row['Membership_Sold'] >= 250) {?>
                                  <td style="background-color: RED">
                                      <a href="./city_store.php?City_Name=<?php echo $row['CityName']?>&State=<?php echo $row['State']?>&Year=<?php echo htmlspecialchars($_GET["Year"])?>">
                                          <?php echo $row['Membership_Sold']?>
                                      </a>
                                  </td>
                              <?php } else if($row['Membership_Sold'] <= 30) { ?>
                                  <td style="background-color: YELLOW">
                                      <a href="./city_store.php?City_Name=<?php echo $row['CityName']?>&State=<?php echo $row['State']?>&Year=<?php echo htmlspecialchars($_GET["Year"])?>">
                                          <?php echo $row['Membership_Sold']?>
                                      </a>
                                  </td>
                              <?php } else { ?>
                                  <td>
                                      <a href="./city_store.php?City_Name=<?php echo $row['CityName']?>&State=<?php echo $row['State']?>&Year=<?php echo htmlspecialchars($_GET["Year"])?>">
                                          <?php echo $row['Membership_Sold']?>
                                      </a>
                                  </td>
                              <?php }?>
                          <?php } else { ?>
                                  <?php if($row['Membership_Sold'] >= 250) {?>
                                      <td style="background-color: RED"><?php echo $row['Membership_Sold']?></td>
                                  <?php } else if($row['Membership_Sold'] <= 30) { ?>
                                      <td style="background-color: YELLOW"><?php echo $row['Membership_Sold']?></td>
                                  <?php } else { ?>
                                      <td><?php echo $row['Membership_Sold']?></td>
                                  <?php }?>
                          <?php }?>
                      </tr>
                       <?php } ?>
                  </tbody>
                </table>
          </fieldset>
          <p><a href="./membership_trends_report.php">[Back to Membership Trends Report]</a> <a href="./">[Back to Main Menu]</a></p>
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
