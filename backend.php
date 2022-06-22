<?php
session_start();
ob_start();

    function filterinput($str){
        $str = htmlspecialchars($str);
        $str = filter_var($str, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return $str;
    }

    function userIP(){
        switch(true){
         case (!empty($_SERVER['HTTP_X_REAL_IP'])) : return $_SERVER['HTTP_X_REAL_IP'];
         case (!empty($_SERVER['HTTP_CLIENT_IP'])) : return $_SERVER['HTTP_CLIENT_IP'];
         case (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) : return $_SERVER['HTTP_X_FORWARDED_FOR'];
         default : return $_SERVER['REMOTE_ADDR'];
       }
   
      }

    $inputs = ['phone','message'];

    foreach($inputs as $input){
        
       if(empty($_POST[$input])){
            $error = $input.' is required';
       }else{
            $$input = filterinput($_POST[$input]);
        }

    }

    if(@$_POST['_token'] !== @$_SESSION['_token']){
          $error = 'Please Try With Other Browser';
     }

        if(isset($error)){
            $_SESSION['result'] = $error;
        }else{

            define('DB_HOST','localhost');
            define('DB_USERNAME','root');
            define('DB_PASSWORD','');
            define('DB_DB','whatspy');

            $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_DB.";charset=utf8", DB_USERNAME, DB_PASSWORD);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $conn->prepare('INSERT INTO `messages` (`phone`,`message`,`userip`) VALUES (?,?,?)')
            ->execute([$phone,$message,userIP()]);

            $conn = null;

            $_SESSION['result'] = 'SuccessFull Message in The Way';

        }
        
        header('location:index.php');
ob_end_flush();
?>