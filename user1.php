<?php
$success = true;
$db_conn = OCILogon(
    "ora_v8f1b",
    "a56890163",
    "dbhost.ugrad.cs.ubc.ca:1522/ug"
);

function executePlainSQL($cmdstr)
{
    // Take a plain (no bound variables) SQL command and execute it.
    //echo "<br>running ".$cmdstr."<br>";
    global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr);
    // There is a set of comments at the end of the file that 
    // describes some of the OCI specific functions and how they work.

    if (!$statement) {
        echo "<br>Cannot parse this command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn);
        // For OCIParse errors, pass the connection handle.
        echo htmlentities($e['message']);
        $success = false;
    }

    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r) {
        echo "<br>Cannot execute this command: " . $cmdstr . "<br>";
        $e = oci_error($statement);
        // For OCIExecute errors, pass the statement handle.
        echo htmlentities($e['message']);
        $success = false;
    } else { }
    return $statement;
}


function executeBoundSQL($cmdstr, $list)
{
    /* Sometimes the same statement will be executed several times.
        Only the value of variables need to be changed.
	   In this case, you don't need to create the statement several
        times.  Using bind variables can make the statement be shared
        and just parsed once.
        This is also very useful in protecting against SQL injection
        attacks.  See the sample code below for how this function is
        used. */

    global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr);

    if (!$statement) {
        echo "<br>Cannot parse this command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn);
        echo htmlentities($e['message']);
        $success = false;
    }

    foreach ($list as $tuple) {
        foreach ($tuple as $bind => $val) {
            //echo $val;
            //echo "<br>".$bind."<br>";
            OCIBindByName($statement, $bind, $val);
            unset($val); // Make sure you do not remove this.
            // Otherwise, $val will remain in an 
            // array object wrapper which will not 
            // be recognized by Oracle as a proper
            // datatype.
        }
        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute this command: " . $cmdstr . "<br>";
            $e = OCI_Error($statement);
            // For OCIExecute errors pass the statement handle
            echo htmlentities($e['message']);
            echo "<br>";
            $success = false;
        }
    }
}

function printResult($result)
{ //prints results from a select statement
    echo "<br>Got data from table tab1:<br>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th></tr>Age</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["NID"] . "</td><td>" . $row["NAME"] . "</td></tr>" . $row["AGE"] . "</td></tr>"; //or just use "echo $row[0]" 
    }
    echo "</table>";
}

function printUser($resultFromSQL, $namesOfColumnsArray)
{
    echo "<table class='table table-hover'>";
    echo "<tr>";
    // iterate through the array and print the string contents
    // foreach ($namesOfColumnsArray as $name) {
    //     echo "<tu>$name</tu>";
    // }
    // echo "</tr>";
    echo "<tr><th>UserID</th>     <th>Name</th></tr>";

    while ($row = OCI_Fetch_Array($resultFromSQL, OCI_BOTH)) {
        echo "<tr>";
        $string = "";

        // iterates through the results returned from SQL query and
        // creates the contents of the table
        for ($i = 0; $i < sizeof($namesOfColumnsArray); $i++) {
            $string .= "<td>" . $row["$i"] . "</td>";
        }
        echo $string;
        echo "</tr>";
    }
    echo "</table>";
}


function printTable($resultFromSQL, $namesOfColumnsArray)
{
    echo "<table class='table table-hover'>";
    echo "<tr>";
    // iterate through the array and print the string contents
    foreach ($namesOfColumnsArray as $name) {
        echo "<th>$name</th>";
    }
    echo "</tr>";

    while ($row = OCI_Fetch_Array($resultFromSQL, OCI_BOTH)) {
        echo "<tr>";
        $string = "";

        // iterates through the results returned from SQL query and
        // creates the contents of the table
        for ($i = 0; $i < sizeof($namesOfColumnsArray); $i++) {
            $string .= "<td>" . $row["$i"] . "</td>";
        }
        echo $string;
        echo "</tr>";
    }
    echo "</table>";
}

// $re = executePlainSQL("select userID,name from Users where userID = 001");
// //TODO: select user info here sql
// $co = array("UserId", "Name");

// printUser($re, $co);

// Connect Oracle...
if ($db_conn) {
    
			if (array_key_exists('updateage', $_POST)) {
        // Update tuple using data from user
        $tuple = array(
            ":bind1" => $_POST['Age'],
        );
        $alltuples = array(
            $tuple
        );
        executeBoundSQL("update Users set age=:bind1 where userID = 001", $alltuples);
        OCICommit($db_conn);
    }
    //TODO: add other update value here and update the table using update statement in sql
    else if (array_key_exists('updategender', $_POST)) {
        $tuple = array(
            ":bind2" => $_POST['Gender'],
        );
        $alltuples = array(
            $tuple
        );
        executeBoundSQL("update Users set gender=:bind2 where userID = 001", $alltuples);
        OCICommit($db_conn);
    }

    // update activitylevel
    else if (array_key_exists('updateal', $_POST)) {
        $tuple = array(
            ":bind3" => $_POST['ActivityLevel'],
        );
        $alltuples = array(
            $tuple
        );
        executeBoundSQL("update Users set activityLevel=:bind3 where userID = 001", $alltuples);
        OCICommit($db_conn);
    }

    // update email
    else if (array_key_exists('updateemail', $_POST)) {
        $tuple = array(
            ":bind4" => $_POST['Email'],
        );
        $alltuples = array(
            $tuple
        );
        executeBoundSQL("update Users set email=:bind1 where userID = 001", $alltuples);
        OCICommit($db_conn);
    }

    // update nutritional_goal
    else if (array_key_exists('updateng', $_POST)) {
        $tuple = array(
            ":bind5" => $_POST['NutritionalGoal'],
        );
        $alltuples = array(
            $tuple
        );
        executeBoundSQL("update Users set nutritional_goal=:bind5 where userID = 001", $alltuples);
        OCICommit($db_conn);
    }

    // update weight_goal
    else if (array_key_exists('updatewg', $_POST)) {
        $tuple = array(
            ":bind6" => $_POST['WeightGoal'],
        );
        $alltuples = array(
            $tuple
        );
        executeBoundSQL("update Users set weight_goal=:bind6 where userID = 001", $alltuples);
        OCICommit($db_conn);
    } else if (array_key_exists('insertweightAndDate', $_POST)) {

        $tuple = array(
            ":weight" => $_POST['Weight'],
            ":dater" => $_POST['Datee'],
            ":mt" => $_POST['MT'],
            ":iname" => $_POST['Ingredent'],
            ":amount" => $_POST['Amount'],

        );
        $alltuples = array(
            $tuple

        );
        executeBoundSQL("insert into Logged_DailyWeight(userID, in_date, weight) values (004, :dater, :weight)
                ", $alltuples);
        OCICommit($db_conn);

       
        executeBoundSQL("insert into Logged_DailyMeals(userID, in_date, mealType) values(004, :dater, :mt )", $alltuples);
        OCICommit($db_conn);
        executeBoundSQL("insert into MakesUp select IID, 004, :dater, :mt, :amount from Ingredients where Iname = :iname", $alltuples);
        //always say not bounded?????
        OCICommit($db_conn);
    }

    // if ($_POST && $success) {
    //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
    // header("location: user.php");
    // } else {
    // Select data...
    // $result = executePlainSQL("select age,gender, activityLevel, email, nutritional_goal, weight_goal from Users where userID=1");
    //printResult($result);
    /* next two lines from Raghav replace previous line */
    // $columnNames = array("Age", "Gender", "Activity Level", "Email", "NutritionalGoal", "WeightGoal");
    // printTable($result, $columnNames);
    // }

    //Commit to save changes...
    // OCILogoff($db_conn);
} else {
    echo "cannot connect";
    $e = OCI_Error(); // For OCILogon errors pass no handle
    echo htmlentities($e['message']);
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>My Food Logger</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('https://columbiaairport.com/wp-content/uploads/2015/12/CAE-Website-Full-Background-Texture1902x1200-9.jpg');
        }

        .ingredientThumbnail {
            width: 50px;
            height: 50px;
        }

        .footer-inverse {
            min-height: 50px;
            background-color: #222;
            border-color: #080808;
            position: relative;
            margin-top: 20px;
            border: 1px solid transparent;
        }

        .footerMessage {
            color: #9d9d9d;
            text-align: center;
            padding: 10px;
        }

        @media (min-width: 768px) {
            .footer-inverse {
                border-radius: 4px;
            }
        }
    </style>
</head>

<nav class="navbar navbar-inverse">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">My Food Logger</a>
        </div>
        <ul class="nav navbar-nav">
            <li class="active"><a href="user.php">User</a></li>
            <li><a href="admin.php">Admin</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="#"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
            <li><a href="sign-in.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
        </ul>
    </div>
</nav>

<body>
    <div class="container">
        <div class="userInfo">
            <h1>User Info</h1>
            <?php
            global $db_conn, $success;
            if ($db_conn) {
                if ($_POST && $success) {
                    //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
                    header("location: user.php");
                } else {
                    $re = executePlainSQL("select userID, name from Users where userID = 001");
                    //TODO: select user info here sql
                    $co = array("UserId", "Name");

                    printUser($re, $co);
                    // Select data...


                    $result = executePlainSQL("select age,gender,activityLevel,email,nutritional_goal,weight_goal from Users where userID=1");
                    //printResult($result);
                    /* next two lines from Raghav replace previous line */
                    $columnNames = array("Age", "Gender", "Activity Level", "Email", "NutritionalGoal", "WeightGoal");
                    printTable($result, $columnNames);
                }
            } else {
                echo "Error connecting to database...";
            }
            ?>
        </div>

        <h1>Update Personal Information</h1>
        <form method="POST" action="user.php">
            <div class="row">
                <div class="form-group col-xs-2">
                    <label for="Age">Age</label>
                    <input type="text" name="Age" class="form-control">
                    <input type="submit" value="update" name="updateage" class="form-control btn-primary">
                </div>
                <div class="form-group col-xs-2">
                    <label for="Gender">Gender</label>
                    <input type="text" name="Gender" class="form-control">
                    <input type="submit" value="update" name="updategender" class="form-control btn-primary">
                </div>
                <div class="form-group col-xs-2">
                    <label for="updateal">Activity Level</label>
                    <input type="text" name="ActivityLevel" class="form-control">
                    <input type="submit" value="update" name="updateal" class="form-control btn-primary">
                </div>
                <div class="form-group col-xs-2">
                    <label for="updateemail">Email</label>
                    <input type="text" name="Email" class="form-control">
                    <input type="submit" value="update" name="updateemail" class="form-control btn-primary">
                </div>
                <div class="form-group col-xs-2">
                    <label for="updateng">Nutritional Goal</label>
                    <input type="text" name="NutritionalGoal" class="form-control">
                    <input type="submit" value="update" name="updateng" class="form-control btn-primary">
                </div>
                <div class="form-group col-xs-2">
                    <label for="updatewg">Weight Goal</label>
                    <input type="text" name="WeightGoal" class="form-control">
                    <input type="submit" value="update" name="updatewg" class="form-control btn-primary">
                </div>
            </div>
        </form>


        <h1>Let's Log Your Daily Meals and Weight</h1>
        <form method="POST" action="user.php">
            <div class="DailyLogger row">
                <div class="form-group col-xs-2">
                    <label for="Weight">Weight</label>
                    <input type="text" name="Weight" class="form-control">
                </div>
                <div class="form-group col-xs-2">
                    <label for="Datee">Date</label>
                    <input type="text" name="Datee" class="form-control">
                </div>
                <div class="form-group col-xs-2">
                    <label for="MT">Meal Type</label>
                    <input type="text" name="MT" class="form-control">
                </div>
                <div class="form-group col-xs-2">
                    <label for="Ingredent">Ingredient</label>
                    <input type="text" name="Ingredent" class="form-control">
                </div>
                <div class="form-group col-xs-2">
                    <label for="Amount">Amount</label>
                    <input type="text" name="Amount" class="form-control">
                </div>
                <div class="form-group col-xs-2">
                    <input type="submit" value="insert" name="insertweightAndDate" class="form-control btn-success">
                </div>
            </div>
        </form>

        <div class="mealHistory">
            <h1>Meal History</h1>

            <form method="GET" action="user.php">
                <input type="submit" value="ShowDailyMeal" name="DatesMeals" class="form-control btn-primary"></p>
            </form>
            <?php
            global $db_conn, $success;
            if ($db_conn) {
                if (array_key_exists('DatesMeals', $_GET)) {
                    $result = executePlainSQL("select m.in_date, sum(i.calorie), sum(i.sugar), sum(i.fiber), count(*)  from  MakesUp m, Ingredients i where  m.IID = i.IID and userID = 004 group by m.in_date order by m.in_date");
                    $columnNames = array("Date", "Total calorie taken ", "Total sugar taken", "Total fiber taken", "number of different food eaten");
                    printTable($result, $columnNames);

                    OCICommit($db_conn);
                }
            } else {
                echo "Error connecting to database...";
            }
            ?>
        </div>

        <h1>Personal Adviosr and Today's Suggested Recepie</h1>
        <form method="GET" action="user.php">
            <input type="submit" value="Show Personal Advisor and Suggested Recepie" name="getspecialist" class="form-control btn-primary">
        </form>
        <?php
        global $db_conn, $success;
        if ($db_conn) {
            if (array_key_exists('getspecialist', $_GET)) {
                $result = executePlainSQL("select fs.Fname, fs.email, a.Rname, r.cook_time, r.cooking_level, r.image_url from FoodSpecialist fs, Advices a, SuggestedRecipe r where a.EID = fs.EID and a.Rname = r.Rname and a.userID = 001");
                $columnNames = array("My speciallist", "contact Info ", "Suggested Recepie", "Cook Time", "Cooking Level", "Content");
                printTable($result, $columnNames);

                OCICommit($db_conn);
            }
        } else {
            echo "Error connecting to database...";
        }
        ?>
    </div>

    <div class='footer-inverse'>
        <p class="footerMessage">CPSC 320 Term Project Group 32</p>
    </div>

</body> 