
<?php
   // Include autoloader 
require_once 'autoload.inc.php'; 

//$html = "<imghttp://localhost/finalproject29/lecturer/includes/ckeditor/upload/309822295.jpg";
$html = '<p><img alt="" src="http://localhost/finalproject29/lecturer/includes/ckeditor/upload/1714111035.png" style="height:123px; width:200px" /></p>';
//echo $_SERVER['DOCUMENT_ROOT'];
//
//
//echo $html;
//die();
$pageURL = '';

if (isset($_SERVER['HTTPS'])) {
    $pageURL = "https://". $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']);

}else{
    echo "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']); 
}
echo $pageURL;

 
die();
 echo "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']); die();


$filename = "file";


 
 
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