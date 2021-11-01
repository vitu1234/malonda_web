<?php
	require_once("assets/vendors/PHPMailer/src/PHPMailer.php");
	require_once("assets/vendors/PHPMailer/src/SMTP.php");
	require_once("assets/vendors/PHPMailer/src/Exception.php");

	$body="
		<div style='max-width:600px;margin:0px auto; border:1px solid #F1F3F4;border-radius:5px; padding:16px;background-color:#F1F3F4;'>
			<p align='center'>
				<img src='cid:Ulendowathu' style='max-height:100px;''>
			</p>
			<hr style='border:1px solid #163665;'>
			<div>
				$body_
			</div>
		<div>
	";

    $mailer_name="Ulendowathu";
	$mailer_email="";  //EMAIL ADDRESS HERE***
	$mailer_password="";  //EMAIL PASSWORD HERE***
	$mailer_website="ulendowathu.com";

	$mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->IsSMTP(); // enable SMTP
    $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587; // or 587
    $mail->IsHTML(true);
    $mail->Username = $mailer_email;
    $mail->Password = $mailer_password;
    $mail->setFrom($mailer_email,$mailer_name);
    $mail->AddEmbeddedImage("/assets/images/logo-3.png","Ulendowathu");
    $mail->Subject = $subject_;  //SUBJECT HERE***
    $mail->Body = $body;   //BODY HERE***

    $mail->addAddress($email_to);

	if(!$mail->Send()){
		//echo "<p class='alert alert-warning'>Mailer Error: " . $mail->ErrorInfo ."</p>";
		return 0;
	}else{
		return 1;
	}

?> 
