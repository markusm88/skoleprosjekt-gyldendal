<?php
    require('dbconnection.php');
    $postdata = file_get_contents("php://input");
    date_default_timezone_set('Europe/Copenhagen');

//GET PARAMETER CHECKS
    if (!empty($_GET)){
        
        $request = $_GET["request"];
        
        
//---------------------------------------------------------------------------------------------
//--------------------SETTING UP ALL GET REQUEST FUNCTIONS FOR API-----------------------------
//---------------------------------------------------------------------------------------------
        if ($_GET["request"] == 'getUsersBySchoolId'){
            echo getUsersBySchoolId($db, $_GET["id"]);
            exit; //end ajax request here
        } else if ($_GET["request"] == 'getUserById') {
            echo getUserById($db, $_GET["id"]);
            exit;
        } else if ($_GET["request"] == 'getUserByUsername') {
            echo getUserByUsername($db, $_GET["username"]);
            exit;
        } else if ($_GET["request"] == 'getUsersByClassId') {
            echo getUsersByClassId($db, $_GET["id"]);
            exit;
        } else if ($_GET["request"] == 'getHomeworkByUsername') {
            echo getHomeworkByUsername($db, $_GET["username"]);
            exit;
        } else if ($_GET["request"] == 'getHomeworkByClassId') {
            echo getHomeworkByClassId($db, $_GET["id"]);
            exit;
        } else if ($_GET["request"] == 'getHomeworkByUserId') {
            echo getHomeworkByUserId($db, $_GET["id"]);
            exit;
        } else if ($_GET["request"] == 'getHomeworkById') {
            echo getHomeworkById($db, $_GET["id"]);
            exit;
        } else if ($_GET["request"] == 'getSchools') {
            echo getSchools($db);
            exit;
        } else if ($_GET["request"] == 'getSubjects') {
            echo getSubjects($db);
            exit;
        } else if ($_GET["request"] == 'getGamesBySubjectId') {
            echo getGamesBySubjectId($db, $_GET["id"]);
            exit;
        } else if ($_GET["request"] == 'getGamesBySubjectName') {
            echo getGamesBySubjectName($db, $_GET["name"]);
            exit;
        } else if ($_GET["request"] == 'getGameByGamecode') {
            echo getGameByGamecode($db, $_GET["code"]);
            exit;
        } else if ($_GET["request"] == 'getGames') {
            echo getGames($db);
            exit;
        } else if ($_GET["request"] == 'getClassesBySchoolId') {
            echo getClassesBySchoolId($db, $_GET["id"]);
            exit;
        } else {
        
        
//SENDS ERROR MESSAGE IF API IS USED INCORRECTLY
            echo json_encode(array('error' => TRUE, 'message' => 'request did not match any get commands! GET ERROR'));
            exit;
        }
    } else if (!empty($postdata)) {
        

//---------------------------------------------------------------------------------------------
//--------------------SETTING UP ALL POST FUNCTIONS FOR API------------------------------------
//---------------------------------------------------------------------------------------------
        $request = json_decode($postdata);
        
        //INSERT API FUNCTIONS
        if (property_exists($request, 'insert')){
            $insert = $request->insert;
            if ($insert == 'student'){
                echo insertUser($db, $request->schoolid, $request->classid, $request->password, $request->name, $request->surname, $request->classyear, $request->email);
                exit;
            } else if ($insert == 'teacher') {
                echo insertTeacher($db, $request->password, $request->name, $request->surname, $request->email, $request->cellphone);
                exit;
            } else if ($insert == 'class') {
                echo insertClass($db, $request->classname, $request->grade, $request->classyear, $request->schoolid);
                exit;
            } else if ($insert == 'homework') {
                echo insertHomework($db, $request->title, $request->description, $request->gameid, $request->subject, $request->startdate, $request->enddate, $request->classid, $request->teacherid);
                exit;
        } 
        
        //DELETE API FUNCTIONS
    } else if (property_exists($request, 'delete')){
            $delete = $request->delete;
            if ($delete == 'student'){
                echo deleteUser($db, $request->id);
                exit;
            } else if ($delete == 'homework'){
                echo deleteHomework($db, $request->id);
                exit;
            } else if ($delete == 'teacher'){
                echo deleteTeacher($db, $request->id);
                exit;
            }
        
        //UPDATE API FUNCTIONS
        } else if (property_exists($request, 'update')){
            $update = $request->update;
            if ($update == 'student'){
                echo updateUser($db, $request->id, $request->classid, $request->username, $request->password, $request->name, $request->surname, $request->classyear, $request->email);
                exit;
            } else if ($update == 'homework'){
                echo updateHomework($db, $request->title, $request->description, $request->gameid, $request->subject, $request->startdate, $request->enddate, $request->classid, $request->teacherid, $request->homeworkid);
                exit;
            } else if ($update == 'teacher'){
                echo updateTeacher($db, $request->id, $request->schoolid, $request->username, $request->password, $request->name, $request->surname, $request->cellphone, $request->email);
                exit;
            }
        } else {
        
        
//SENDS ERROR MESSAGE IF API IS USED INCORRECTLY
            echo json_encode(array('error' => TRUE, 'message' => 'request did not match any post commands! POST ERROR'));
            exit;
        }
        
    } else {
        
        
//SENDS ERROR MESSAGE IF API IS USED INCORRECTLY
        echo json_encode(array('error' => TRUE, 'message' => 'no get or post request sent!'));
        exit;
    }
    
    
//GET ALL USERS BY SCHOOL - RETURNS JSON ARRAY
    function getUsersBySchoolId($conn, $schoolid) {
        $statement=$conn->prepare("select users.*, classes.classname, classes.grade
from users
join schools on users.schoolid = schools.schoolid
join classes on users.classid = classes.classid
where schools.schoolid = $schoolid");
        $statement->execute();
        $results=$statement->fetchAll(PDO::FETCH_ASSOC);
        $json=json_encode($results);
        
        return $json;
    }
    
//GET ALL SCHOOLS - RETURNS JSON ARRAY
    function getSchools($conn) {
        $statement=$conn->prepare("SELECT * FROM schools");
        $statement->execute();
        $results=$statement->fetchAll(PDO::FETCH_ASSOC);
        $json=json_encode($results);
        
        return $json;
    }
    
//GET ALL SUBJECTS - RETURNS JSON ARRAY
    function getSubjects($conn) {
        $statement=$conn->prepare("SELECT * FROM subjects");
        $statement->execute();
        $results=$statement->fetchAll(PDO::FETCH_ASSOC);
        $json=json_encode($results);
        
        return $json;
    }
    
//GET USER BY ID - RETURNS JSON ARRAY
    function getUserById($conn, $userid) {
        $statement=$conn->prepare("SELECT * FROM users WHERE users.userid = $userid");
        $statement->execute();
        $results=$statement->fetchAll(PDO::FETCH_ASSOC);
        $json=json_encode($results);
        
        return $json;
    }
    
//GET USER BY USERNAME - RETURNS JSON ARRAY
    function getUserByUsername($conn, $username) {
        $statement=$conn->prepare("SELECT * FROM users WHERE users.username = '$username'");
        $statement->execute();
        $results=$statement->fetchAll(PDO::FETCH_ASSOC);
        $json=json_encode($results);
        
        return $json;
    }
    
//GET TEACHER BY USERNAME - RETURNS JSON ARRAY
    function getTeacherByUsername($conn, $username) {
        $statement=$conn->prepare("SELECT * FROM teachers WHERE teachers.username = '$username'");
        $statement->execute();
        $results=$statement->fetchAll(PDO::FETCH_ASSOC);
        $json=json_encode($results);
        
        return $json;
    }
    
//GET USERS IN A CLASS BY CLASS ID - RETURNS JSON ARRAY
    function getUsersByClassId($conn, $classid) {
        $statement=$conn->prepare("select users.*
from users
join classes on users.classid = classes.classid
where classes.classid = $classid;");
        $statement->execute();
        $results=$statement->fetchAll(PDO::FETCH_ASSOC);
        $json=json_encode($results);
        
        return $json;
    }
    
//GET HOMEWORK BY USERNAME - RETURNS JSON ARRAY
    function getHomeworkByUsername($conn, $username) {
        $statement=$conn->prepare("select homework.*
from homework
join classes_homework on homework.homeworkid = classes_homework.homework_homeworkid
join classes on classes_homework.classes_classid = classes.classid
join users on classes.classid = users.classid
where users.username = '$username'");
        $statement->execute();
        $results=$statement->fetchAll(PDO::FETCH_ASSOC);
        $json=json_encode($results);
        
        return $json;
    }
    
//GET HOMEWORK BY USERID - RETURNS JSON ARRAY
    function getHomeworkByUserId($conn, $userid) {
        $statement=$conn->prepare("select homework.*
from homework
join classes_homework on homework.homeworkid = classes_homework.homework_homeworkid
join classes on classes_homework.classes_classid = classes.classid
join users on classes.classid = users.classid
where users.userid = $userid");
        $statement->execute();
        $results=$statement->fetchAll(PDO::FETCH_ASSOC);
        $json=json_encode($results);
        
        return $json;
    }
    
//GET HOMEWORK BY HOMEWORKID - RETURNS JSON ARRAY
    function getHomeworkById($conn, $id) {
        $statement=$conn->prepare("select homework.*,classes.classid
from homework
left join classes_homework on homework.homeworkid = classes_homework.homework_homeworkid
left join classes on classes_homework.classes_classid = classes.classid
where homework.homeworkid = $id");
        $statement->execute();
        $results=$statement->fetchAll(PDO::FETCH_ASSOC);
        $json=json_encode($results);
        
        return $json;
    }
    
//GET HOMEWORK BY CLASSID - RETURNS JSON ARRAY
    function getHomeworkByClassId($conn, $classid) {
        $statement=$conn->prepare("SELECT homework.*,WEEK(homework.startdate, 1) AS 'startweek',WEEK(homework.enddate, 1) AS 'endweek',
teachers.*,games.gamename,games.gamepath,games.gameinfo,games.gamecode,games.difficultyfrom,games.difficultyto
from homework
join classes_homework on homework.homeworkid = classes_homework.homework_homeworkid
join classes on classes_homework.classes_classid = classes.classid
left join teachers_homework on homework.homeworkid = teachers_homework.homework_homeworkid
left join teachers on teachers_homework.teachers_teacherid = teachers.teacherid
left join games on homework.gameid = games.gameid
where classes.classid = $classid and year(homework.enddate) = year(CURRENT_TIMESTAMP)");
        $statement->execute();
        $results = new stdClass();
        $homeworkArray = array();
        
        $results=$statement->fetchAll(PDO::FETCH_ASSOC);
        
        for ($i = 0; $i < count($results); $i++){
            $teacher = new stdClass();
            $game = new stdClass();
            $teacher->teacherid = $results[$i]['teacherid'];
            $teacher->schoolid = $results[$i]['schoolid'];
            $teacher->username = $results[$i]['username'];
            $teacher->name = $results[$i]['name'];
            $teacher->surname = $results[$i]['surname'];
            $teacher->email = $results[$i]['email'];
            $teacher->cellphone = $results[$i]['cellphone'];

            $game->gameid = $results[$i]['gameid'];
            $game->gamename = $results[$i]['gamename'];
            $game->gamepath = $results[$i]['gamepath'];
            $game->gameinfo = $results[$i]['gameinfo'];
            $game->gamecode = $results[$i]['gamecode'];
            $game->difficultyfrom = $results[$i]['difficultyfrom'];
            $game->difficultyto = $results[$i]['difficultyto'];


            $homework = new stdClass();
            $homework->homeworkid = $results[$i]['homeworkid'];
            $homework->title = $results[$i]['title'];
            $homework->description = $results[$i]['description'];
            $homework->subject = $results[$i]['subject'];
            $homework->startdate = $results[$i]['startdate'];
            $homework->enddate = $results[$i]['enddate'];
            $homework->startweek = $results[$i]['startweek'];
            $homework->endweek = $results[$i]['endweek'];

            $homework->teacher = $teacher;
            $homework->game = $game;

            array_push($homeworkArray, $homework);
        }
        
        
        $json=json_encode($homeworkArray);
        
        return $json;
    }
    
//GET CLASSES BY SCHOOLID - RETURNS JSON ARRAY
    function getClassesBySchoolId($conn, $schoolid) {
        $statement=$conn->prepare("select classes.*
from classes
join schools on classes.schoolid = schools.schoolid
where schools.schoolid = $schoolid");
        $statement->execute();
        $results=$statement->fetchAll(PDO::FETCH_ASSOC);
        $json=json_encode($results);
        
        return $json;
    }
    
//GET GAMES BY SUBJECTID - RETURNS JSON ARRAY
    function getGamesBySubjectId($conn, $subjectid) {
        $statement=$conn->prepare("select games.*
from games
join games_subjects on games.gameid = games_subjects.games_gameid
join subjects on games_subjects.subjects_subjectsid = subjects.subjectsid
where subjects.subjectsid = $subjectid");
        $statement->execute();
        $results=$statement->fetchAll(PDO::FETCH_ASSOC);
        $json=json_encode($results);
        
        return $json;
    }
    
//GET GAMES BY SUBJECTNAME - RETURNS JSON ARRAY
    function getGamesBySubjectName($conn, $subjectname) {
        $statement=$conn->prepare("select games.*
from games
join games_subjects on games.gameid = games_subjects.games_gameid
join subjects on games_subjects.subjects_subjectsid = subjects.subjectsid
where subjects.name = '$subjectname'");
        $statement->execute();
        $results=$statement->fetchAll(PDO::FETCH_ASSOC);
        $json=json_encode($results);
        
        return $json;
    }
    
//GET GAME BY GAMECODE - RETURNS JSON ARRAY
    function getGameByGamecode($conn, $gamecode) {
        $statement=$conn->prepare("select *
from games
where games.gamecode = $gamecode");
        $statement->execute();
        $results=$statement->fetchAll(PDO::FETCH_ASSOC);
        $json=json_encode($results);
        
        return $json;
    }
    
//GET ALL GAMES - RETURNS JSON ARRAY
    function getGames($conn) {
        $statement=$conn->prepare("select games.*, subjects.name AS subjectname
from games
join games_subjects on games.gameid = games_subjects.games_gameid
join subjects on games_subjects.subjects_subjectsid = subjects.subjectsid");
        $statement->execute();
        $results=$statement->fetchAll(PDO::FETCH_ASSOC);
        $json=json_encode($results);
        
        return $json;
    }
    
//GET HOMEWORKID BY FULLHOMEWORK - RETURNS JSON ARRAY
    function getHomeworkIdByHomework($conn, $title, $description, $gameid, $subject, $startdate, $enddate) {
        $statement=$conn->prepare("select homeworkid
from homework
where title = '$title' AND description = '$description' AND gameid = $gameid AND subject = '$subject' AND startdate = '$startdate' AND enddate = '$enddate'");
        $statement->execute();
        $results=$statement->fetchAll(PDO::FETCH_ASSOC);
        
        return $results;
    }



//
// INSERT STATEMENTS BELOW
//

// INSERT USER TO DB <- ALL FIELDS REQUIRED | RETURNS TRUE FOR SUCCESS
    function insertUser($db, $schoolid, $classid, $password, $name, $surname, $classyear, $email){
        
        $username = mb_strtolower(mb_substr(str_replace(' ', '', $surname), 0, 3, 'UTF-8') . mb_substr(str_replace(' ', '', $name), 0, 3, 'UTF-8') . mb_substr(date('Y'), 2, 4, 'UTF-8'), 'UTF-8');
        
        $numStart = 4;
        
        while (json_decode(getUserByUsername($db, $username)) != null){
            $username = mb_strtolower(mb_substr(str_replace(' ', '', $surname), 0, $numStart, 'UTF-8') . mb_substr(str_replace(' ', '', $name), 0, 3, 'UTF-8') . mb_substr(date('Y'), 2, 4, 'UTF-8'), 'UTF-8');
            $numStart++;
        }
        
        $stmt = $db->prepare("INSERT INTO users (schoolid, classid, username, password, name, surname, classyear, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $hashedPass = hash('sha256', $password);
        
        $saveuser = $username;
        
        $stmt->bindParam(1, $schoolid);
        $stmt->bindParam(2, $classid);
        $stmt->bindParam(3, $username);
        $stmt->bindParam(4, $hashedPass);
        $stmt->bindParam(5, $name);
        $stmt->bindParam(6, $surname);
        $stmt->bindParam(7, $classyear);
        $stmt->bindParam(8, $email);
        
        $report = new stdClass();
        $report->success = $stmt->execute();
        $report->username = $saveuser;
        echo json_encode($report);
    }

// UPDATE HOMEWORK <- ALL FIELDS REQUIRED | RETURNS TRUE FOR SUCCESS
    function updateHomework($db, $title, $description, $gameid, $subject, $startdate, $enddate, $classid, $teacherid, $homeworkid){
        $report = new stdClass();
        
        //UPDATE CONNECTION TO CLASS
        $stmt2 = $db->prepare("UPDATE classes_homework
        SET classes_classid = ?
        WHERE homework_homeworkid = ?;");
        $stmt2->bindParam(1, $classid);
        $stmt2->bindParam(2, $homeworkid);
        $report->success2 = $stmt2->execute();
        
        
        //UPDATE CONNECTION TO TEACHER
        $stmt3 = $db->prepare("UPDATE teachers_homework
        SET teachers_teacherid = ?
        WHERE homework_homeworkid = ?");
        $stmt3->bindParam(1, $teacherid);
        $stmt3->bindParam(2, $homeworkid);
        $report->success3 = $stmt3->execute();
        
        
        //UPDATE HOMEWORK DETAILS 
        $stmt = $db->prepare("UPDATE homework 
        SET title = ?,
        description = ?,
        gameid = ?,
        subject = ?,
        startdate = ?,
        enddate = ?
        WHERE homeworkid = ?");
        $stmt->bindParam(1, $title);
        $stmt->bindParam(2, $description);
        $stmt->bindParam(3, $gameid);
        $stmt->bindParam(4, $subject);
        $stmt->bindParam(5, $startdate);
        $stmt->bindParam(6, $enddate);
        $stmt->bindParam(7, $homeworkid);
        $report->success1 = $stmt->execute();
        
        
        if ($report->success1 && $report->success2 && $report->success3)
            $report->success = 1;
        else
            $report->success = 0;
        
        
        echo json_encode($report);
    }

// DELETE HOMEWORK FROM DB <- ALL FIELDS REQUIRED | RETURNS TRUE FOR SUCCESS
    function deleteHomework($db, $homeworkid){
        $report = new stdClass();
        
        //DELETE DEPENDENCY TO CLASS
        $stmt2 = $db->prepare("DELETE FROM classes_homework WHERE homework_homeworkid = ?;");
        $stmt2->bindParam(1, $homeworkid);
        $report->success2 = $stmt2->execute();
        
        
        //DELETE DEPENDENCY TO TEACHER
        $stmt3 = $db->prepare("DELETE FROM teachers_homework WHERE homework_homeworkid = ?");
        $stmt3->bindParam(1, $homeworkid);
        $report->success3 = $stmt3->execute();
        
        
        //DELETE HOMEWORK
        $stmt = $db->prepare("DELETE FROM homework WHERE homeworkid = ?");
        $stmt->bindParam(1, $homeworkid);
        $report->success1 = $stmt->execute();
        
        
        if ($report->success1 && $report->success2 && $report->success3)
            $report->success = 1;
        else
            $report->success = 0;
        
        
        echo json_encode($report);
    }

// INSERT HOMEWORK TO DB <- ALL FIELDS REQUIRED | RETURNS TRUE FOR SUCCESS
    function insertHomework($db, $title, $description, $gameid, $subject, $startdate, $enddate, $classid, $teacherid){
        $stmt = $db->prepare("INSERT INTO homework (title, description, gameid, subject, startdate, enddate) VALUES (?, ?, ?, ?, ?, ?)");

        $stmt->bindParam(1, $title);
        $stmt->bindParam(2, $description);
        $stmt->bindParam(3, $gameid);
        $stmt->bindParam(4, $subject);
        $stmt->bindParam(5, $startdate);
        $stmt->bindParam(6, $enddate);
        
        $report = new stdClass();
        $report->success1 = $stmt->execute();
        
        
        //PREPARE HOMEWORKID FOR CONNECTIONS
        $homeworkid = getHomeworkIdByHomework($db, $title, $description, $gameid, $subject, $startdate, $enddate);
        
        $hwid = $homeworkid[0]['homeworkid'];
        
        //CONNECT HOMEWORK TO SELECTED CLASS
        $stmt2 = $db->prepare("INSERT INTO classes_homework (homework_homeworkid, classes_classid) VALUES (?, ?);");
        $stmt2->bindParam(1, $hwid);
        $stmt2->bindParam(2, $classid);
        $report->success2 = $stmt2->execute();
        
        
        //CONNECT HOMEWORK TO TEACHER(OWNER)
        $stmt3 = $db->prepare("INSERT INTO teachers_homework (homework_homeworkid, teachers_teacherid) VALUES (?, ?);");
        $stmt3->bindParam(1, $hwid);
        $stmt3->bindParam(2, $teacherid);
        $report->success3 = $stmt3->execute();
        
        
        //CREATE SUCCESS REPORT BASED ON ALL 3 INSERTS
        if ($report->success1 && $report->success2 && $report->success3)
            $report->success = 1;
        else
            $report->success = 0;
        
        
        echo json_encode($report);
    }


// INSERT TEACHER TO DB <- ALL FIELDS REQUIRED | RETURNS TRUE FOR SUCCESS
    function insertTeacher($db, $schoolid, $password, $name, $surname, $email, $cellphone){
        $stmt = $db->prepare("INSERT INTO teachers (username, schoolid, password, name, surname, email, cellphone) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $hashedPass = hash('sha256', $password);
        $username = mb_substr($surname, 0, 3, 'UTF-8') + mb_substr($name, 0, 3, 'UTF-8') + mb_substr(date('Y'), 2, 4, 'UTF-8');
        
        $stmt->bindParam(1, $username);
        $stmt->bindParam(2, $schoolid);
        $stmt->bindParam(3, $hashedPass);
        $stmt->bindParam(4, $name);
        $stmt->bindParam(5, $surname);
        $stmt->bindParam(6, $email);
        $stmt->bindParam(7, $cellphone);
        
        $success = $stmt->execute();
        echo $success;
    }


// INSERT CLASS TO DB <- ALL FIELDS REQUIRED | RETURNS TRUE FOR SUCCESS
    function insertClass($db, $classname, $grade, $classyear, $schoolid){
        $stmt = $db->prepare("INSERT INTO classes (classname, grade, classyear, schoolid) VALUES (?, ?, ?, ?)");
        
        $stmt->bindParam(1, $classname);
        $stmt->bindParam(2, $grade);
        $stmt->bindParam(3, $classyear);
        $stmt->bindParam(4, $schoolid);
        
        $report->success = $stmt->execute();
        $report->username = $username;
        echo json_encode($report);
    }





// DELETE STATEMENTS
function deleteUser($db, $userid){
    $stmt = $db->prepare("DELETE FROM `ingmag13`.`users`
WHERE users.userid = ?;");

    $stmt->bindParam(1, $userid);

    $success = $stmt->execute();
    echo $success;
}

function deleteTeacher($db, $teacherid){
    $stmt = $db->prepare("DELETE FROM `ingmag13`.`teachers`
WHERE teachers.teacherid = ?;");

    $stmt->bindParam(1, $teacherid);

    $success = $stmt->execute();
    echo $success;
}






//UPDATE STATEMENTS
function updateUser($db, $userid, $classid, $username, $password, $name, $surname, $classyear, $email){
    if ($password == ""){
        $stmt = $db->prepare("UPDATE `ingmag13`.`users`
    SET
    `classid` = ?,
    `username` = ?,
    `name` = ?,
    `surname` = ?,
    `classyear` = ?,
    `email` = ?
    WHERE `userid` = ?;");

        $stmt->bindParam(1, $classid);
        $stmt->bindParam(2, $username);
        $stmt->bindParam(3, $name);
        $stmt->bindParam(4, $surname);
        $stmt->bindParam(5, $classyear);
        $stmt->bindParam(6, $email);
        $stmt->bindParam(7, $userid);
    } else {
        $stmt = $db->prepare("UPDATE `ingmag13`.`users`
    SET
    `classid` = ?,
    `username` = ?,
    `password` = ?,
    `name` = ?,
    `surname` = ?,
    `classyear` = ?,
    `email` = ?
    WHERE `userid` = ?;");
        
        $hashedPass = hash('sha256', $password);

        $stmt->bindParam(1, $classid);
        $stmt->bindParam(2, $username);
        $stmt->bindParam(3, $hashedPass);
        $stmt->bindParam(4, $name);
        $stmt->bindParam(5, $surname);
        $stmt->bindParam(6, $classyear);
        $stmt->bindParam(7, $email);
        $stmt->bindParam(8, $userid);
    }
    
    
    $success = $stmt->execute();
    echo $success;
}

function updateTeacher($db, $teacherid, $schoolid, $username, $password, $name, $surname, $cellphone, $email){
    if ($password == ""){
        $stmt = $db->prepare("UPDATE `ingmag13`.`teachers`
    SET
    `schoolid` = ?,
    `username` = ?,
    `name` = ?,
    `surname` = ?,
    `cellphone` = ?,
    `email` = ?
    WHERE `teacherid` = ?;");

        $stmt->bindParam(1, $schoolid);
        $stmt->bindParam(2, $username);
        $stmt->bindParam(3, $name);
        $stmt->bindParam(4, $surname);
        $stmt->bindParam(5, $cellphone);
        $stmt->bindParam(6, $email);
        $stmt->bindParam(7, $teacherid);
    } else {
        $stmt = $db->prepare("UPDATE `ingmag13`.`teachers`
    SET
    `schoolid` = ?,
    `username` = ?,
    `password` = ?,
    `name` = ?,
    `surname` = ?,
    `cellphone` = ?,
    `email` = ?
    WHERE `teacherid` = ?;");
        
        $hashedPass = hash('sha256', $password);

        $stmt->bindParam(1, $schoolid);
        $stmt->bindParam(2, $username);
        $stmt->bindParam(3, $hashedPass);
        $stmt->bindParam(4, $name);
        $stmt->bindParam(5, $surname);
        $stmt->bindParam(6, $cellphone);
        $stmt->bindParam(7, $email);
        $stmt->bindParam(8, $teacherid);
    }
    
    
    $success = $stmt->execute();
    echo $success;
}


?>