<?php

    include './lib/connection.php';

    $holidayName = addslashes(isset($_POST['holidayName']) ? $_POST['holidayName'] : null);
    $holidayDate = addslashes(isset($_POST['holidayDate']) ? $_POST['holidayDate'] : null);

    if(!empty($holidayName) && !empty($holidayDate)){

         $exactHolidayExistsQuery = "
                    SELECT
                        HOLIDAY.Holiday_Name AS HolidayName,
                        HOLIDAY.Date AS HolidayDate
                    FROM
                        HOLIDAY
                    WHERE
                        HOLIDAY.Date = '$holidayDate' and HOLIDAY.Holiday_Name like '%$holidayName%';";

         $resultExactHolidayExists = mysqli_query($db, $exactHolidayExistsQuery);
         $row_cnt_eh = mysqli_num_rows($resultExactHolidayExists);

         if($row_cnt_eh>0) {
             $success = "Holiday already exists for the Date";
             $message = "Holiday already exists for the Date";
         } else {

             $holidayExistsQuery = "
                        SELECT
                            HOLIDAY.Holiday_Name AS HolidayName,
                            HOLIDAY.Date AS HolidayDate
                        FROM
                            HOLIDAY
                        WHERE
                            HOLIDAY.Date = '$holidayDate';";

             $resultHolidayExists = mysqli_query($db, $holidayExistsQuery);
             $row_cnt = mysqli_num_rows($resultHolidayExists);

             if($row_cnt>0){
                $row = mysqli_fetch_row($resultHolidayExists);

                $holidayNameAppend = addslashes($row[0]) . $holidayName;
                $updateHolidayQuery = "
                    UPDATE
                        HOLIDAY
                    SET
                        HOLIDAY.Holiday_Name = '$holidayNameAppend'
                    WHERE
                        HOLIDAY.Date = '$holidayDate';
                    ";

                 $resultUpdate = mysqli_query($db, $updateHolidayQuery);

                 if($resultUpdate) {
                    $success = "Holiday Updated successfully.";
                    $message = "Holiday Updated successfully.";
                 } else {
                    $success = "Failed to Update Holiday.";
                    $message = "Failed to Update Holiday.";
                 }
             } else {
                 $dateQuery = "
                 SELECT
                     DATE.Date
                 FROM
                     DATE
                 WHERE
                     DATE.Date = '$holidayDate';
                 ";

                 $resultDate = mysqli_query($db, $dateQuery);
                 $row_count = mysqli_num_rows($resultDate);
                 if ($row_count==0) {
                    $insertDateQuery = "
                    INSERT INTO
                        DATE
                    VALUES ('$holidayDate');
                    ";

                    $resultDateInsert = mysqli_query($db, $insertDateQuery);
                 }

                 $insertQuery = "
                        INSERT INTO
                            HOLIDAY
                        VALUES ('$holidayName', '$holidayDate');";

                 $resultInsert = mysqli_query($db, $insertQuery);

                 if($resultInsert) {
                     $success = "Holiday Added successfully.";
                     $message = "Holiday Added successfully.";
                  } else {
                     $success = "Failed to Add Holiday.";
                     $message = "Failed to Add Holiday.";
                  }
             }
          }
    }
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <title>Add Holiday</title>
</head>

<body>
  <div id="top" class="page" role="document">
    <header role="banner">
      <h1>PricePalace Data Warehouse</h1>
    </header>
    <main role="main">
      <div id="message"><?php if(isset($success)){ echo $message; } ?></div>
      <section>
          <p><a href="./maintain_holidays.php">[Back to Maintain Holidays]</a>
          <a href="./">[Back to Main Menu]</a></p>
                <fieldset>
                      <legend>Add Holiday</legend>
                        <form name="add_holiday" method="POST" onsubmit="return validateDate()">
                          Holiday Name: <input type="text" id="holidayName" name="holidayName">
                          Holiday Date: <input type="date" id="holidayDate" name="holidayDate">
                          <input type="submit" value="Submit">
                        </form>
                        <br/>
                </fieldset>
          <p><a href="./maintain_holidays.php">[Back to Maintain Holidays]</a>
          <a href="./">[Back to Main Menu]</a></p>
      </section>
    </main>
    <footer role="contentinfo">
      <p>© 2020 CS6400 Team 042. All rights reserved.</p>
    </footer>
  </div>
  <script>
        function validateDate() {
          let holidayDate = document.forms["add_holiday"]["holidayDate"].value;
          let holidayName = document.forms["add_holiday"]["holidayName"].value;
          if(!holidayName || !holidayDate) {
              alert("Please enter a valid Holiday Name and Date!");
              return false;
          }
        }
      </script>
</body>

</html>
<?php mysqli_close($db);  ?>
