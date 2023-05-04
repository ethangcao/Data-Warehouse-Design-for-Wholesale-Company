<?php

    include './lib/connection.php';

    $city = isset($_POST['cityName']) ? $_POST['cityName'] : null;
    $state = isset($_POST['state']) ? $_POST['state'] : null;


   if(!empty($city) && !empty($state)){
        $query = "
        SELECT
            CITY.City_Name AS CityName,
            CITY.City_State AS State,
            CITY.Population AS Population
        FROM
            CITY
        WHERE
            CITY.City_Name = '$city' AND
            CITY.City_State = '$state';
        ";

        $result = mysqli_query($db, $query);
    }

    $population = isset($_POST['population']) ? $_POST['population'] : null;

    if(!empty($population)){
         $updateQuery = "
                UPDATE
                    CITY
                SET
                    CITY.Population = $population
                WHERE
                    CITY.City_Name = '$city' AND
                    CITY.City_State = '$state';";

         $resultUpdate = mysqli_query($db, $updateQuery);
         if($resultUpdate) {
            $result = mysqli_query($db, $query);
            $success = "Population updated successfully.";
            $message = "Population updated successfully.";
         } else {
            $success = "Failed to update the population.";
            $message = "Failed to update the population.";
         }
    }
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <title>City Population</title>
</head>

<body>
  <div id="top" class="page" role="document">
    <header role="banner">
      <h1>PricePalace Data Warehouse</h1>
    </header>
    <main role="main">
      <div id="message"><?php if(isset($success)){ echo $message; } ?></div>
      <section>
          <p><a href="./">[Back to Main Menu]</a></p>

          <fieldset>
                      <legend>City Population</legend>

                        <form name="city_population" method="POST" onsubmit="return validateCityState()">
                          City Name: <input type="text" id="cityName" name="cityName" value="<?php echo $city ?>">
                          State: <input type="text" id="state" name="state" value="<?php echo $state ?>">
                          <input type="submit" value="Submit">
                        </form>

                        <br/>

                        <?php if(!empty($city) && !empty($state)){  ?>

                        <table>
                          <thead>
                              <tr>
                                  <th>City Name</th>
                                  <th>City State</th>
                                  <th>Population</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php if($result->num_rows == 0){ ?>
                              <tr>
                                  <td colspan="3">No record is found.</td>
                              </tr>
                              <?php } else { ?>
                              <?php while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){ ?>
                              <tr>
                                  <td><?php echo $row['CityName']?></td>
                                  <td><?php echo $row['State']?></td>
                                  <td><form name="population_form" method="POST" onsubmit="return validatePopulation()">
                                          <input type="text" id="population" name="population" value="<?php echo $row['Population']?>"></input>
                                          <input type="hidden" name="cityName" value="<?php echo $row['CityName']?>"></input>
                                          <input type="hidden" name="state" value="<?php echo $row['State']?>"></input>
                                          <input type="submit" value="Update Population">
                                        </form></td>
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
      function validateCityState() {
        let cityName = document.forms["city_population"]["cityName"].value;
        let state = document.forms["city_population"]["state"].value;

        if (!cityName || !state) {
          alert("City Name or State cannot be empty!");
          return false;
        }
      }

      function validatePopulation() {
        let population = document.forms["population_form"]["population"].value;

        if(!population || population <= 0) {
            alert("Please enter a valid Population value!");
            return false;
        }
      }
    </script>
</body>

</html>
<?php mysqli_close($db);  ?>
