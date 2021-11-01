<?php 

   require '../includes/Functions.php';
    $operation = new Functions();

      function cleanSpecialCharacters($string) {
            $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
            return preg_replace('/[^A-Za-z0-9.\-]/', "", $string);  // Removes special chars.
        }

$response = array();
$success = "";
$message ="";



if(isset($_POST['reg_no']) && isset($_POST['assignment_id'])  && !empty($_POST['reg_no']) && !empty($_POST['assignment_id'])){
  $reg_no = addslashes($_POST['reg_no']);
  $assignment_id = addslashes($_POST['assignment_id']);
  $assignment_comment = addslashes($_POST['assignment_comment']);
  $date = date("hs");

  
  $rand = rand(1,100);
  $streplaceFileName = cleanSpecialCharacters($_FILES['file']['name']);
  $upload_file = $rand."-".$streplaceFileName;
  $ds = DIRECTORY_SEPARATOR;
  $storeFolder = 'uploads/';
  if((!empty($_FILES)) && !empty($_FILES['file']['name'])) {

    if(($_FILES["file"]["size"] < 110000000)){
        if(preg_match('/[.](zip)$/', strtolower($_FILES['file']['name'])) || preg_match('/[.](pdf)$/', strtolower($_FILES['file']['name']))) {

            $filename = $rand . "-" . $streplaceFileName;
            $tempFile = $_FILES['file']['tmp_name'];
            $targetPath = $storeFolder . $ds;
            $targetFile = $date."_".$filename;
            $moveFile = $targetPath.$date."_".$filename;


            $table = "student_assignment_scripts";
          
          //get the assignment course code
          $getCourseCode = $operation->retrieveSingle("SELECT * FROM `lecturer_assignments_add` WHERE assignment_id = '$assignment_id'");
          $course_code = $getCourseCode['course_code'];
          
            //CHECK IF ALREADY SUBMITTED
            $count = $operation->countAll("SELECT * FROM `student_assignment_scripts` WHERE course_code = '$course_code' AND assignment_id = '$assignment_id' AND reg_no = '$reg_no'");
            if($count == 0){
                //if added comment
                if($assignment_comment != ''){
                    $data = [
                        'assignment_id' => "$assignment_id",
                        'reg_no' => "$reg_no",
                        'course_code' => "$course_code",
                        'file' => "$targetFile",
                        'student_comment' => "$assignment_comment",
                    ];
                }else{
                    //if comment not added
                    $data = [
                        'assignment_id' => "$assignment_id",
                        'reg_no' => "$reg_no",
                        'course_code' => "$course_code",
                        'file' => "$targetFile",
                    ];
                }


                if(move_uploaded_file($tempFile,$moveFile)){
                    $operation->insertData($table,$data);
//                    echo "<p class= 'alert alert-success text-center'>assignment upload successfully!</p>";
                    
                    //UPLOAD RESPONSE
                    $success = true;
                    $message = "Successfully Uploaded";
                }else{
                    $success = false;
                    $message = "Not Uploaded";
                } 
            }else{

                if($assignment_comment == ''){
                     $data = [
                        'file' => "$targetFile",
                        'student_comment' => "$assignment_comment",
                    ];
                }else{
                     $data = [
                        'file' => "$targetFile",
                    ];
                }


                $where = "assignment_id = '$assignment_id' AND course_code = '$course_code' AND reg_no = '$reg_no'";
                //delete old files
                if(move_uploaded_file($tempFile,$moveFile)){
                    $operation->updateData($table,$data,$where);

                    $fname = $operation->retrieveSingle("SELECT *FROM `student_assignment_scripts` WHERE $where");

                    $filename = $fname['file'];
                    $directory = "uploads/".$filename;
                    if(unlink($directory)){

                    }


                    $success = true;
                    $message = "Successfully Uploaded, file changed";
                }else{
                    $success = false;
                    $message = "Failed to change file";
                }


            }
          }else{
              $success = false;
              $message = "Upload Failed, make sure file is zip/pdf";
        }
    }else{
        $success = false;
        $message = "File too large to upload";
    }




}else{
    $success = false;
    $message = "Please select a file";
}

 header("Content-Type:application/json");
$response["success"] = $success;
$response["message"] = $message;
echo json_encode($response);


}else{
  header("Content-Type:application/json");
    $success = false;
    $message = "Unknown request";
    echo json_encode($response);
}

    



?>