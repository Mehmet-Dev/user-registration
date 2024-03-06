<?php
session_start();
require_once('config.php')

$token = bin2hex(random_bytes(CSRF_TOKEN_LENGTH)); // Generate CRSF token

$_SESSION['token'] = $token;
?>

<!DOCTYPE html>
<html lang="nl-NL">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>

<body>
    <div class="container main-container">
        <?php 
        if (isset($_SESSION['ID']) && isset($_SESSION['Username'])) { // User logged in, display them this 
            ?>
            <div id="records-table">
                <h1>U bent ingelogd!</h1>
                <h2>Gebruikersnaam: <?php echo $_SESSION['Username'] ?></h2>
                <h2>ID: <?php echo $_SESSION['ID'] ?></h2>
                <a href="logout.php" class="btn btn-danger">Uitloggen</a>
            </div>
            <?php
        }

        if(!isset($_SESSION['ID']) && !isset($_SESSION['Username'])) { // User not logged in, display the login/register forms
            ?>
            <form action="db.php" method="post" id="register-form">
                <p>
                    <?php 
                    if(isset($_SESSION['errors'])) { //Check if there were any error or success messages
                        echo '<span style="color:red;">';
                        echo "Er is een fout opgetreden.\n";
                        foreach($_SESSION['errors'] as $error) {
                            echo $error . '<br>';
                        }
                        echo "</span>";
                    }
                    else if(isset($_SESSION['gelukt']))
                    {
                        echo '<span style="color:green;">';
                        echo "Registratie successvol!";
                        echo "</span>";
                    }
                    ?>
                </p>
                <input type="hidden" name="process" value="register">
                <input type="hidden" name="token" value="<?php echo $token ?>">
                <h2>User Registratie</h2>

                <label for="username">Gebruikersnaam:</label>
                <input type="text" name="username" required>
                
                <label for="password">Wachtwoord:</label>
                <input type="password" name="password" minlength="8" required>

                <button type="submit">Register</button>
                <button id="view-login-form" class="btn-danger">Liever inloggen</button>
            </form>
            
            <form action="db.php" method="post" id="login-form">
                <p style="color:red;">
                    <?php 
                    if(isset($_SESSION['errors'])) {
                        echo "Er is een fout opgetreden.\n";
                        foreach($_SESSION['errors'] as $error) {
                            echo $error . '<br>';
                        }
                        unset($_SESSION['errors']);
                    }
                    else if(isset($_SESSION['gelukt']))
                    {
                        echo '<span style="color:green;">';
                        echo "Registratie successvol!";
                        echo "</span>";
                        unset($_SESSION['gelukt']);
                    }
                    ?>
                </p>
                <input type="hidden" name="process" value="login">
                <input type="hidden" name="token" value="<?php echo $token ?>">
                <h2>Inloggen</h2>

                <label for="username">Gebruikersnaam:</label>
                <input type="text" name="username" required>

                <label for="password">Wachtwoord:</label>
                <input type="password" name="password" minlength="8" required>

                <button type="submit">Inloggen</button>
                <button id="view-register-form" class="btn-red">Liever registreren</button>
            </form>
            <?php
        }
        ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="script.js"></script>
</body>

</html>