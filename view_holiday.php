<?php

    include './lib/connection.php';

    $query = "
    SELECT
        HOLIDAY.Holiday_Name AS holidayName,
        HOLIDAY.Date AS holidayDate
    FROM
        HOLIDAY;
    ";

    $result = mysqli_query($db, $query);

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <title>View Holidays</title>
</head>

<body>
  <div id="top" class="page" role="document">
    <header role="banner">
      <h1>PricePalace Data Warehouse</h1>
    </header>
    <main role="main">
      <section>
          <p><a href="./maintain_holidays.php">[Back to Maintain Holidays]</a>
          <a href="./">[Back to Main Menu]</a></p>

          <fieldset>
                      <legend> Holidays </legend>
                        <?php?>

                        <table>
                          <thead>
                              <tr>
                                  <th>Holiday Name</th>
                                  <th>Holiday Date</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){ ?>
                              <tr>
                                  <td><?php echo $row['holidayName']?></td>
                                  <td><?php echo $row['holidayDate']?></td>
                              </tr>
                              <?php } ?>
                          </tbody>
                        </table>
                        <?php?>
                    </fieldset>
          <p><a href="./maintain_holidays.php">[Back to Maintain Holidays]</a>
          <a href="./">[Back to Main Menu]</a></p>
      </section>
    </main>
    <footer role="contentinfo">
      <p>© 2020 CS6400 Team 042. All rights reserved.</p>
    </footer>
  </div>
</body>

</html>
<?php mysqli_close($db);  ?>
