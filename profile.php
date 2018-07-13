<?php
require_once("inc/header.php");

$page = "User profile";

if(!userConnect())
{
    header('location:login.php');
    exit();
}

$result = $pdo->query("SELECT * FROM user");
$users = $result->fetchAll();

foreach ($users as $key => $value) {
            
	if ($key == 'profile_photo') {
		$content .= '<td><img height="100" src="' . URL . 'uploads/img/' . $value . '" alt="' . $users['photo'] . '"/></td>';
	} 
}

?>

<h1><?= $page ?></h1>
<hr>
<img height="100" src=<?= $photo ?> /> 
<ul id="info">
    <li><strong>First name:</strong> <?= $_SESSION['user']['firstname']?></li>
    <li><strong>Last name:</strong> <?= $_SESSION['user']['lastname']?></li>
    <li><strong>E-mail:</strong> <?= $_SESSION['user']['email']?></li>
</ul>
<hr>
<h2><strong>Orders list</strong></h2>

<style>
hr{
    background-color: white;
}

#info li{
    list-style-type: none;
    border: 1px solid #74808b;
    padding: 7px;
}

main{
    background-color: #434a50;
    box-shadow: 13px 13px 13px 20px #434a50;
    height: 100%;
    color: white
}

img{
    border: 1px solid white;
}

h1, h2{
    text-shadow: 1px 1px #74808b;
}


</style>


<?php
    require_once("inc/footer.php");
?>