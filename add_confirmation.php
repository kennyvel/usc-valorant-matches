<?php
    require 'config/config.php';

    // No need to parse and check inputs, I checked before the form submission

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ( $mysqli->connect_errno ) {
        echo $mysqli->connect_error;
        exit();
    }

    $mysqli->set_charset('utf8');

    $league_id = $_POST['league_id'];

    // echo $league_id;
    // echo '<br>';

    $opp_name = $_POST['opponent_team'];

    // echo $opp_name;
    // echo '<br>';

    $series_score = $_POST['series_score'];

    // echo $series_score;
    // echo '<br>';

    $map_one_id = $_POST['map_one_id'];

    // echo $map_one_id;
    // echo '<br>';

    if(isset($_POST['map_two_id']) && trim($_POST['map_two_id']) != '')
    {
        $map_two_id = $_POST['map_two_id'];
    }
    else 
    {
        $map_two_id = 'null';
    }

    // echo $map_two_id;
    // echo '<br>';

    if(isset($_POST['map_three_id']) && trim($_POST['map_three_id']) != '')
    {
        $map_three_id = $_POST['map_three_id'];
    }
    else 
    {
        $map_three_id = 'null';
    }

    // echo $map_three_id;
    // echo '<br>';

    $map_one_score = $_POST['map_one_score'];

    // echo $map_one_score;
    // echo '<br>';

    if(isset($_POST['map_two_score']) && trim($_POST['map_two_score']) != '')
    {
        $map_two_score = $_POST['map_two_score'];
    }
    else
    {
        $map_two_score = 'null';
    }

    // echo $map_two_score;
    // echo '<br>';

    if(isset($_POST['map_three_score']) && trim($_POST['map_three_score']) != '')
    {
        $map_three_score = $_POST['map_three_score'];
    }
    else
    {
        $map_three_score = 'null';
    }

    // echo $map_three_score;
    // echo '<br>';

    $opp_comp_one = $_POST['opponent_comp_one'];
    $usc_comp_one = $_POST['usc_comp_one'];

    // echo $opp_comp_one;
    // echo '<br>';

    // echo $usc_comp_one;
    // echo '<br>';

    if(isset($_POST['opponent_comp_two']) && trim($_POST['opponent_comp_two']) != '')
    {
        $opp_comp_two = $_POST['opponent_comp_two'];
    }
    else 
    {
        $opp_comp_two = 'null';
    }

    // echo $opp_comp_two;
    // echo '<br>';

    if(isset($_POST['usc_comp_two']) && trim($_POST['usc_comp_two']) != '')
    {
        $usc_comp_two = $_POST['usc_comp_two'];
    }
    else 
    {
        $usc_comp_two = 'null';
    }

    // echo $usc_comp_two;
    // echo '<br>';

    if(isset($_POST['opponent_comp_three']) && trim($_POST['opponent_comp_three']) != '')
    {
        $opp_comp_three = $_POST['opponent_comp_three'];
    }
    else 
    {
        $opp_comp_three = 'null';
    }

    // echo $opp_comp_three;
    // echo '<br>';

    if(isset($_POST['usc_comp_three']) && trim($_POST['usc_comp_three']) != '')
    {
        $usc_comp_three = $_POST['usc_comp_three'];
    }
    else 
    {
        $usc_comp_three = 'null';
    }

    // echo $usc_comp_three;
    // echo '<br>';

    $match_date = $_POST['match_date'];

    // echo $match_date;

    $sql = "INSERT INTO matches (league_id, opponent_name, series_score, map_one_id, map_two_id, map_three_id, map_one_score,map_two_score, map_three_score, match_date, opponent_comp_one, usc_comp_one, opponent_comp_two, usc_comp_two, opponent_comp_three, usc_comp_three) VALUES ($league_id, '$opp_name', '$series_score', $map_one_id, $map_two_id, $map_three_id, '$map_one_score', '$map_two_score', '$map_three_score', '$match_date', '$opp_comp_one', '$usc_comp_one', '$opp_comp_two', '$usc_comp_two', '$opp_comp_three', '$usc_comp_three');";

    $result = $mysqli->query($sql);

    if (!$result) {
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
    <meta name="description" content="This page is only to confirm to the user that the match they wanted to add has been added. I feel that giving the user simple feedback like this always helps so they don't wonder if what they did worked or not.">
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
            Game against <span class="font-italic"><?php echo $_POST['opponent_team']; ?></span> on <span class="font-italic"><?php echo $_POST['match_date']; ?></span> was successfully added.
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