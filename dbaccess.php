<!-- (c)2024 Jeongwoo Kim, KNU CSE -->
<?php
$pdo = new PDO('mysql:host = localhost; port = 17686; dbname = PrjVoid_UserDB', 
   'jeongwoo_dev_php', '!@Aa86010104');
// See the "errors" folder for details...
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
   $initial_use_command = "USE PrjVoid_UserDB";
   $initial_use = $pdo->prepare($initial_use_command);
   $initial_use->execute();
} catch (Exception $ex) {
   echo("Internal Error Occured");
   error_log("pdo.php error: " . $ex->getMessage());
   return;
}
?>