<!DOCTYPE html>
<html lang="">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
</head>

<body>
  
  <form action="upload_assignment.php" method="post" enctype="multipart/form-data"> 
    <input type="file" name="file" id="file" required/>
    <input type="text" name="reg_no" value="BScICT/26/16" required />
    <input type="text" name="assignment_id" value="8" required/>
    <input type="text" name="assignment_comment" value="this is the comment" required/>
    <button type="submit" >Submit</button>
  </form>
  
</body>
</html>
