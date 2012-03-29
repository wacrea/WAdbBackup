<?php

// This script in php send you an email with the copy of your database. When you launch it ! (CRON or classic launch )

// v1.1
// Developed by William Agay (http://www.williamagay.fr)

///////////////////////////////////////////
//// Config
///////////////////////////////////////////

$email_destination = ''; // Owner

$host = ''; // Database host
$user = ''; // Database username
$pass = '';	// Database password
$db = '';	// Database name

///////////////////////////////////////////
// Saving database
///////////////////////////////////////////

$date = date("m-d-Y");
$backup = "bdd-backup_".$db.'_'.$date.".sql.gz";
// Use functions of : MySQLdump & GZIP for generate a gzip backup
$command = "mysqldump -h$host -u$user -p$pass $db | gzip> $backup";
system($command);

// Sending backup

$boundary = "_".md5 (uniqid (rand()));

$file_name = $backup;
  $attached_file = file_get_contents($backup); //file name ie: ./image.jpg
  $attached_file = chunk_split(base64_encode($attached_file));

  $attached = "\n\n". "--" .$boundary . "\nContent-Type: application; name=\"$file_name\"\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: attachment; filename=\"$file_name\"\r\n\n".$attached_file . "--" . $boundary . "--";

  $headers ="From: My server \r\n";
  $headers .= "MIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
  
  $message = 'La sauvegarde de votre base de donnée : '.$db.' du '.$date;

  $body = "--". $boundary ."\nContent-Type: text/plain; charset=ISO-8859-1\r\n\n".$message . $attached;

  @mail($email_destination,$message,$body,$headers); 
  
  // Delete the generated gzip
  unlink($backup);

?>