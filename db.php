<?php
session_start();
require_once('config.php')

// Only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    header('Refresh: 3; URL=index.php');
    echo 'Only POST requests are allowed.';
    exit;
}

/**
 * Database class for connections
 */
class Database
{
    private $host;
    private $username;
    private $password;
    private $dbname;
    private $charset;

    protected $pdo;
    /**
     * Constructor for creating a database class
     */
    public function __construct($host, $username, $password, $dbname, $charset = 'utf8')
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->charset = $charset;

        $this->connect();
    }

    /**
     * Creating the connection
     */
    private function connect()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";

        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $_SESSION['errors'] = ["Er was een fout opgetreden met inloggen."];
            header('Location: index.php');
        }
    }

    /**
     * Register user
     * 
     * @param string $username Username of the user, validatred
     * @param string $password Provided password, validated
     */
    function register_user($username, $password)
    {
        $pass = password_hash($password, PASSWORD_DEFAULT); //Create password hash
        $query = $this->pdo->prepare("INSERT INTO user_registration VALUES (NULL, :user, :pass)"); //Prepared statement
        $query->bindParam(':user', $username);
        $query->bindParam(':pass', $pass);

        try { //If execution is complete, return them back to the main page
            $query->execute();
            $_SESSION['gelukt'] = true;
            header('Location: index.php');
        } catch (PDOException $e) { //If execution failed, provide error message
            $_SESSION['errors'] = ["Er was een fout opgetreden met inloggen."];
            header('Location: index.php');
        }
    }

    /**
     * Login user
     * 
     * @param string $username Username of the user
     * @param string $password Password of the user
     */
    function login_user($username, $password)
    {
        $query = $this->pdo->prepare("SELECT * FROM user_registration WHERE Username = :user");
        $query->bindParam(":user", $username);

        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);

        if($row)
        {
            if(password_verify($password, $row['Password']))
            {
                $_SESSION['Username'] = $username;
                $_SESSION['ID'] = $row['ID'];
                header("Location: index.php");
            }
            else
            {
                $_SESSION['errors'] = ['Wachtwoord is incorrect.'];
                header("Location: index.php");
            }
        }
        else {
            $_SESSION['errors'] = ['Gebruiker niet gevonden.'];
            header("Location: index.php");
        }
    }
}

$db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

//Statement to check if the token are matching 
if ($_POST['token'] == $_SESSION['token']) {
    if ($_POST['process'] == 'register') { //Registering user, validate input
        $username = $_POST['username'];
        $pass = $_POST['password'];

        $errors = [];

        if (!preg_match('/^[a-zA-Z0-9]{8,20}$/', $username)) {
            $errors[] = 'Gebruikersnaam is of te lang, te kort, of bevat illegale karakters.';
        }

        if (!preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,30}$/', $pass)) {
            $errors[] = 'Wachtwoord moet 8-30 karakters lang zijn, heeft 1 hoofdletter, 1 nummer en 1 speciale karakter nodig.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php');
        } else {
            $db->register_user($username, $pass);
        }
    } else if ($_POST['process'] == "login") { // Login user in, check if inputs are not empty
        $username = $_POST['username'];
        $password = $_POST['password'];

        $errors = [];

        if (empty($username) || empty($password)) {
            $errors[] = 'Gebruikersnaam/wachtwoord zijn verplicht!';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php');
        } else {
            $db->login_user($username, $password);
        }
    }
} else {
    header('Refresh: 3; URL=index.php');
    echo 'Invalid token';
}
