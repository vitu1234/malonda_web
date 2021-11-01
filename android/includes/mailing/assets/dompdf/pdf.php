
<?php
   // Include autoloader 
require_once 'autoload.inc.php'; 
include("../applicant/operations/Functions.php");
include("../applicant/img/logo.php");
$operation = new Functions();

$html = '';

//echo $_POST['assignment_id'];
$today = "";

if(isset($_POST['assignment_id'])){
    $today = date("dhs");
    
    $assignment_id = $_POST['assignment_id'];
    
    $resultAssignment = $operation->retrieveSingle("SELECT * FROM `lecturer_assignments_add` WHERE assignment_id = '$assignment_id'");
    
    $dateline = date_create($resultAssignment["dateline"]);
     $date_added = date_create($resultAssignment["date_added"]);

     $date = date_format($dateline,"D, d M Y");
     $date2 = date_format($date_added,"D, d M Y");
    $course = $resultAssignment['course_code'];
    $filename = $course."_".$today.".pdf";
    
    $html = '
        <table style="border=0px;">
        
           <tr>
                <th align="left" style="padding: 10px;">Assignment Title:</th>
                <td colspan="50%" style="padding: 10px; ">'.$resultAssignment['assignment_title'].'</td>
           </tr>
           <tr>
                <th align="left" style="padding: 10px;">Assignment Type:</th>
                <td colspan="50%" style="padding: 10px; ">'.$resultAssignment['assignment_type'].'</td>
           </tr>
           <tr>
                <th align="left" style="padding: 10px;">Dateline:</th>
                <td colspan="50%" style="padding: 10px; ">'.$date.'</td>
           </tr>
           <tr>
                <th align="left" style="padding: 10px;">Date Uploaded:</th>
                <td colspan="50%" style="padding: 10px; ">'.$date2.'</td>
           </tr>
           
        </table>
        <br/>
        <hr/>
        '.$resultAssignment['assignment_content'].'
    ';
    
}elseif(isset($_POST['depart_print'])){
     $today = date("dhs");
    $filename = "grades.pdf";
    
    $records = $_POST['depart_print'];
    
    if($records == "all_depart"){
        
        $numbers = array();
        
        $regN = $operation->retrieveMany("SELECT DISTINCT(reg_no),reg_no FROM school_grade WHERE grade<50");
        
        foreach($regN as $row){
            $no = $row['reg_no'];
            
            array_push($numbers,$no);
        }
//        print_r($numbers);die();
        
        $htm = '
        <!DOCTYPE html>
		<html>
		<head>
			<!-- Bootstrap CSS-->
        <link href="../applicant/css/bootstrap.min.css" rel="stylesheet" media="all">


        <link href="../applicant/css/style.css" rel="stylesheet" media="all">
		</head>

		<body>
        
                    <div class="table-responsive ">
                        <table class="table table-borderless">
                            <td class="text-center" colspan="2" style="padding:10px;">
                                <span><img style="height:100px" src="'.$logo.'"  /></span>
                                <br>
                                <br>
                                
                            <td>
                        </table>
                        
                    </div>
               
			<header>
				<p class="text-center h4">STUDENTS PASSLIST</p>
			</header>
			<section >
            <div class="table-responsive">
                <table class="table table-stripped table-condensed " >
                    <thead style="background-color:black;color:#fff;">
                        <tr>
                            <th>Student Name</th>
                            <th>RegNo</th>
                        </tr>
                    </thead>
                    <tbody>
                
        ';
        $closeHtml = '
            </section>

            </body>
            </html>';
      
//        print_r($numbers);die();
        $nn ="";
      
        $countAllGrades = $operation->countAll("SELECT DISTINCT(reg_no) FROM `school_grade` ");
        
        if($countAllGrades == count($numbers)){
            $nn = "<tr>No Records found</tr>";
        }else{
               if(count($numbers) == 0){
                     $sql = "SELECT DISTINCT(school_grade.reg_no),users.full_name FROM `student_info` INNER JOIN users ON student_info.user_id = users.user_id INNER JOIN school_grade ON student_info.reg_no = school_grade.reg_no ";
            $rr = $operation->retrieveMany($sql);
               foreach($rr as $result){

    //              echo $sql.$where ."<br/>";
                  


                   $tbody = '
                    <tr>
                        <td>'.$result['full_name'].'</td>
                        <td>'.$result['reg_no'].'</td>
                    </tr>
                    ';
                  $nn.=$tbody;
              }
             
        }else{
             $sql = "SELECT DISTINCT(school_grade.reg_no),users.full_name FROM `school_grade` INNER JOIN student_info ON school_grade.reg_no = student_info.reg_no INNER JOIN users ON student_info.user_id = users.user_id
            WHERE school_grade.reg_no !=  ";
            
               for($i = 0;$i<count($numbers);$i++){
                  $rr = $numbers[$i];
                  $where = "'$rr'";
                   
    //              echo $sql.$where ."<br/>";
                  $result = $operation->retrieveSingle($sql.$where);

                   
                   $tbody = '
                    <tr>
                        <td>'.$result['full_name'].'</td>
                        <td>'.$result['reg_no'].'</td>
                    </tr>
                    ';
                  $nn.=$tbody;
              }
            
            
            
        
        }
        }
        
     
       
        
        $tfooter = "</tbody></table></div>";
        $html=$htm.$nn.$tfooter.$closeHtml;

        
    }else{
        $depart_id = $_POST['depart_print'];
        
        
                $numbers = array();
        
        $regN = $operation->retrieveMany("SELECT DISTINCT(reg_no),reg_no FROM school_grade WHERE grade<50 AND depart_id='$depart_id'");
        
        foreach($regN as $row){
            $no = $row['reg_no'];
            
            array_push($numbers,$no);
        }
//        print_r($numbers);die();
        
        $htm = '
        <!DOCTYPE html>
		<html>
		<head>
			<!-- Bootstrap CSS-->
        <link href="../applicant/css/bootstrap.min.css" rel="stylesheet" media="all">


        <link href="../applicant/css/style.css" rel="stylesheet" media="all">
		</head>

		<body>
        
                    <div class="table-responsive ">
                        <table class="table table-borderless">
                            <td class="text-center" colspan="2" style="padding:10px;">
                                <span><img style="height:100px" src="'.$logo.'"  /></span>
                                <br>
                                <br>
                                
                            <td>
                        </table>
                        
                    </div>
               
			<header>
				<p class="text-center h4">STUDENTS PASSLIST</p>
			</header>
			<section >
            <div class="table-responsive">
                <table class="table table-stripped table-condensed " >
                    <thead style="background-color:black;color:#fff;">
                        <tr>
                            <th>Student Name</th>
                            <th>RegNo</th>
                        </tr>
                    </thead>
                    <tbody>
                
        ';
        $closeHtml = '
            </section>

            </body>
            </html>';
      
//        print_r($numbers);die();
        $nn ="";
      
        $countAllGrades = $operation->countAll("SELECT DISTINCT(reg_no) FROM `school_grade` WHERE depart_id = '$depart_id'");
        
        if($countAllGrades == count($numbers)){
            $nn = "<tr>No Records found</tr>";
        }else{
               if(count($numbers) == 0){
                     $sql = "SELECT DISTINCT(school_grade.reg_no),users.full_name FROM `student_info` INNER JOIN users ON student_info.user_id = users.user_id INNER JOIN school_grade ON student_info.reg_no = school_grade.reg_no WHERE school_grade.depart_id = '$depart_id'";
            $rr = $operation->retrieveMany($sql);
               foreach($rr as $result){

    //              echo $sql.$where ."<br/>";
                  


                   $tbody = '
                    <tr>
                        <td>'.$result['full_name'].'</td>
                        <td>'.$result['reg_no'].'</td>
                    </tr>
                    ';
                  $nn.=$tbody;
              }
             
        }else{
             $sql = "SELECT DISTINCT(school_grade.reg_no),users.full_name FROM `school_grade` INNER JOIN student_info ON school_grade.reg_no = student_info.reg_no INNER JOIN users ON student_info.user_id = users.user_id
            WHERE school_grade.depart_id = '$depart_id' AND school_grade.reg_no !=  ";
            
               for($i = 0;$i<count($numbers);$i++){
                  $rr = $numbers[$i];
                  $where = "'$rr'";
                   
    //              echo $sql.$where ."<br/>";
                  $result = $operation->retrieveSingle($sql.$where);

                   
                   $tbody = '
                    <tr>
                        <td>'.$result['full_name'].'</td>
                        <td>'.$result['reg_no'].'</td>
                    </tr>
                    ';
                  $nn.=$tbody;
              }
            
            
            
        
        }
        }
        
     
       
        
        $tfooter = "</tbody></table></div>";
        $html=$htm.$nn.$tfooter.$closeHtml;
        

        
    }
    
//    $html = "<h3>Printed</h3>";
}elseif(isset($_POST['depart_print_supplimentary'])){
    
     $today = date("dhs");
    $filename = "Supplimentary grades.pdf";
    
    $records = $_POST['depart_print_supplimentary'];
    
    if($records == "all_depart"){
        
        $query = "SELECT DISTINCT(school_grade.reg_no),semester_number,academic_year_id,user_id FROM `school_grade` INNER JOIN student_info ON school_grade.reg_no = student_info.reg_no WHERE grade BETWEEN 40 AND 49 AND grade_status = 0";
        
        $results = $operation->retrieveMany($query);
        $htm = '
        <!DOCTYPE html>
		<html>
		<head>
			<!-- Bootstrap CSS-->
        <link href="../applicant/css/bootstrap.min.css" rel="stylesheet" media="all">


        <link href="../applicant/css/style.css" rel="stylesheet" media="all">
		</head>

		<body>
        
                    <div class="table-responsive ">
                        <table class="table table-borderless">
                            <td class="text-center" colspan="2" style="padding:10px;">
                                <span><img style="height:100px" src="'.$logo.'"  /></span>
                                <br>
                                <br>
                                
                            <td>
                        </table>
                        
                    </div>
               
			<header>
				<p class="text-center h4">STUDENTS SUPPLIMENTARIES</p>
			</header>
			<section >
            <div class="table-responsive">
                <table class="table table-stripped table-condensed " >
                    <thead style="background-color:black;color:#fff;">
                        <tr>
                            <th>Student Name</th>
                            <th>RegNo</th>
                            <th>Academic Year</th>
                            <th>Semester</th>
                        </tr>
                    </thead>
                    <tbody>
                
        ';
        $closeHtml = '
            </section>

            </body>
            </html>';
        
        $nn = "";
         foreach($results as $row){
            $aca_id = $row['academic_year_id'];
            $user_id = $row['user_id'];
             
            $user= $operation->retrieveSingle("SELECT * FROM `users` WHERE user_id = '$user_id'");
            $aca= $operation->retrieveSingle("SELECT * FROM `school_academic_year` WHERE academic_year_id = '$aca_id'");
            $tbody = '
                <tr>
                    <td>'.$user['full_name'].'</td>
                    <td>'.$row['reg_no'].'</td>
                    <td>'.$aca['academic_year'].'</td>
                    <td>'.$row['semester_number'].'</td>
                </tr>
            ';
             $nn.=$tbody;
        }

        
        $tfooter = "</tbody></table></div>";
        $html=$htm.$nn.$tfooter.$closeHtml;

//        echo $html;die();
    }else{
        
        $query = "SELECT DISTINCT(school_grade.reg_no),semester_number,academic_year_id,user_id FROM `school_grade` INNER JOIN student_info ON school_grade.reg_no = student_info.reg_no WHERE grade BETWEEN 40 AND 49 AND grade_status = 0 AND school_grade.depart_id='$records'";
        
        $countRc = $operation->countAll($query);
        
        if($countRc == 0){
            $html = "No records for the selected department";
        }else{
            $results = $operation->retrieveMany($query);
        $htm = '
        <!DOCTYPE html>
		<html>
		<head>
			<!-- Bootstrap CSS-->
        <link href="../applicant/css/bootstrap.min.css" rel="stylesheet" media="all">


        <link href="../applicant/css/style.css" rel="stylesheet" media="all">
		</head>

		<body>
        
                    <div class="table-responsive ">
                        <table class="table table-borderless">
                            <td class="text-center" colspan="2" style="padding:10px;">
                                <span><img style="height:100px" src="'.$logo.'"  /></span>
                                <br>
                                <br>
                                
                            <td>
                        </table>
                        
                    </div>
               
			<header>
				<p class="text-center h4">STUDENTS SUPPLIMENTARIES</p>
			</header>
			<section >
            <div class="table-responsive">
                <table class="table table-stripped table-condensed " >
                    <thead style="background-color:black;color:#fff;">
                        <tr>
                            <th>Student Name</th>
                            <th>RegNo</th>
                            <th>Academic Year</th>
                            <th>Semester</th>
                        </tr>
                    </thead>
                    <tbody>
                
        ';
        $closeHtml = '
            </section>

            </body>
            </html>';
        
        $nn = "";
         foreach($results as $row){
            $aca_id = $row['academic_year_id'];
            $user_id = $row['user_id'];
             
            $user= $operation->retrieveSingle("SELECT * FROM `users` WHERE user_id = '$user_id'");
            $aca= $operation->retrieveSingle("SELECT * FROM `school_academic_year` WHERE academic_year_id = '$aca_id'");
            $tbody = '
                <tr>
                    <td>'.$user['full_name'].'</td>
                    <td>'.$row['reg_no'].'</td>
                    <td>'.$aca['academic_year'].'</td>
                    <td>'.$row['semester_number'].'</td>
                </tr>
            ';
             $nn.=$tbody;
        }

        
        $tfooter = "</tbody></table></div>";
        $html=$htm.$nn.$tfooter.$closeHtml;
        }
        
        
    }
    
//    $html = "<h3>Printed</h3>";
    
    
    
}elseif(isset($_POST['depart_print_course_rpt'])){
        $today = date("dhs");
    $filename = "repeat courses grades.pdf";
    
    $records = $_POST['depart_print_course_rpt'];
    
    if($records == "all_depart"){
        
        $query = "SELECT DISTINCT(school_repeat_courses.reg_no),semester_number,academic_year_id,user_id FROM `school_repeat_courses` INNER JOIN student_info ON school_repeat_courses.reg_no = student_info.reg_no WHERE repeat_status = 0";
        
        $results = $operation->retrieveMany($query);
        $htm = '
        <!DOCTYPE html>
		<html>
		<head>
			<!-- Bootstrap CSS-->
        <link href="../applicant/css/bootstrap.min.css" rel="stylesheet" media="all">


        <link href="../applicant/css/style.css" rel="stylesheet" media="all">
		</head>

		<body>
        
                    <div class="table-responsive ">
                        <table class="table table-borderless">
                            <td class="text-center" colspan="2" style="padding:10px;">
                                <span><img style="height:100px" src="'.$logo.'"  /></span>
                                <br>
                                <br>
                                
                            <td>
                        </table>
                        
                    </div>
               
			<header>
				<p class="text-center h4">STUDENTS WITH COURSE REPEATS</p>
			</header>
			<section >
            <div class="table-responsive">
                <table class="table table-stripped table-condensed " >
                    <thead style="background-color:black;color:#fff;">
                        <tr>
                            <th>Student Name</th>
                            <th>RegNo</th>
                            <th>Academic Year</th>
                            <th>Semester</th>
                        </tr>
                    </thead>
                    <tbody>
                
        ';
        $closeHtml = '
            </section>

            </body>
            </html>';
        
        $nn = "";
         foreach($results as $row){
            $uid = $row['user_id'];
            $yr_id = $row['academic_year_id'];
            $name = $operation->retrieveSingle("SELECT * FROM `users` WHERE user_id = '$uid'");
            $yr = $operation->retrieveSingle("SELECT * FROM `school_academic_year` WHERE academic_year_id = '$yr_id'");
             
            $tbody = '
                <tr>
                    <td>'.$name['full_name'].'</td>
                    <td>'.$row['reg_no'].'</td>
                    
                    <td>'.$yr['academic_year'].'</td>
                    <td>'.$row['semester_number'].'</td>
                   
                    
                </tr>
            ';

             $nn.=$tbody;
        }

        
        $tfooter = "</tbody></table></div>";
        $html=$htm.$nn.$tfooter.$closeHtml;

//        echo $html;die();
    }else{
        
        $query = "SELECT DISTINCT(school_repeat_courses.reg_no),semester_number,academic_year_id,user_id FROM `school_repeat_courses` INNER JOIN student_info ON school_repeat_courses.reg_no = student_info.reg_no WHERE repeat_status = 0 AND student_info.depart_id='$records'";
        
        $countRc = $operation->countAll($query);
        
        if($countRc == 0){
            $html = "No records for the selected department";
        }else{
            $results = $operation->retrieveMany($query);
        $htm = '
        <!DOCTYPE html>
		<html>
		<head>
			<!-- Bootstrap CSS-->
        <link href="../applicant/css/bootstrap.min.css" rel="stylesheet" media="all">


        <link href="../applicant/css/style.css" rel="stylesheet" media="all">
		</head>

		<body>
        
                    <div class="table-responsive ">
                        <table class="table table-borderless">
                            <td class="text-center" colspan="2" style="padding:10px;">
                                <span><img style="height:100px" src="'.$logo.'"  /></span>
                                <br>
                                <br>
                                
                            <td>
                        </table>
                        
                    </div>
               
			<header>
				<p class="text-center h4">STUDENTS WITH COURSE REPEATS</p>
			</header>
			<section >
            <div class="table-responsive">
                <table class="table table-stripped table-condensed " >
                    <thead style="background-color:black;color:#fff;">
                        <tr>
                            <th>Student Name</th>
                            <th>RegNo</th>
                            <th>Academic Year</th>
                            <th>Semester</th>
                        </tr>
                    </thead>
                    <tbody>
                
        ';
        $closeHtml = '
            </section>

            </body>
            </html>';
        
        $nn = "";
         foreach($results as $row){
             
            $uid = $row['user_id'];
            $yr_id = $row['academic_year_id'];
            $name = $operation->retrieveSingle("SELECT * FROM `users` WHERE user_id = '$uid'");
            $yr = $operation->retrieveSingle("SELECT * FROM `school_academic_year` WHERE academic_year_id = '$yr_id'");
             
            $tbody = '
                <tr>
                    <td>'.$name['full_name'].'</td>
                    <td>'.$row['reg_no'].'</td>
                    
                    <td>'.$yr['academic_year'].'</td>
                    <td>'.$row['semester_number'].'</td>
                   
                    
                </tr>
            ';
             
//            $aca_id = $row['academic_year_id'];
//            $user_id = $row['user_id'];
//             
//            $user= $operation->retrieveSingle("SELECT * FROM `users` WHERE user_id = '$user_id'");
//            $aca= $operation->retrieveSingle("SELECT * FROM `school_academic_year` WHERE academic_year_id = '$aca_id'");
//            $tbody = '
//                <tr>
//                    <td>'.$user['full_name'].'</td>
//                    <td>'.$row['reg_no'].'</td>
//                    <td>'.$aca['academic_year'].'</td>
//                    <td>'.$row['semester_number'].'</td>
//                </tr>
//            ';
             $nn.=$tbody;
        }

        
        $tfooter = "</tbody></table></div>";
        $html=$htm.$nn.$tfooter.$closeHtml;
        }
        
        
    }
}elseif(isset($_POST['depart_print_semeRpt'])){
        $today = date("dhs");
    $filename = "repeat semester.pdf";
    
    $records = $_POST['depart_print_semeRpt'];
    
    if($records == "all_depart"){
        
        $query = "SELECT DISTINCT(school_repeat_semester.reg_no),semester_number,academic_year_id,user_id FROM `school_repeat_semester` INNER JOIN student_info ON school_repeat_semester.reg_no = student_info.reg_no";
        
        $results = $operation->retrieveMany($query);
        $htm = '
        <!DOCTYPE html>
		<html>
		<head>
			<!-- Bootstrap CSS-->
        <link href="../applicant/css/bootstrap.min.css" rel="stylesheet" media="all">


        <link href="../applicant/css/style.css" rel="stylesheet" media="all">
		</head>

		<body>
        
                    <div class="table-responsive ">
                        <table class="table table-borderless">
                            <td class="text-center" colspan="2" style="padding:10px;">
                                <span><img style="height:100px" src="'.$logo.'"  /></span>
                                <br>
                                <br>
                                
                            <td>
                        </table>
                        
                    </div>
               
			<header>
				<p class="text-center h4">STUDENTS REPEATING SEMESTER</p>
			</header>
			<section >
            <div class="table-responsive">
                <table class="table table-stripped table-condensed " >
                    <thead style="background-color:black;color:#fff;">
                        <tr>
                            <th>Student Name</th>
                            <th>RegNo</th>
                            <th>Academic Year</th>
                            <th>Semester</th>
                        </tr>
                    </thead>
                    <tbody>
                
        ';
        $closeHtml = '
            </section>

            </body>
            </html>';
        
        $nn = "";
         foreach($results as $row){
            $uid = $row['user_id'];
            $yr_id = $row['academic_year_id'];
            $name = $operation->retrieveSingle("SELECT * FROM `users` WHERE user_id = '$uid'");
            $yr = $operation->retrieveSingle("SELECT * FROM `school_academic_year` WHERE academic_year_id = '$yr_id'");
             
            $tbody = '
                <tr>
                    <td>'.$name['full_name'].'</td>
                    <td>'.$row['reg_no'].'</td>
                    
                    <td>'.$yr['academic_year'].'</td>
                    <td>'.$row['semester_number'].'</td>
                   
                    
                </tr>
            ';

             $nn.=$tbody;
        }

        
        $tfooter = "</tbody></table></div>";
        $html=$htm.$nn.$tfooter.$closeHtml;

//        echo $html;die();
    }else{
        
       $query = "SELECT DISTINCT(school_repeat_semester.reg_no),semester_number,academic_year_id,user_id FROM `school_repeat_semester` INNER JOIN student_info ON school_repeat_semester.reg_no = student_info.reg_no AND student_info.depart_id='$records'";
        
        $countRc = $operation->countAll($query);
        
        if($countRc == 0){
            $html = "No records for the selected department";
        }else{
            $results = $operation->retrieveMany($query);
        $htm = '
        <!DOCTYPE html>
		<html>
		<head>
			<!-- Bootstrap CSS-->
        <link href="../applicant/css/bootstrap.min.css" rel="stylesheet" media="all">


        <link href="../applicant/css/style.css" rel="stylesheet" media="all">
		</head>

		<body>
        
                    <div class="table-responsive ">
                        <table class="table table-borderless">
                            <td class="text-center" colspan="2" style="padding:10px;">
                                <span><img style="height:100px" src="'.$logo.'"  /></span>
                                <br>
                                <br>
                                
                            <td>
                        </table>
                        
                    </div>
               
			<header>
				<p class="text-center h4">STUDENTS WITH COURSE REPEATS</p>
			</header>
			<section >
            <div class="table-responsive">
                <table class="table table-stripped table-condensed " >
                    <thead style="background-color:black;color:#fff;">
                        <tr>
                            <th>Student Name</th>
                            <th>RegNo</th>
                            <th>Academic Year</th>
                            <th>Semester</th>
                        </tr>
                    </thead>
                    <tbody>
                
        ';
        $closeHtml = '
            </section>

            </body>
            </html>';
        
        $nn = "";
         foreach($results as $row){
             
            $uid = $row['user_id'];
            $yr_id = $row['academic_year_id'];
            $name = $operation->retrieveSingle("SELECT * FROM `users` WHERE user_id = '$uid'");
            $yr = $operation->retrieveSingle("SELECT * FROM `school_academic_year` WHERE academic_year_id = '$yr_id'");
             
            $tbody = '
                <tr>
                    <td>'.$name['full_name'].'</td>
                    <td>'.$row['reg_no'].'</td>
                    
                    <td>'.$yr['academic_year'].'</td>
                    <td>'.$row['semester_number'].'</td>
                   
                    
                </tr>
            ';
             
//            $aca_id = $row['academic_year_id'];
//            $user_id = $row['user_id'];
//             
//            $user= $operation->retrieveSingle("SELECT * FROM `users` WHERE user_id = '$user_id'");
//            $aca= $operation->retrieveSingle("SELECT * FROM `school_academic_year` WHERE academic_year_id = '$aca_id'");
//            $tbody = '
//                <tr>
//                    <td>'.$user['full_name'].'</td>
//                    <td>'.$row['reg_no'].'</td>
//                    <td>'.$aca['academic_year'].'</td>
//                    <td>'.$row['semester_number'].'</td>
//                </tr>
//            ';
             $nn.=$tbody;
        }

        
        $tfooter = "</tbody></table></div>";
        $html=$htm.$nn.$tfooter.$closeHtml;
        }
        
        
    }
}elseif($_POST['all_falcuties']){
    
         $today = date("dhs");
    $filename = "All Falcuties.pdf";
        $query = "SELECT *FROM school_department ORDER BY depart_name ASC ";
    
    
        $results = $operation->retrieveMany($query);
        $htm = '
        <!DOCTYPE html>
		<html>
		<head>
			<!-- Bootstrap CSS-->
        <link href="../applicant/css/bootstrap.min.css" rel="stylesheet" media="all">


        <link href="../applicant/css/style.css" rel="stylesheet" media="all">
		</head>

		<body>
        
                    <div class="table-responsive ">
                        <table class="table table-borderless">
                            <td class="text-center" colspan="2" style="padding:10px;">
                                <span><img style="height:100px" src="'.$logo.'"  /></span>
                                <br>
                                <br>
                                
                            <td>
                        </table>
                        
                    </div>
               
			<header>
				<p class="text-center h4">ALL DEPARTMENTS</p>
			</header>
			<section >
            <div class="table-responsive">
                <table class="table table-stripped table-condensed " >
                    <thead style="background-color:black;color:#fff;">
                        <tr>
                            <th>Department</th>
                            <th>Number of Years</th>
                        </tr>
                    </thead>
                    <tbody>
                
        ';
        $closeHtml = '
            </section>

            </body>
            </html>';
        
        $nn = "";
         foreach($results as $row){
            
            $tbody = '
                <tr>
                    <td>'.$row['depart_name'].'</td>
                    <td>'.$row['number_of_years'].'</td>
                    
                </tr>
            ';

             $nn.=$tbody;
        }

        
        $tfooter = "</tbody></table></div>";
        $html=$htm.$nn.$tfooter.$closeHtml;

        
}


//
//
//echo $html;
//die();







 
 
// Reference the Dompdf namespace 
use Dompdf\Dompdf; 
 
// Instantiate and use the dompdf class 
$dompdf = new Dompdf(array('enable_remote' => true));

// Load HTML content 
$dompdf->loadHtml($html); 
 
// (Optional) Setup the paper size and orientation 
$dompdf->setPaper('A4', 'portrait'); 
 
// Render the HTML as PDF 
$dompdf->render(); 
 
// Output the generated PDF to Browser 
$dompdf->stream($filename,array("Attachment" => true));
?>