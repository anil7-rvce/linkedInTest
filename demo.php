<?php
    session_start();

   $config['base_url']             =   'http://localhost/auth.php';//example path
    $config['callback_url']         =   'http://localhost/demo.php';//example path 
      $config['linkedin_access']      =   '81na1lyzoskrhe';
    $config['linkedin_secret']      =   'ne2eXfF0pBkZqf31';

    include_once "linkedin.php";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "csnuser";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
    
    # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
    $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], $config['callback_url'] );
    //$linkedin->debug = true;

   if (isset($_REQUEST['oauth_verifier'])){
        $_SESSION['oauth_verifier']     = $_REQUEST['oauth_verifier'];

        $linkedin->request_token    =   unserialize($_SESSION['requestToken']);
        $linkedin->oauth_verifier   =   $_SESSION['oauth_verifier'];
        $linkedin->getAccessToken($_REQUEST['oauth_verifier']);

        $_SESSION['oauth_access_token'] = serialize($linkedin->access_token);
        header("Location: " . $config['callback_url']);
        exit;
   }
   else{
        $linkedin->request_token    =   unserialize($_SESSION['requestToken']);
        $linkedin->oauth_verifier   =   $_SESSION['oauth_verifier'];
        $linkedin->access_token     =   unserialize($_SESSION['oauth_access_token']);
   }


    # You now have a $linkedin->access_token and can make calls on behalf of the current member
    $xml_response = $linkedin->getProfile("~:(id,email-address,first-name,last-name,headline,picture-url,location)");

     echo '<pre>';
    echo 'My Profile Info<br>';
    echo  $xml_response;
    
     echo '<br />';
     echo '</pre>';

     $xml = simplexml_load_string($xml_response);
    echo '<pre>';
    print_r($xml);
    echo '</pre>';

  $values = array();

   foreach ($xml as $key => $value) {
       // echo $value .'<br>';
       array_push($values, $value);
   }
$max = sizeof($values);
// for($i = 0; $i < $max;$i++)
// {
//     echo $values[$i];
//     echo '<br/>';
// }
$linkedin_id=$values[0];
$linkedin_email=$values[1];
$linkedin_firstname=$values[2];
$linkedin_lastname=$values[3];
$linkedin_headline=$values[4];
$linkedin_picture=$values[5];
$linkedin_location=$values[6];

// echo "<h1>Location: </h1>" .$linkedin_location; 


// $sql = "INSERT INTO linkedin_profile VALUES ($linkedin_id, $linkedin_email, $linkedin_firstname, $linkedin_lastname, $linkedin_headline, $linkedin_picture, $linkedin_location)";




$check="SELECT email from linkedin_profile where email='$values[1]'";
// echo "Output";
// echo $check;
// echo "<br/>";
$rs = mysqli_query($conn,$check);

$data = mysqli_fetch_array($rs, MYSQLI_NUM);



$user_present="SELECT email from user where email='$values[1]'";
// echo "Output";
// echo $check;
// echo "<br/>";
$rs = mysqli_query($conn,$user_present);

$data_check = mysqli_fetch_array($rs, MYSQLI_NUM);
// echo "data_check" . $data_check;
if($data_check[0] == $values[1]) {
  
    
    echo "<b><h3>You have already registered with us</h3></b>";
}
elseif ($data[0] == $values[1]) {
  echo "<b><h3>User Already in Exists</h3></b><br/>";
}
else
{
  $sql = "INSERT INTO linkedin_profile VALUES ('$values[0]', '$values[1]','$values[2]','$values[3]','$values[4]','$values[5]','$values[6]')";

if ($conn->query($sql) === TRUE) {
    echo "<b>You have signed-up successfully</b>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    echo "Problem occured with registration, try again!";
        }
}
$conn->close();



    // $search_response = $linkedin->search("?company-name=facebook&count=10");
    // //$search_response = $linkedin->search("?title=software&count=10");

    // //echo $search_response;
    // $xml = simplexml_load_string($search_response);

    // echo '<pre>';
    // echo 'Look people who worked in facebook';
    // print_r($xml);
    // echo '</pre>';
?>