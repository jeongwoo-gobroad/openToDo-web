<?php
$pdo = new PDO('YOUR_DB', 
   'YOUR_DB');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
   $initial_use_command = "USE YOUR_DB";
   $initial_use = $pdo->prepare($initial_use_command);
   $initial_use->execute();
} catch (Exception $ex) {
   echo("Internal Error Occured");
   error_log("pdo.php error: " . $ex->getMessage());
   return;
}
?>