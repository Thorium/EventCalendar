<?php
 header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
 header('Expires: '. gmdate("D, d M Y H:i:s") , ' GMT');
 header("Cache-Control: no-cache, must-revalidate");
 header("Pragma: no-cache");

if(isset($_POST["sendmail"])){
    if($_POST["answer"]=="kahdeksan" || $_POST["answer"]=="kaheksan"){ //Uusi kohde
    
        //Debug:
        ini_set('display_errors', 'On');

        //Alustetaan sivu:
        require 'dataaccess.php';
        $DA = new Data_Access;
        $DA->openDatabase();
    
        //// Mail:
        //$to = "tuomas.testspam@mailinator2.com";
        //$subject = "Palaute testikalenterista";
        //$message = htmlspecialchars($_POST["mailbody"]);
        //$from = htmlspecialchars($_POST["mailfrom"]);
        //$headers = "From: $from";
        //mail($to,$subject,$message,$headers);
        
        $sql="insert into ContactRequest(Request, Email) values ('".str_replace("'","''", $_POST["mailbody"])."','".str_replace("'","''", $_POST["mailfrom"])."')";
        $DA->insertRow($sql);
        
        echo "ok";
    } else {
        echo "safty-fail";
    }
} else { 
        echo "no-data";
}
?>