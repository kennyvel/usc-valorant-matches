<?php
    if( !isset($_GET['match_id']) || trim($_GET['match_id']) == '')
    {
        $error = "Invalid URL.";
    } else {
        require 'config/config.php';

        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ( $mysqli->connect_errno ) {
            echo $mysqli->connect_error;
            exit();
        }

        $mysqli->set_charset('utf8');

        $match_id = $_GET['match_id'];

        $sql = "SELECT * FROM matches WHERE match_id = $match_id;";
        $results = $mysqli->query($sql);

        if (!$results) {
            echo $mysqli->error;
            $mysqli->close();
            exit();
        }

        $row = $results->fetch_assoc();
        $opponent_name = $row['opponent_name'];
        $match_date = $row['match_date'];

        $sql_delete = "DELETE FROM matches WHERE match_id = $match_id;";

        $results_delete = $mysqli->query($sql_delete);

        if (!$results_delete) {
            echo $mysqli->error;
            $mysqli->close();
            exit();
        }

        // Close DB Connection
        $mysqli->close();

    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="This page is for deleting the match indicated by the user and giving the user some indication that the deletion was successful.">
    <title>USC Trojan Esports</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: var(--main-color);
            margin: 0;
            padding: 0;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
        }

        .container {
            align-items: center;
            color: black;
            border-radius: 8px;
            text-align: center;
            height: 800px;
            padding: 0;
        }

    </style>
</head>
<body>
    <header>
        <div class="navbar">
            <a href="home.html">
                <img src="images/usc_valorant.jpeg" alt="Logo" class="logo">
            </a>
            <ul class="nav-links">
                <li><a href="home.html">Home</a></li>
                <li><a href="valorant.html">Team</a></li>
                <li><a href="add_form.php">Match Submission</a></li>
                <li><a href="match_results.php">Match Results</a></li>
                <li><a href="https://discord.gg/VKmER52DVY">Discord</a></li>
                <!-- Add more list items as needed -->
            </ul>
        </div>
    </header>

    <div class="container">
        <?php if( isset($error) && trim($error) != '' ): ?>

            <div class="text-danger mt-5">
                <!-- Show Error Messages Here. -->
                <?php echo $error; ?>
            </div>

        <?php else: ?>

            <div class="text-success mt-5">
                Match against <span class="font-italic"><?php echo $opponent_name; ?></span> on <span class="font-italic"><?php echo $match_date; ?></span> was successfully deleted.
            </div>

        <?php endif; ?>

        <a href="match_results.php" role="button" class="btn btn-primary mt-5">Back to Match Results</a>
    </div>
    
    <footer>
        <p>&copy; 2023 USC Trojan Esports (have other links here for things like Discord servers and social media)</p>
    </footer>

    <!-- Include Bootstrap JS (optional, only if you need Bootstrap JavaScript features) -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>

    </script>
</body>
</html>