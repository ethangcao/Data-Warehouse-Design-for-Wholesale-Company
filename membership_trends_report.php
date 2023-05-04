<?php

    include './lib/connection.php';

    $query = "
    SELECT
        COUNT(MEMBERSHIP.Member_ID) as Total_Membership,
        YEAR(MEMBERSHIP.Date) as Year
    FROM
        MEMBERSHIP
    GROUP BY
        YEAR(MEMBERSHIP.Date)
    ORDER BY
        YEAR(MEMBERSHIP.Date) DESC;
    ";

    $result = mysqli_query($db, $query);
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <title>Membership Trends Report</title>
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
            <legend>Membership Trends Report</legend>

            <table>
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Total Memberships Sold</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){ ?>
                    <tr id="<?php echo $row['Total_Membership'] ?>">
                        <td><?php echo $row['Year']?></td>
                        <td>
                            <a href="./city_memberships_sold.php?Year=<?php echo $row['Year']?>">
                                <?php echo $row['Total_Membership']?>
                            </a>
                        </td>
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
</body>

</html>
<?php mysqli_close($db);  ?>
