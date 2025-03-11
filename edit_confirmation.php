<?php
    require 'config/config.php';

    // DB Connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ( $mysqli->connect_errno ) {
        echo $mysqli->connect_error;
        exit();
    }

    $mysqli->set_charset('utf8');

    $match_id = $_POST['match_id'];

    $league_id = $_POST['league_id'];

    $opp_name = $_POST['opponent_team'];

    $series_score = $_POST['series_score'];

    $map_one_id = $_POST['map_one_id'];

    if(isset($_POST['map_two_id']) && trim($_POST['map_two_id']) != '')
    {
        $map_two_id = $_POST['map_two_id'];
    }
    else 
    {
        $map_two_id = 'null';
    }

    if(isset($_POST['map_three_id']) && trim($_POST['map_three_id']) != '')
    {
        $map_three_id = $_POST['map_three_id'];
    }
    else 
    {
        $map_three_id = 'null';
    }

    $map_one_score = $_POST['map_one_score'];

    if(isset($_POST['map_two_score']) && trim($_POST['map_two_score']) != '')
    {
        $map_two_score = $_POST['map_two_score'];
    }
    else
    {
        $map_two_score = 'null';
    }

    if(isset($_POST['map_three_score']) && trim($_POST['map_three_score']) != '')
    {
        $map_three_score = $_POST['map_three_score'];
    }
    else
    {
        $map_three_score = 'null';
    }

    $opp_comp_one = $_POST['opponent_comp_one'];
    $usc_comp_one = $_POST['usc_comp_one'];

    if(isset($_POST['opponent_comp_two']) && trim($_POST['opponent_comp_two']) != '')
    {
        $opp_comp_two = $_POST['opponent_comp_two'];
    }
    else 
    {
        $opp_comp_two = 'null';
    }

    if(isset($_POST['usc_comp_two']) && trim($_POST['usc_comp_two']) != '')
    {
        $usc_comp_two = $_POST['usc_comp_two'];
    }
    else 
    {
        $usc_comp_two = 'null';
    }

    if(isset($_POST['opponent_comp_three']) && trim($_POST['opponent_comp_three']) != '')
    {
        $opp_comp_three = $_POST['opponent_comp_three'];
    }
    else 
    {
        $opp_comp_three = 'null';
    }

    if(isset($_POST['usc_comp_three']) && trim($_POST['usc_comp_three']) != '')
    {
        $usc_comp_three = $_POST['usc_comp_three'];
    }
    else 
    {
        $usc_comp_three = 'null';
    }

    $match_date = $_POST['match_date'];

    $sql = "UPDATE matches 
            SET league_id = $league_id,
                opponent_name = '$opp_name',
                series_score = '$series_score',
                map_one_id = $map_one_id,
                map_two_id = $map_two_id,
                map_three_id = $map_three_id,
                map_one_score = '$map_one_score',
                map_two_score = '$map_two_score',
                map_three_score = '$map_three_score',
                opponent_comp_one = '$opp_comp_one',
                usc_comp_one = '$usc_comp_one',
                opponent_comp_two = '$opp_comp_two',
                usc_comp_two = '$usc_comp_two',
                opponent_comp_three = '$opp_comp_three',
                usc_comp_three = '$usc_comp_three',
                match_date = '$match_date'
            WHERE match_id = $match_id;";

    $results = $mysqli->query($sql);

    if (!$results) {
        echo $mysqli->error;
        $mysqli->close();
        exit();
    }
    
    // Close DB Connection
    $mysqli->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="This page is only to confirm to the user that the match they wanted to edit has been edited. I feel that giving the user simple feedback like this always helps so they don't wonder if what they did worked or not.">
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
            border-radius: 8px;
            text-align: center;
            height: 740px;
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
        <div class="text-success mt-5">
            Game against <span class="font-italic"><?php echo $_POST['opponent_team']; ?></span> was successfully edited.
        </div>
    </div>
    
    <footer>
        <p>&copy; 2023 USC Valorant</p>
    </footer>

    <!-- Include Bootstrap JS (optional, only if you need Bootstrap JavaScript features) -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>

    </script>
</body>
</html>