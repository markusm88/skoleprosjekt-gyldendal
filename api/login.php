<?php
require('dbconnection.php');
$postdata = file_get_contents("php://input");

//IF LOGIN API RECIEVES LOGIN REQUEST- START LOGIN
if (!empty($postdata)) {
    $user = json_decode($postdata);
    $username = mb_strtolower($user->username, 'UTF-8');
    $password = $user->password;
    
    //SEARCH STUDENT AND TEACHER DBs FOR THE REQUESTED USER
    $dbuser = getUserByUsername($db, $username);
    $dbteacher = getTeacherByUsername($db, $username);
    
    //CHECK IF FOUND USER EXISTS AS STUDENT OR TEACHER
    if (sizeof($dbuser)==0){
        if (sizeof($dbteacher)==0){
            echo 'user not found in db';
            
            exit(0);
        }
    }
    
    //IF USER IS CONFIRMED A STUDENT- START LOGIN PROCEDURE
    if (sizeof($dbuser)!=0){
        $dbuser = $dbuser[0];
        if ($dbuser['username'] == $username){
            //HASHING INSERTED PASSWORD AND COMPARE TO SAVED HASH
            if ($dbuser['password'] == hash('sha256', $password)){

                //AUTH OK. START SESSION AND SET ALL PARAMETERS
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION["userid"] = $dbuser['userid'];
                $_SESSION["schoolid"] = $dbuser['schoolid'];
                $_SESSION["classid"] = $dbuser['classid'];
                $_SESSION["username"] = $dbuser['username'];
                $_SESSION["name"] = $dbuser['name'];
                $_SESSION["surname"] = $dbuser['surname'];
                $_SESSION["classyear"] = $dbuser['classyear'];
                $_SESSION["email"] = $dbuser['email'];
                $_SESSION["grade"] = $dbuser['grade'];
                $_SESSION["userlvl"] = 1;

                //REPORT SESSION SUCCESS AND EXIT PROCEDURE
                echo 'success';
                exit(0);
            }
        }
    }
    
    //IF USER IS CONFIRMED A TEACHER- START LOGIN PROCEDURE
    if (sizeof($dbteacher)!=0){
        $dbteacher = $dbteacher[0];
        //var_dump($dbteacher);
        if ($dbteacher['username'] == $username){
            //HASHING INSERTED PASSWORD AND COMPARE TO SAVED HASH
            if ($dbteacher['password'] == hash('sha256', $password)){

                //AUTH OK. START SESSION AND SET ALL PARAMETERS
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION["teacherid"] = $dbteacher['teacherid'];
                $_SESSION["schoolid"] = $dbteacher['schoolid'];
                $_SESSION["username"] = $dbteacher['username'];
                $_SESSION["name"] = $dbteacher['name'];
                $_SESSION["surname"] = $dbteacher['surname'];
                $_SESSION["email"] = $dbteacher['email'];
                $_SESSION["cellphone"] = $dbteacher['cellphone'];
                $_SESSION["userlvl"] = 2;

                //REPORT SESSION SUCCESS AND EXIT PROCEDURE
                echo 'success';
                exit(0);
            }
        }
    }
    
    //ENDING UP HERE MEANS THAT USER WAS FOUND, BUT PASSWORD WAS INCORRECT.
    echo 'wrong password';
}

//GET THE USER WITH SPECIFIC USERNAME - RETURNS JSON ARRAY
function getUserByUsername($conn, $username) {
    $statement=$conn->prepare("SELECT users.*, classes.grade
FROM users
join classes on users.classid = classes.classid
WHERE users.username = '$username'");
    $statement->execute();
    $results=$statement->fetchAll(PDO::FETCH_ASSOC);
    
    return $results;
}

//GET THE TEACHER WITH SPECIFIC USERNAME - RETURNS JSON ARRAY
function getTeacherByUsername($conn, $username) {
    $statement=$conn->prepare("SELECT * FROM teachers WHERE teachers.username = '$username'");
    $statement->execute();
    $results=$statement->fetchAll(PDO::FETCH_ASSOC);

    return $results;
}

?>