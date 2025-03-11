<?php
    require 'config/config.php';

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ( $mysqli->connect_errno ) {
        echo $mysqli->connect_error;
        exit();
    }

    $mysqli->set_charset('utf8');

    // Matches:
    $sql_matches = "SELECT * FROM matches ORDER BY match_date DESC;";
    $results_matches = $mysqli->query($sql_matches);
    if ( $results_matches == false ) {
        echo $mysqli->error;
        $mysqli->close();
        exit();
    }

    // Leagues:
    $sql_leagues = "SELECT * FROM leagues;";
    $results_leagues = $mysqli->query($sql_leagues);
    if ( $results_leagues == false ) {
        echo $mysqli->error;
        $mysqli->close();
        exit();
    }

    // Maps:
    $sql_maps = "SELECT * FROM maps;";
    $results_maps = $mysqli->query($sql_maps);
    if ( $results_maps == false ) {
        echo $mysqli->error;
        $mysqli->close();
        exit();
    }

    $total_results = $results_matches->num_rows;
    $results_per_page = 10;
    $last_page = ceil($total_results / $results_per_page);

    if (isset($_GET['page']) && trim($_GET['page']) != '') {
        $current_page = $_GET['page'];
    } else {
        $current_page = 1;
    }

    if ($current_page < 1 || $current_page > $last_page) {
        $current_page = 1;
    }

    $start_index = ($current_page - 1) * $results_per_page;

    $sql = rtrim($sql_matches, ';');

    // echo "<hr>";
    // echo $sql;

    $sql = "SELECT * FROM matches ORDER BY match_date DESC LIMIT $start_index, $results_per_page;";

    $results = $mysqli->query($sql);

    if ( !$results ) {
        echo $mysqli->error;
        $mysqli->close();
        exit();
    }

    $duelists = ['Jett', 'Iso', 'Neon', 'Phoenix', 'Raze', 'Reyna', 'Yoru'];
    $initiators = ['Breach', 'Fade', 'Gekko', 'Kayo', 'Skye', 'Sova'];
    $controllers = ['Astra', 'Brimstone', 'Harbor', 'Omen', 'Viper'];
    $sentinels = ['Chamber', 'Cypher', 'Deadlock', 'Killjoy', 'Sage'];

    // Close DB Connection
    $mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="This page shows all the matches that are in the database sorted by date, with the most recent match showing first. This shows the user all the matches and more information about each match if they expand each entry to show map information. Also where they can choose to delete or edit matches!">
    <!-- Include Bootstrap CSS -->
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
            padding-top: 25px;
            background-color: var(--main-color);
            align-items: center;
            text-align: center;
            min-height: 740px;
        }

        .match-bar {
            border-top: 1px solid #666;
            min-height: 75px;
            display: flex;
            justify-content: space-between;
            background-color: #40474f; /* Dark gray background */
            color: white; /* Text color */
            margin-bottom: 3px;
            flex: 1;
        }

        .win {
            background-color: #428D4D !important; /* Green */
            border-top: 2px solid #357A3F;
        }

        .loss {
            background-color: #8D4242 !important; /* Red */
            border-top: 2px solid #843434;
        }

        .win-text {
            color: #4CAF50 !important;
        }

        .loss-text {
            color: #f44336 !important;
        }

        .team-name {
            margin: auto 0;
            flex: 1;
            justify-content: flex-end;
        }

        .center {
            align-items: center;
            text-align: center;
        }

        .right {
            align-items: left;
            text-align: left;
        }

        .left {
            align-items: right;
            text-align: right;
        }

        .series-score {
            align-items: center;
            display: flex;
            min-width: 125px;
        }

        .dropdown-button {
            background: none;
            border: none;
            cursor: pointer;
            color: white; /* Button arrow color */
            margin: 0 auto; /* Center the button */
            width: 75px;
            min-height: 75px;
        }

        .button-text {
            padding: auto 0; /* Center this vertically and horizontally in button */
        }

        .dropdown-icon {
            font-size: 1.2em;
            position: absolute;
            transform: translate(-50%, -5%);
        }

        .map-score-bar {
            width: 100%;
            text-align: center;
            margin-bottom: 10px;
            align-items: center;
        }

        .map-score-item {
            border-top: 1px solid #666;
            min-height: 75px;
            display: flex;
            justify-content: space-between;
            background-color: #394046;
            color: white;
            margin: 3px 0;
            box-shadow: 0 1px 3px -1px rgba(0, 0, 0, 0.5);
        }

        .map-info {
            width: 75px;
            min-height: 75px;
            background-color: #4B5158;
            font-size: 12px;
            align-items: center;
            margin: 0 auto;
        }

        .map-name {
            color: white;
            padding: 12.5px;
        }

        .mod-expanded {
            display: block;
        }

        .mod-collapsed {
            display: none;
        }

        .map-comp {
            margin: auto 0;
            justify-content: flex-end;
            flex: 1;
        }

        .map-comp-group {
            width: fit-content;
            display: inline;
        }

        .map-comp img {
            width: 100%;
            max-width: 40px;
            height: auto;
            background-color: #666;
            border: 1px solid #999;
            border-radius: 5px;
        }

        .info-space {
            min-width: 125px;
            margin: 0 auto; /* Center the button */
        }

        #league-name img {
            width: 100%;
            max-width: 55px;
            height: auto;
            margin-right: 10px;
        }

        .match {
            align-items: center;
        }

        #delete-button {
            text-align: center;
            line-height: 20px;
            width: 100%;
            max-width: 75px;
            max-height: 35px;
            margin-bottom: 5px;
        }

        #edit-button {
            text-align: center;
            line-height: 20px;
            width: 100%;
            max-width: 75px;
            max-height: 35px;
        }

        .button-group {
            width: 50%;
            text-align: center;
            max-width: 75px;
            flex: 1;
            margin-right: 1px;
        }

        .space {
            width: 73px;
        }

        #match-date {
            flex: 1;
        }

        .right-group {
            display: flex;
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

    <!-- Add a title like Match Results here on the page -->

    <div class="container">
        <div class="col-12">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php 
                        if ($current_page <= 1) {
                            echo "disabled";
                        }
                     ?>">
                        <a class="page-link" href="<?php 
                            $_GET['page'] = 1;
                            echo $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET);
                         ?>">First</a>
                    </li>
                    <li class="page-item <?php 
                        if ($current_page <= 1) {
                            echo "disabled";
                        }
                     ?>">
                        <a class="page-link" href="<?php 
                            $_GET['page'] = $current_page - 1;
                            echo $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET);
                         ?>">&laquo;</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="">
                            <?php echo $current_page; ?>
                        </a>
                    </li>
                    <li class="page-item <?php 
                        if ($current_page >= $last_page) {
                            echo "disabled";
                        }
                     ?>">
                        <a class="page-link" href="<?php 
                            $_GET['page'] = $current_page + 1;
                            echo $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET);
                         ?>">&raquo;</a>
                    </li>
                    <li class="page-item <?php 
                        if ($current_page >= $last_page) {
                            echo "disabled";
                        }
                     ?>">
                        <a class="page-link" href="<?php 
                            $_GET['page'] = $last_page;
                            echo $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET);
                         ?>">Last</a>
                    </li>
                </ul>
            </nav>
        </div> <!-- .col -->

        <div class="col-12 mb-5">

            Showing 
            <?php echo $start_index + 1; ?>
            -
            <?php echo $start_index + $results->num_rows; ?>
            of 
            <?php echo $total_results; ?>
            result(s).

        </div> <!-- .col -->

        <?php while( $row = $results->fetch_assoc() ): ?>
            <div class="match">
                <div class="match-bar">
                    <span id="league-name" class="team-name center">
                        <?php
                            while( $league_row = $results_leagues->fetch_assoc() ) {
                                if( $league_row['league_id'] == $row['league_id'] ) {
                                    $league = $league_row['league_name'];
                                }
                            }

                            $results_leagues->data_seek(0);
                        ?>
                        <img src="images/<?php echo $league; ?>.png">
                        <strong><?php echo $league; ?></strong>
                    </span>
                    <span id="usc-name" class="team-name left">USC Cardinal</span>

                    <div class="series-score">
                        <?php
                            $seriesScore = $row['series_score'];

                            // Explode the string into an array based on the dash
                            $scoreArray = explode('-', $seriesScore);

                            if ($scoreArray[0] > $scoreArray[1]) {
                                $classToAdd = 'win';
                            } else if ($scoreArray[0] < $scoreArray[1]) {
                                $classToAdd = 'loss';
                            }
                        ?>
                        <button class="dropdown-button <?php echo $classToAdd; ?>" onclick="toggleMapScores(this)">
                            <span class="button-text"><strong><?php echo $row['series_score']; ?></strong></span>
                            <br>
                            <span class="dropdown-icon">&#9662;</span>
                        </button>
                    </div>

                    <span id="opponent-name" class="team-name right"><?php echo $row['opponent_name']; ?></span>

                    <div class="right-group team-name center">
                        <span id="match-date"><?php echo $row['match_date']; ?></span>
                        
                        <div class="button-group">
                            <a href="delete.php?match_id=<?php echo $row['match_id'] ?>" 
                                class="btn btn-outline-danger"
                                id="delete-button"
                                onclick="return confirm('Are you sure you want to delete this match?')"
                                >
                                Delete
                            </a>
                            <a href="edit_form.php?match_id=<?php echo $row['match_id'] ?>" 
                                class="btn btn-outline-primary"
                                id="edit-button"
                                onclick="return confirm('Are you sure you want to edit this match?')"
                                >
                                Edit
                            </a>
                        </div>
                    </div>    
                </div>

                <div id="map-scores" class="map-score-bar mod-collapsed">
                    <div class="map-score-item">
                        <div class="map-comp left">
                            <?php
                                $compString = $row['usc_comp_one'];

                                $agentsArray = explode(',', $compString);

                                $agentsArray = array_map('trim', $agentsArray);

                                $agentsArray = array_map('ucfirst', $agentsArray);

                                sort($agentsArray);
                            ?>

                            <div id="duelists" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $duelists) ) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="initiators" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $initiators)) : ?>
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="controllers" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $controllers)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="sentinels" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $sentinels)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="info-space">
                            <div class="map-info">
                                <?php
                                    while( $map_row = $results_maps->fetch_assoc() ) {
                                        if( $map_row['map_id'] == $row['map_one_id'] ) {
                                            $map_one = $map_row['map_name'];
                                        }
                                    }

                                    $results_maps->data_seek(0);

                                    $matchScore = $row['map_one_score'];

                                    // Explode the string into an array based on the dash
                                    $scoreArray = explode('-', $matchScore);

                                    if ($scoreArray[0] > $scoreArray[1]) {
                                        $classToAdd = 'win-text';
                                    } else if ($scoreArray[0] < $scoreArray[1]) {
                                        $classToAdd = 'loss-text';
                                    }
                                ?>

                                <div class="map-name <?php echo $classToAdd; ?>"><?php echo $map_one; ?></div>
                                <div class="map-score"><?php echo $row['map_one_score']; ?></div>
                            </div>
                        </div>

                        <div class="map-comp right">
                            <?php
                                $compString = $row['opponent_comp_one'];

                                $agentsArray = explode(',', $compString);

                                $agentsArray = array_map('trim', $agentsArray);

                                $agentsArray = array_map('ucfirst', $agentsArray);

                                sort($agentsArray);
                            ?>

                            <div id="duelists" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $duelists)) : ?>
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="initiators" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $initiators)) : ?>
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="controllers" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $controllers)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="sentinels" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $sentinels)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                <?php if( $row['map_two_id'] !== null) : ?>

                    <div class="map-score-item">
                        <div class="map-comp left">
                            <?php
                                $compString = $row['usc_comp_two'];

                                $agentsArray = explode(',', $compString);

                                $agentsArray = array_map('trim', $agentsArray);

                                $agentsArray = array_map('ucfirst', $agentsArray);

                                sort($agentsArray);
                            ?>

                            <div id="duelists" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $duelists)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="initiators" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $initiators)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="controllers" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $controllers)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="sentinels" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $sentinels)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="info-space">
                            <div class="map-info">

                                <?php
                                    while( $map_row = $results_maps->fetch_assoc() ) {
                                        if( $map_row['map_id'] == $row['map_two_id'] ) {
                                            $map_two = $map_row['map_name'];
                                        }
                                    }

                                    $results_maps->data_seek(0);

                                    $matchScore = $row['map_two_score'];

                                    // Explode the string into an array based on the dash
                                    $scoreArray = explode('-', $matchScore);

                                    if ($scoreArray[0] > $scoreArray[1]) {
                                        $classToAdd = 'win-text';
                                    } else if ($scoreArray[0] < $scoreArray[1]) {
                                        $classToAdd = 'loss-text';
                                    }
                                ?>

                                <div class="map-name <?php echo $classToAdd; ?>"><?php echo $map_two; ?></div>
                                <div class="map-score"><?php echo $row['map_two_score']; ?></div>
                            </div>
                        </div>

                        <div class="map-comp right">
                            <?php
                                $compString = $row['opponent_comp_two'];

                                $agentsArray = explode(',', $compString);

                                $agentsArray = array_map('trim', $agentsArray);

                                $agentsArray = array_map('ucfirst', $agentsArray);

                                sort($agentsArray);
                            ?>

                            <div id="duelists" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $duelists)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="initiators" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $initiators)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="controllers" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $controllers)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="sentinels" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $sentinels)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>

                <?php if( $row['map_three_id'] !== null) : ?>
                    
                    <div class="map-score-item">
                        <div class="map-comp left">
                            <?php
                                $compString = $row['usc_comp_three'];

                                $agentsArray = explode(',', $compString);

                                $agentsArray = array_map('trim', $agentsArray);

                                $agentsArray = array_map('ucfirst', $agentsArray);

                                sort($agentsArray);
                            ?>

                            <div id="duelists" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $duelists)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="initiators" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $initiators)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="controllers" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $controllers)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="sentinels" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $sentinels)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="info-space">
                            <div class="map-info">

                                <?php
                                    while( $map_row = $results_maps->fetch_assoc() ) {
                                        if( $map_row['map_id'] == $row['map_three_id'] ) {
                                            $map_three = $map_row['map_name'];
                                        }
                                    }

                                    $results_maps->data_seek(0);

                                    $matchScore = $row['map_three_score'];

                                    // Explode the string into an array based on the dash
                                    $scoreArray = explode('-', $matchScore);

                                    if ($scoreArray[0] > $scoreArray[1]) {
                                        $classToAdd = 'win-text';
                                    } else if ($scoreArray[0] < $scoreArray[1]) {
                                        $classToAdd = 'loss-text';
                                    }
                                ?>

                                <div class="map-name <?php echo $classToAdd; ?>"><?php echo $map_three; ?></div>
                                <div class="map-score"><?php echo $row['map_three_score']; ?></div>
                            </div>
                        </div>

                        <div class="map-comp right">
                            <?php
                                $compString = $row['opponent_comp_three'];

                                $agentsArray = explode(',', $compString);

                                $agentsArray = array_map('trim', $agentsArray);

                                $agentsArray = array_map('ucfirst', $agentsArray);

                                sort($agentsArray);
                            ?>

                            <div id="duelists" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $duelists)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="initiators" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $initiators)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="controllers" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $controllers)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div id="sentinels" class="map-comp-group">
                                <?php foreach( $agentsArray as $agent ) : ?>
                                    <?php if( in_array($agent, $sentinels)) : ?> 
                                        <img src="images/<?php echo $agent; ?>.png" title="<?php echo $agent; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>   
                </div>
            </div>

        <?php endwhile; ?>

        <div class="col-12 mt-5">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php 
                        if ($current_page <= 1) {
                            echo "disabled";
                        }
                     ?>">
                        <a class="page-link" href="<?php 
                            $_GET['page'] = 1;
                            echo $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET);
                         ?>">First</a>
                    </li>
                    <li class="page-item <?php 
                        if ($current_page <= 1) {
                            echo "disabled";
                        }
                     ?>">
                        <a class="page-link" href="<?php 
                            $_GET['page'] = $current_page - 1;
                            echo $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET);
                         ?>">&laquo;</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="">
                            <?php echo $current_page; ?>
                        </a>
                    </li>
                    <li class="page-item <?php 
                        if ($current_page >= $last_page) {
                            echo "disabled";
                        }
                     ?>">
                        <a class="page-link" href="<?php 
                            $_GET['page'] = $current_page + 1;
                            echo $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET);
                         ?>">&raquo;</a>
                    </li>
                    <li class="page-item <?php 
                        if ($current_page >= $last_page) {
                            echo "disabled";
                        }
                     ?>">
                        <a class="page-link" href="<?php 
                            $_GET['page'] = $last_page;
                            echo $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET);
                         ?>">Last</a>
                    </li>
                </ul>
            </nav>
        </div> <!-- .col -->   
    </div>

    <footer>
        <p>&copy; 2023 USC Valorant</p>
    </footer>

    <!-- Include Bootstrap JS (optional, only if you need Bootstrap JavaScript features) -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        function toggleMapScores(button) {
            var matchContainer = button.closest('.match');
            var mapScoresDiv = matchContainer.querySelector('.map-score-bar');

            var dropdownButton = button
            var dropdownIcon = button.querySelector('.dropdown-icon');

            if(mapScoresDiv.classList.contains('mod-expanded')) {
                mapScoresDiv.classList.add('mod-collapsed');
                dropdownIcon.innerHTML = "&#9662";
            }
            else {
                mapScoresDiv.classList.remove('mod-collapsed');
                dropdownIcon.innerHTML = "&#9652";
                
            }
            mapScoresDiv.classList.toggle('mod-expanded');
        }
    </script>
</body>
</html>
