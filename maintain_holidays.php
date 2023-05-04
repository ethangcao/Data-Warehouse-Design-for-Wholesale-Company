<?php
    if (isset($_POST['ViewHoliday']))
    {
        header("Location: ./view_holiday.php");
        exit();
    } else if (isset($_POST['AddHoliday']))
    {
        header("Location: ./add_holiday.php");
        exit();
    }
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <title>Maintain Holidays</title>
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
                  <legend>Maintain Holidays </legend>

                    <form name="maintain_holiday" method="POST">
                      <input type="submit" name="AddHoliday" value="Add Holiday">
                      <input type="submit" name="ViewHoliday" value="View Holiday">
                    </form>
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
