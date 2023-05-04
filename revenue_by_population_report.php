
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <title>Revenue by Population Report</title>
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
            <legend>Revenue by Population Report</legend>

<?php 
include './lib/connection.php';

//check to see if the variables are set, check if they are exist
if (isset($_GET['order'])){
    $order = $_GET['order'];
}else{
    $order = 'Revenue_Year';
}

if(isset($_GET['sort'])){
    $sort = $_GET['sort'];
}else{
    $sort = 'ASC';
}

//sql query 
$resultSet = $db ->query("SELECT  T2.Revenue_Year,
concat('$',format(SUM(CASE WHEN (T2.City_Population_Category = 'Small') THEN T2.Purchase_Revenue  ELSE NULL END),2)) AS 'Small',
concat('$',format(SUM(CASE WHEN (T2.City_Population_Category = 'Medium') THEN T2.Purchase_Revenue ELSE NULL END),2)) AS 'Medium',
concat('$',format(SUM(CASE WHEN (T2.City_Population_Category = 'Large') THEN T2.Purchase_Revenue ELSE NULL END),2)) AS 'Large',
concat('$',format(SUM(CASE WHEN (T2.City_Population_Category = 'Extra Large') THEN T2.Purchase_Revenue ELSE NULL END),2)) AS 'Extra_Large'
FROM
(SELECT YEAR(T1.Purchase_Date) AS 'Revenue_Year', T1.Store_Number, (T1.Real_price * T1.Unit_Quantity) AS 'Purchase_Revenue', Ct.Population,
CASE WHEN Population < 3700000 THEN 'Small'
WHEN Population BETWEEN 3700000 AND  6700000 THEN 'Medium'
WHEN Population BETWEEN 6700000 AND  9000000 THEN 'Large'
ELSE 'Extra Large'
END AS 'City_Population_Category'
FROM
(Select R.PID, R.Store_Number, R.Purchase_Date, R.Unit_Quantity, ifnull(OS.Sale_Price, R.Retail_Price) AS Real_Price
 from
(SELECT S.PID, S.Store_Number, S.Date  AS Purchase_Date, S.Unit_Quantity, P.Retail_Price
FROM SOLD S NATURAL JOIN Product P) R
LEFT OUTER JOIN ON_SALE OS ON (R.Purchase_Date, R.PID) = (OS.Date, OS.PID)) T1
LEFT OUTER JOIN Store St ON (T1.Store_Number = St.Store_Number)
LEFT OUTER JOIN CITY Ct ON (Ct.City_Name = St.City_Name and Ct.City_State = St.City_State)) T2
Group by T2.Revenue_Year
Order by $order $sort");

$count = mysqli_num_rows($resultSet);               
if($count > 0){
   
    $sort == 'DESC' ? $sort = 'ASC' : $sort = 'DESC';
    
    echo"
    <table class = 'center'>
        <tr>
            <th><a href='?order=Revenue_Year&&sort=$sort'>Revenue Year</th>
            <th>Small</th>
            <th>Medium</th>
            <th>Large</th>
            <th>Extra Large</th>
            
    ";
    while($rows = $resultSet->fetch_assoc())
    {
        $Revenue_Year = $rows['Revenue_Year'];
        $Small = $rows['Small'];
        $Medium = $rows['Medium'];
        $Large = $rows['Large'];
        $Extra_Large = $rows['Extra_Large'];
        echo"
        <tr>
            <td>$Revenue_Year</td>
            <td>$Small</td>
            <td>$Medium</td>
            <td>$Large</td>
            <td>$Extra_Large</td>
        ";
    }echo "</table>";
        } else {
        print "<tr>";
        print "<td colspan='5'>No record is found.</td>";
        print "</tr>";
}$resultSet->free();
?>
<p><a href="./">[Back to Main Menu]</a></p>
</section>
</main>
<footer role="contentinfo">
<p>© 2020 CS6400 Team 042. All rights reserved.</p>
</footer>
</div>
</body>
</html>


    

    
