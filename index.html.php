<!DOCTYPE html>

<html lang='ro'>
  <?php  include ("config.php"); ?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test</title>
    <style>
        body {
            background: paleturquoise;  
            font-family: sans-serif;
            display: grid;
            place-items: center; 
            margin: 0; 
            height: 100vh; 
        }
        h1 {
            color: darkgreen;
            box-shadow: 0px 1px rgba(0, 100, 0, 0.3); 
        }
        .mainDiv {
            background: lightcyan;  
            font-family: sans-serif;
            display: grid;
            place-items: center; 
            padding: 0px 50px 50px 50px;
            margin: 0 10px 0 10px;
            border: 1px solid darkgreen;
            border-radius: 12px;
            box-shadow: 3px 4px 6px rgb(0, 139, 139, 0.5), -0.5em 0px .4em rgb(0, 100, 0, 0.6);
        }
        input, select {
            float: right;
            margin: 0 0 0 30px;
        }
        .submitButton {
            margin: 12px;
            padding: 6px 20px;
            border-radius: 16px;
            border: 1px solid darkgreen;
            color: white;
            background-color: #4CAF50;
        }
        .error {
            color: #FF0000;
            size: 4px;
            float: left;
        }
    </style>
</head>

<body>

    <?php

        include ("config.php");

        // define variables and set to empty values
        $nameErr = $emailErr = $datetimeErr = $consultantErr = "";
        $name = $email = $datetime = $consultant = "";

        if ($_SERVER["REQUEST_METHOD"] == "POST") 
        {
            if (empty($_POST["name"])) {
              $nameErr = "*Name is required";
            } else {
              $name = test_input($_POST["name"]);
              // check if name only contains letters and whitespace
              if (!preg_match("/^[a-zA-Z-' ]*$/",$name)) {
                $nameErr = "*Only letters and white space allowed at name box";
              }
            }
            
            if (empty($_POST["email"])) {
              $emailErr = "*Email is required.";
            } else {
              $email = test_input($_POST["email"]);
              // check if e-mail address is well-formed
              if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailErr = "*Invalid email format";
              }
            }

            $myDate = date('m/d/Y H:i:s', time() + 60*60); // current time
            
            if (empty($_POST["datetime"])) {
                $datetimeErr = "*The date is required";
              } else {
                $datetime = test_input($_POST["datetime"]);
                // check if date NOT older than current date
                if (strtotime($myDate) > strtotime($datetime)) {
                  $datetimeErr = "*The appointment cannot be scheduled in the past";
                }
                // check if date is between Monday and Friday
                $dateParam = date('w',strtotime($datetime));
                //echo "<br> dateparam=", "$dateParam"; 
                if (($dateParam == 0) || ($dateParam == 6)) {
                  $datetimeErr = "*The appointment can be scheduled only between Monday and Friday";
                }
                // check if date is between 9:00-13:00 and 15:30-21:00
                $dateParam2 = date('H:i', strtotime($datetime));
                // echo "<br> dateparam2=", "$dateParam2"; 
                // echo "<br> dateparam3=", date('H:i', strtotime($dateParam3));
                $dateParam3 = "00:00:00";
                $dateParam33 = date('H:i', strtotime($dateParam3));
                $dateParam4 = "09:00:00";
                $dateParam44 = date('H:i', strtotime($dateParam4));
                $dateParam5 = "12:00:00"; /// ultima programare posibila din primul interval se poate face pana in ora 12:00 inclusiv
                $dateParam55 = date('H:i', strtotime($dateParam5));
                $dateParam6 = "15:30:00";
                $dateParam66 = date('H:i', strtotime($dateParam6));
                $dateParam7 = "20:00:00"; /// ultima programare posibila din al doilea interval se poate face pana in ora 20:00 inclusiv
                $dateParam77 = date('H:i', strtotime($dateParam7));
                if (($dateParam33 < $dateParam2) && ($dateParam2 < $dateParam44)) {$datetimeErr = "*The appointments are taking one hour and can be performed only between 9:00-13:00 and 15:30-21:00";}
                if (($dateParam55 < $dateParam2) && ($dateParam2 < $dateParam66)) {$datetimeErr = "*The appointments are taking one hour and can be performed only between 9:00-13:00 and 15:30-21:00";}
                if (($dateParam77 < $dateParam2)) {$datetimeErr = "*The appointments are taking one hour and can be performed only between 9:00-13:00 and 15:30-21:00";}

              } 

            if (isset($_POST['submitConsultant'])){
              $nameC = test_input($_POST["nameC"]);
              if (empty($nameC)) {
                echo "Please enter a valid name.";
                } else {
                  $sqli = "INSERT INTO employee(name) VALUES ('$nameC')";
                  if (mysqli_query($conn, $sqli)) {
                  echo "Consultant [", $nameC , "] has been added.";
                  }
                }
            }

            if (empty($_POST['consultant'])) {
                $consultantErr = "*The consultant is required";
                ///echo "<br> consultant=", $consultant;
              } else {
                $consultant = test_input($_POST["consultant"]);
              }

        }

        function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
          }

        $sql = "SELECT * from employee";
        $query = mysqli_query($conn, $sql);

        
    ?>

    <form action='' method="POST">
      <label for="consultant"> Add new consultant:</label>
      <input type="text" placeholder="Consultant name" name="nameC">
      <button type="submit" name="submitConsultant"> Add </button>  
    </form>
    
    <div class="mainDiv"> 

        <h1> Book-in procedure </h1> <br><br>
        <h4> Please fulfill the below form in order to have a consulting appointment.</h4>
        <br>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
        Name: <input type="text" name="name" value="<?php echo $name;?>">
        <br><br>

        E-mail: <input type="text" name="email" value="<?php echo $email;?>">
        <br><br>

        Date: <input type="datetime-local" name="datetime" value="<?php echo $datetime;?>">
        <br><br>

        Consultants: 
        <select name="consultant" value="<?php echo $consultant;?>">  
            <option value="">--- Select ---</option> 
            <?php
            while ($row = mysqli_fetch_array($query)) { 
              #code
            ?>  
            
            <option value="<?php echo $row['name']?>"><?php echo $row['name']?></option>
            
            <?php
            }
            ?> 
        </select> 
        <br><br>

        <input type="submit" name="submit" value="Submit" class="submitButton">  
        </form>
        <br>

        <span class="error"> <?php echo $nameErr;?> </span>
        <span class="error"> <?php echo $emailErr;?> </span>
        <span class="error"> <?php echo $datetimeErr;?> </span>
        <span class="error"> <?php echo $consultantErr;?> </span>
    </div>

    
    <br><br>

    <?php
        if (empty($nameErr) && empty($emailErr) && empty($datetimeErr) && empty($consultantErr)) {

          ///condition for datetime
          
          ///initial trebuie executata doar o singura data, atunci cand tabelul este gol
          $inserting = "INSERT INTO `appointments` (`consultant`, `client_name`, `client_email`, `datetime`) VALUES ('$consultant','$name','$email','$datetime')";
          mysqli_query($conn, $inserting);
          ///

          $sql2 = mysqli_query($conn, "SELECT `datetime` from `appointments`");
          $result_sql2 = array([]);

          //////////////////////////condition for datetime

          // while ($row_user = mysqli_fetch_assoc($sql2))
          //   $result_sql2[] = $row_user;
          // sort($result_sql2);

          // $min = $max = 0;
          // $datetime2 = strtotime($datetime);

          // foreach($result_sql2 as $value){ 
          //   $parapara = strtotime($value['datetime']);
          //   $parapara2 = date('H:i:s', strtotime($parapara));
          //   if (($parapara <= $datetime2) && ($min <= $parapara)) {  ///aflarea vecinului mic din cadrul sirului de date ordonate crescator(din linia 221)
          //     $min = $parapara;
          //   } 
          //   if (($parapara >= $datetime2) && ($max <= $parapara)) {  ///aflarea vecinului mare din cadrul sirului de date ordonate crescator(din linia 221)
          //     $max = $parapara;
          //   } 
          //   // echo $parapara . "<br>";
          //   // echo $parapara2 . "<br>";
          // }
          
          //if (($min + 5400 <= $datetime2) || ($datetime2 + 3600 <= $max - 1800)) {
           // $inserting = "INSERT INTO `appointments` (`consultant`, `client_name`, `client_email`, `datetime`) VALUES ('$consultant','$name','$email','$datetime')";
           // mysqli_query($conn, $inserting);
            echo '<div class="mainDiv">', "An appointment has been made on " , date('d/m/y H:i',strtotime($datetime)), " for " , $name , " (" , $email , ")" , " at consultant " , $consultant , ".", '</div>';
         // }  
        }
    ?>
    

</body>

</html>
