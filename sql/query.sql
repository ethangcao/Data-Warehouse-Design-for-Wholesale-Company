## Main Menu
SELECT 
    (SELECT COUNT(STORE.Store_Number) FROM STORE) AS `Stores`, 
    (SELECT COUNT(MANUFACTURER.Manufacturer_Name) FROM MANUFACTURER) AS `Manufacturers`, 
    (SELECT COUNT(MEMBERSHIP.Member_ID) FROM MEMBERSHIP) AS `Memberships`, 
    (SELECT COUNT(PRODUCT.PID) FROM PRODUCT) AS `Products`;
    
## Manufacturer’s Product Report
SELECT 
            TRIM(MANUFACTURER.Manufacturer_Name) AS `Manufacturer Name`,
            MANUFACTURER.Maximum_Discount AS `Maximum Discount`, 
            COUNT(PRODUCT.Product_Name) AS `Product Count`, 
            ROUND(AVG(IFNULL(PRODUCT.Retail_Price, 0.00)), 2)  AS `Avg Retail Price`,
            MAX(IFNULL(PRODUCT.Retail_Price, 0.00)) AS `Max Retail Price`, 
            MIN(IFNULL(PRODUCT.Retail_Price, 0.00)) AS `Min Retail Price` 
        FROM 
            MANUFACTURER 
        LEFT JOIN PRODUCT ON 
            PRODUCT.Manufacturer_Name = MANUFACTURER.Manufacturer_Name 
        GROUP BY 
            MANUFACTURER.Manufacturer_Name 
        ORDER BY 
            `Avg Retail Price` DESC 
        LIMIT 100;
SELECT 
                PRODUCT.PID, 
                PRODUCT.Product_Name AS `Product Name`, 
                PRODUCT.Retail_Price AS `Product Retail Price`, 
                GROUP_CONCAT(
                    CATEGORY_OF.Category_Name ORDER BY CATEGORY_OF.Category_Name ASC 
                ) AS `Product Categories` 
            FROM 
                PRODUCT 
            LEFT JOIN CATEGORY_OF ON 
                PRODUCT.PID = CATEGORY_OF.PID 
            WHERE 
                PRODUCT.Manufacturer_Name = '$manufacturer_name'
            GROUP BY 
                PRODUCT.PID 
            ORDER BY 
                PRODUCT.Retail_Price DESC;
                
## Category Report
SELECT
            CO.Category_Name as `Category Name`,
            COUNT( IFNULL(P.PID, 0)) AS `Number of Products`,
            ROUND(AVG(IFNULL(P.Retail_Price, 0)), 2) AS `Average Retail Price`,
            COUNT(DISTINCT P.Manufacturer_Name) AS `Number of Unique Manufacturers`
        FROM
            CATEGORY_OF AS CO 
        LEFT JOIN PRODUCT AS P ON
            CO.PID = P.PID
        GROUP BY 
            CO.Category_Name
        ORDER BY
            CO.Category_Name ASC;
            
## Actual vs. Predicted Revenue for GPS Units Report
SELECT 
                P.PID, 
                P.Product_Name AS `Product Name`, 
                P.Retail_Price AS `Product Retail Price`,
                P.Total_Quantity AS `Units Sold`,
                P.Total_Quantity - IFNULL(S.Sale_Quantity, 0) AS `Units Sold at Retail Price`,
                IFNULL(S.Sale_Quantity, 0) AS `Units Sold at Sale Price`,
                ROUND( (P.Total_Quantity - IFNULL(S.Sale_Quantity, 0) ) * P.Retail_Price + IFNULL(S.Sale_Quantity, 0) * 0.75 * P.Retail_Price, 2) AS `Predicted Revenue`,
                ROUND( (P.Total_Quantity - IFNULL(S.Sale_Quantity, 0) ) * P.Retail_Price + IFNULL(S.Sale_Total, 0), 2) AS `Actual Revenue`,
                ROUND( IFNULL(S.Sale_Total, 0) - IFNULL(S.Sale_Quantity, 0) * 0.75 * P.Retail_Price, 2) AS `Revenue Difference`
            FROM
                (
                    SELECT
                        SOLD.PID AS PID, 
                        PRODUCT.Product_Name,
                        PRODUCT.Retail_Price, 
                        SUM(SOLD.Unit_Quantity) AS Total_Quantity
                    FROM 
                        SOLD NATURAL JOIN PRODUCT
                    WHERE 
                        SOLD.PID IN (SELECT PID FROM CATEGORY_OF WHERE Category_Name = 'GPS') AND  
                        (SOLD.Date BETWEEN '$start_date' AND '$end_date' )
                    GROUP BY 
                        SOLD.PID
                ) AS P LEFT JOIN
                (
                    SELECT
                        SOLD.PID, 
                        SUM(SOLD.Unit_Quantity) AS Sale_Quantity, 
                        SUM( ON_SALE.Sale_Price* SOLD.Unit_Quantity ) AS Sale_Total
                    FROM 
                        SOLD JOIN ON_SALE ON (SOLD.Date, SOLD.PID) = (ON_SALE.Date, ON_SALE.PID)
                        JOIN PRODUCT ON PRODUCT.PID = ON_SALE.PID
                    WHERE 
                        SOLD.PID IN (SELECT PID FROM CATEGORY_OF WHERE Category_Name = 'GPS') AND 
                        (SOLD.Date BETWEEN '$start_date' AND '$end_date' )
      
                    GROUP BY 
                        SOLD.PID
                ) AS S ON P.PID = S.PID 
            WHERE 
                ABS(  IFNULL(S.Sale_Total, 0) -  IFNULL(S.Sale_Quantity, 0) * 0.75 * P.Retail_Price  ) >= 5000
            ORDER BY 
                `Revenue Difference` DESC;
                
## Store Revenue by Year by State Report
SELECT
                        DISTINCT CITY .City_State AS State
                        FROM CITY
                        ORDER BY State;
SELECT
                                Store_Number AS `Store Number`,
                                Street_Address AS `Store Address`,
                                City_Name AS `City`,
                                YEAR( R .Date) AS `Sales Year`,
                                ROUND(SUM((IFNULL(ON_SALE.Sale_Price, Retail_Price) ) * Unit_Quantity), 2)
                                AS `Total Revenue`
                                FROM
                                (
                                SELECT
                                SOLD .PID,
                                Store_Number,
                                Date,
                                Unit_Quantity,
                                Retail_Price
                                FROM
                                SOLD
                                NATURAL JOIN PRODUCT
                                ) R
                                LEFT JOIN ON_SALE ON
                                ( R .Date, R .PID) = ( ON_SALE .Date, ON_SALE .PID)
                                NATURAL JOIN STORE
                                WHERE
                                STORE .City_State = '$state'
                                GROUP BY
                                Store_Number, YEAR( R .Date)
                                ORDER BY
                                `Sales Year` ASC, `Total Revenue` DESC;
                                
## Air Conditioners on Groundhog Day Report
SELECT
                    A .y AS `Year`,
                    A .Year_Total AS `Year Total`,
                    A .Year_Average AS `Average Day Sale`,
                    IFNULL( G .Groundhog, 0) AS `Groundhog Day Sale`
                    FROM
                    (
                    SELECT
                    YEAR(Date) AS y,
                    SUM(Unit_Quantity) AS Year_Total,
                    ROUND(SUM(Unit_Quantity) / 365, 2) AS Year_Average
                    FROM
                    SOLD
                    WHERE
                    PID IN (SELECT PID FROM CATEGORY_OF WHERE Category_Name = 'Air Conditioner')
                    GROUP BY
                    YEAR(Date)
                    ) A
                    LEFT JOIN
                    (
                    SELECT
                    YEAR(Date) AS y,
                    SUM(Unit_Quantity) AS Groundhog
                    FROM
                    SOLD
                    WHERE
                    PID IN (SELECT PID FROM CATEGORY_OF WHERE Category_Name = 'Air Conditioner') AND
                    DATE_FORMAT(Date, '%m%d') = '0202'
                    GROUP BY
                    YEAR(Date)
                    ) G ON A .y = G .y
                    ORDER BY
                    `Year` ASC;
                    
## State Highest Volume Report
SELECT DATE_FORMAT(DATE.Date, '%Y-%m') AS 'YearMonth' 
                        FROM DATE
                        GROUP BY DATE_FORMAT(DATE.Date, '%Y-%m');
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
                            D.Category_Name ASC;

## Revenue by Population Report

SELECT  T2.Revenue_Year,
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
Order by $order $sort;


## Membership Trends Report
SELECT
        COUNT(MEMBERSHIP.Member_ID) as Total_Membership,
        YEAR(MEMBERSHIP.Date) as Year
    FROM
        MEMBERSHIP
    GROUP BY
        YEAR(MEMBERSHIP.Date)
    ORDER BY
        YEAR(MEMBERSHIP.Date) DESC;
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

## Maintain Holidays
SELECT
                        HOLIDAY.Holiday_Name AS HolidayName,
                        HOLIDAY.Date AS HolidayDate
                    FROM
                        HOLIDAY
                    WHERE
                        HOLIDAY.Date = '$holidayDate' and HOLIDAY.Holiday_Name like '%$holidayName%';
SELECT
                            HOLIDAY.Holiday_Name AS HolidayName,
                            HOLIDAY.Date AS HolidayDate
                        FROM
                            HOLIDAY
                        WHERE
                            HOLIDAY.Date = '$holidayDate';
UPDATE
                        HOLIDAY
                    SET
                        HOLIDAY.Holiday_Name = '$holidayNameAppend'
                    WHERE
                        HOLIDAY.Date = '$holidayDate';
INSERT INTO
                            HOLIDAY
                        VALUES ('$holidayName', '$holidayDate');
                        
## city population
SELECT
            CITY.City_Name AS CityName,
            CITY.City_State AS State,
            CITY.Population AS Population
        FROM
            CITY
        WHERE
            CITY.City_Name = '$city' AND
            CITY.City_State = '$state';
UPDATE
                    CITY
                SET
                    CITY.Population = $population
                WHERE
                    CITY.City_Name = '$city' AND
                    CITY.City_State = '$state';