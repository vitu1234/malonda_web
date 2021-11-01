
<?php
   // Include autoloader 
require_once 'autoload.inc.php'; 
include("../applicant/operations/Functions.php");
include("../applicant/img/logo.php");
$operation = new Functions();

$html = '';

//echo $_POST['assignment_id'];
$today = "";


$htm = '
<!DOCTYPE html>
<html>
<head>
    <!-- Bootstrap CSS-->
<link href="../applicant/css/bootstrap.min.css" rel="stylesheet" media="all">


<link href="../applicant/css/style.css" rel="stylesheet" media="all">
</head>
    <div class="table-responsive ">
                        <table class="table table-borderless">
                            <td class="text-center" colspan="2" style="padding:10px;">
                                <span><img style="height:100px" src="'.$logo.'"  /></span>
                                <br>
                                <br>
                                
                            <td>
                        </table>
                        
                    </div>
';

$closeHtml = '

    </body>
    </html>';

$nn ="";

if(isset($_POST['lecturer_id']) && isset($_POST['course_code'])){
    $today = date("dhs");
    
    $lecturer_id = $_POST['lecturer_id'];
    $course_code = $_POST['course_code'];
    
    $results = $operation->retrieveMany("SELECT * FROM `school_dean_lecturer_grades` WHERE lecturer_id ='$lecturer_id' AND course_code =  '$course_code'");
    $course_name = $operation->retrieveSingle("SELECT *FROM school_courses WHERE course_code = '$course_code'");
    
    $filename = $course_code."_".$today.".pdf";
    
    
    $nn = '
        <h3 class="text-center alert-primary"></h3>
        <h4>FINAL GRADES | Course Code: '.$course_code.' | Course Name: '.$course_name['course_name'].' </h4>
        <table class="table table-condensed table-borderless">
            <tr style="background-color:black; color:#fff">
                <th>Reg#</th>
                <th>Student Name</th>
                <th>Grade</th>
                
            </tr>
            <tbody>
    ';
    foreach($results as $row){
        $reg_no = $row['reg_no'];
        
        $getID = $operation->retrieveSingle("SELECT *FROM student_info WHERE reg_no = '$reg_no'");
        $user_id = $getID['user_id'];
        
        $name = $operation->retrieveSingle("SELECT *FROM users WHERE user_id = '$user_id'");
        
        $body ='
            <tr>
                <td>'.$reg_no.'</td>
                <td>'.$name['full_name'].'</td>
                <td>'.$row['grade'].'</td>
            </tr>
        ';
        
        $nn.=$body;
    }
    
    
}
        
        
     
       
        
$tfooter = "</tbody></table></div>";
$html=$htm.$nn.$tfooter.$closeHtml;
        

        
    
    


//
//
//echo $html;
//die();







 
 
// Reference the Dompdf namespace 
use Dompdf\Dompdf; 
 
// Instantiate and use the dompdf class 
$dompdf = new Dompdf();

// Load HTML content 
$dompdf->loadHtml($html); 
 
// (Optional) Setup the paper size and orientation 
$dompdf->setPaper('A4', 'portrait'); 
 
// Render the HTML as PDF 
$dompdf->render(); 
 
// Output the generated PDF to Browser 
$dompdf->stream($filename,array("Attachment" => true));
?>