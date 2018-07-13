<?php

    require_once('inc/header.php');

    if($_POST)
    {
        foreach($_POST as $key => $value) 
        {
            $_POST[$key] = addslashes($value);
        }

        // debug($_POST);
        // debug($_FILES);

        if(!empty($_FILES['photo']['name'])) // I'm checking if I got a result for the 1st photo
        {
            // I give a random name for my photo.
            $photo_name = $_POST['firstname'] . '_' . $_POST['pseudo'] . '_' . time() . '-' . rand(1,999) .  '_' . $_FILES['photo']['name'];

            $photo_name = str_replace(' ', '-', $photo_name);

            // we register the path of my file
            $photo_path = ROOT_TREE . 'uploads/img/' . $photo_name;

            $max_size = 2000000;

            if($_FILES['photo']['size'] > $max_size || empty($_FILES['photo']['size']))
            {
                $msg_error .= "<div class='alert alert-danger'>Please select a 2Mo file maximum !</div>";
            }

            $type_photo = ['image/jpeg', 'image/png', 'image/gif'];
            
            if(!in_array($_FILES['photo']['type'], $type_photo) || empty($_FILES['photo']['type']))
            {
                $msg_error .= "<div class='alert alert-danger'>Please select a JPEG/JPG, a PNG or a GIF file.</div>";
            }

        }
        elseif (isset($_POST['actual_photo'])) // if I update a user, I target the new input created with my $update_user
        {
            $photo_name = $_POST['actual_photo'];
        }
        else
        {
            $photo_name = 'default.jpg';
        }

        // OTHER CHECK POSSIBLE HERE

        if(empty($msg_error))
        {

            if(!empty($_POST['id_user'])) // we register the update
            {
                $result = $pdo->prepare("UPDATE user SET pseudo=:pseudo, pwd=:pwd, firstname=:firstname, lastname=:lastname, photo=:photo, email=:email, gender=:gender, city=:city, zip_code=:zip_code, address=:address, privilege=:privilege WHERE id_user = :id_user");

                $result->bindValue(':id_user', $_POST['id_user'], PDO::PARAM_INT);
            }
            else // we register for the first time in the DTB
            {
                $result = $pdo->prepare("INSERT INTO user (pseudo, pwd, firstname, lastname, photo, email, gender, city, zip_code, address, privilege) VALUES (:pseudo, :pwd, :firstname, :lastname, :photo, :email , :gender, :city, :zip_code, :address, :privilege)");
            }

            $result->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
            $result->bindValue(':pwd', $_POST['pwd'], PDO::PARAM_STR);
            $result->bindValue(':firstname', $_POST['firstname'], PDO::PARAM_STR);
            $result->bindValue(':lastname', $_POST['lastname'], PDO::PARAM_STR);
            $result->bindValue(':photo', $photo_name, PDO::PARAM_STR);
            $result->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
            $result->bindValue(':gender', $_POST['gender'], PDO::PARAM_STR);
            $result->bindValue(':city', $_POST['city'], PDO::PARAM_STR);
            $result->bindValue(':zip_code', $_POST['zip_code'], PDO::PARAM_STR);
            $result->bindValue(':address', $_POST['address'], PDO::PARAM_STR);
            $result->bindValue(':privilege', $_POST['privilege'], PDO::PARAM_INT);

            if($result->execute()) // if the request was inserted ine the DTB
            {
                if(!empty($_FILES['photo']['name']))
                {
                    copy($_FILES['photo']['tmp_name'], $photo_path); 
                }

                if(!empty($_POST['id_user']))
                {
                    header('location:user_list.php?m=update');
                }
            }

        }

    }

    if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id']))
    {
        $req = "SELECT * FROM user WHERE id_user = :id_user";

        $result = $pdo->prepare($req);
        $result->bindValue(':id_user', $_GET['id'], PDO::PARAM_INT);
        $result->execute();

        if($result->rowCount() == 1)
        {
            $update_user = $result->fetch();
        }
    }

    $pseudo = (isset($update_user)) ? $update_user['pseudo'] : '';
    $pwd = (isset($update_user)) ? $update_user['pwd'] : '';
    $firstname = (isset($update_user)) ? $update_user['firstname'] : '';
    $lastname = (isset($update_user)) ? $update_user['lastname'] : '';
    $photo = (isset($update_user)) ? $update_user['photo'] : '';
    $email = (isset($update_user)) ? $update_user['email'] : '';
    $gender = (isset($update_user)) ? $update_user['gender'] : '';
    $city = (isset($update_user)) ? $update_user['city'] : '';
    $zip_code = (isset($update_user)) ? $update_user['zip_code'] : '';
    $address = (isset($update_user)) ? $update_user['address'] : '';
    $privilege = (isset($update_user)) ? $update_user['privilege'] : '';
    $id_user = (isset($update_user)) ? $update_user['id_user'] : '';

    $action = (isset($update_user)) ? "Update" : 'Add';

?>

    <h1 class="h2"><?= $action ?> a user</h1>

    <form action="" method="post" enctype="multipart/form-data">
        <?= $msg_error ?>
        <input type='hidden' name="id_user" value="<?= $id_user ?>">
        <div class="form-group">
            <input type="text" class="form-control" name="pseudo" placeholder="pseudo of the user..." value="<?= $pseudo ?>">
        </div>
        <input type='hidden' name="pwd" value="<?= $pwd ?>">
        <div class="form-group">
            <input type="text" class="form-control" name="firstname" placeholder="firstname of the user..."  value="<?= $firstname ?>">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="lastname" placeholder="lastname of the user..."  value="<?= $lastname ?>">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="email" placeholder="email of the user..."  value="<?= $email ?>">
        </div>
        <div class="form-group">
            <select class="form-control" name="gender">
                <option disabled selected>gender of the user...</option>
                <option <?php if($gender == 'm'){ echo 'selected';} ?>>m</option>
                <option <?php if($gender == 'f'){ echo 'selected';} ?>>f</option>
                <option <?php if($gender == 'o'){ echo 'selected';} ?>>o</option>
            </select>
        </div>
        <div class="form-group">
            <label for="photo">user photo</label>
            <input type="file" class="form-control-file" id="photo" name="photo">
            <?php
                if(isset($update_user))
                {
                    echo "<input name='actual_photo' value='$photo' type='hidden'>";
                    echo "<img style='width:10%;' src='" . URL . "uploads/img/$photo'>";
                }
            ?>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="city" placeholder="city of the user..."  value="<?= $city ?>">
        </div>
        <div class="form-group">
            <input type="number"  class="form-control" name="zip_code" placeholder="zip_code of the user..."  value="<?= $zip_code ?>">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="address" placeholder="Address of the user..."  value="<?= $address ?>">
        </div>
        <div class="form-group">
            <input type="number" class="form-control" name="privilege" placeholder="Privilege of the user..."  value="<?= $privilege ?>">
        </div>
        <input type="submit" value="<?= $action ?> the user" class="btn btn-info btn-lg btn-block">
    </form>


<?php
    require_once('inc/footer.php');
?>