<?php
error_reporting(E_ERROR | E_PARSE);

$content = $_POST["content"];
$user = $_SESSION["username"];

$host = 'localhost';
$dbname = 'chat_db';
$username_db = 'root';
$password_db = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['authenticated']) {
        if ($_SESSION["content"] === "" || $_SESSION["content"] === " ") {
            $errorMessage = "Error";
        } else {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare("INSERT INTO posts (content, user) VALUES (:content, :user)");
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':user', $user);
            $stmt->execute();

            $successMessage = "Message successfully added";
        }
    } else {
        $errorMessage = "You are not logged in";
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Chatroom</title>
    <style>
        #heading,
        #subheading,
        #content {
            display: none;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="/home">Chat</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-item nav-link <?= $_SESSION["site"] === "/views/index.php" ? "active" : "" ?>"
                            href="/home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-item nav-link <?= $_SESSION["site"] === "/views/index.php" ? "active" : "" ?>"
                            href="/blog">Chat</a>
                    </li>
                    <?php if (isset($_SESSION["authenticated"])) { ?>
                        <li class="nav-item">
                            <a class="nav-item nav-link" href="/logout">Logout</a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item">
                            <a class="nav-item nav-link <?= $_SESSION["site"] === "/views/login.php" ? "active" : "" ?>"
                                href="/login">Login</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </nav>

    </header>

    <main class="container mt-4">
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>
        <?php if (isset($successMessage)): ?>
            <div class="alert alert-success">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>
        <h1 id="heading">Add message</h1>
        <form method="post" action="">
            <div class="form-group">
                <label for="content">Content</label>
                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add post</button>
        </form>

        <h2>Messages</h2>
        <ul>
            <?php
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare("SELECT content, user FROM posts");
            $stmt->execute();
            $posts = $stmt->fetchAll();

            foreach ($posts as $post) {
                echo "<li><strong>" . $post['user'] . "</strong><br>" . $post['content'] . "</li>";
            }
            ?>
        </ul>
    </main>

    <footer class="mt-4 text-center">
        <p>&copy; 2023 Vít Vosol. All rights reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#heading").fadeIn(1500);
            $("#subheading, #content").slideDown(1000);
        });
    </script>
</body>

</html>