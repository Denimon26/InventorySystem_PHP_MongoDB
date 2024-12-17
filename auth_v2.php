<?php include_once('includes/load.php');

require 'vendor/autoload.php';
use MongoDB\Client;



$req_fields = array('username', 'password');
validate_fields($req_fields);
$username = remove_junk($_POST['username']);
$password = remove_junk($_POST['password']);

if (empty($errors)) {

  $user = authenticate_v2($username, $password);

  if ($user){
    //Update Sign in time
    $_SESSION['user_id'] = $user['_id'];

    // redirect user to group home page by user level
    if ($user['user_level'] == 1)
    {
      $session->msg("s", "Hello " . $user['username'] . ", Welcome to LionsTech INV.");
      redirect('admin.php', false);
    }
    elseif ($user['user_level'] == 2)
    {
      $session->msg("s", "Hello " . $user['username'] . ", Welcome to LionsTech INV.");
      redirect('home.php', false);
    }
    else
    {
      $session->msg("s", "Hello " . $user['username'] . ", Welcome to LionsTech INV.");
      redirect('home.php', false);
    }
  }
  else{
    $session->msg("d", "Sorry Username/Password incorrect.");
    redirect('index.php', false);
  }

} else {

  $session->msg("d", $errors);
  redirect('login_v2.php', false);
}


function authenticate_v2($username = '', $password = '')
{
  $uri = 'mongodb+srv://boladodenzel:denzelbolado@cluster0.9ahxb.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
  $client = new Client($uri);
  $database = $client->selectDatabase('inventory_system');
  $admins = $database->selectCollection('admin');
  $users = $database->selectCollection('users');
  $user = $admins->findOne(['username' => $username]);
  if (!isset($user)) {
    $user = $users->findOne(['username' => $username]);
    if (!isset($user)) {
      echo '<script>alert("Please Check Credentials")</script>';
      return false;
    } else {
      if ($password != $user['password']) {
        echo '<script>alert("Please Check Credentials")</script>';
        return false;
      }
      else{
        return $user;
      }
    }
  } else {
    if ($password != $user['password']) {
      echo '<script>alert("Please Check Credentials")</script>';
      return false;
    }
    else{
      return $user;

    }

  }

}

?>