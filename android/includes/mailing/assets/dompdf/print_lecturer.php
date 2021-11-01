
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

if(isset($_POST['assignment_id']) && isset($_POST['course_code'])){
    $today = date("dhs");
    
    $assignment_id = $_POST['assignment_id'];
    $course_code = $_POST['course_code'];
    
    $resultAssignment = $operation->retrieveSingle("SELECT * FROM `lecturer_assignments_add` WHERE assignment_id = '$assignment_id'");
    
    $assignments = $operation->retrieveMany("SELECT * FROM `lecturer_assignments_add` INNER JOIN student_assignment_scripts ON student_assignment_scripts.assignment_id = lecturer_assignments_add.assignment_id WHERE lecturer_assignments_add.course_code = '$course_code' AND lecturer_assignments_add.assignment_id = '$assignment_id' ");
  
    $filename = $course_code."_".$today.".pdf";
    
    
    $nn = '
        <h3 class="text-center alert-primary">SUBMITTED </h3>
        <h4>Assignment Name:'.$resultAssignment['assignment_title'].'  </h4>
        <table class="table table-condensed table-borderless">
            <tr style="background-color:black; color:#fff">
                <th>Student Name</th>
                <th>Reg#</th>
            </tr>
            <tbody>
    ';
    foreach($assignments as $row){
        $reg_no = $row['reg_no'];
        
        $getID = $operation->retrieveSingle("SELECT *FROM student_info WHERE reg_no = '$reg_no'");
        $user_id = $getID['user_id'];
        
        $name = $operation->retrieveSingle("SELECT *FROM users WHERE user_id = '$user_id'");
        
        $body ='
            <tr>
                <td>'.$name['full_name'].'</td>
                <td>'.$reg_no.'</td>
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