<?php
require_once ("include/braintree_init.php");
require_once 'vendor/braintree/braintree_php/lib/Braintree.php';
$clientToken = Braintree_ClientToken::generate();

echo  json_encode(array("error"=>false, "message"=>$clientToken));
?>