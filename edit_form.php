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

        // Maps:
        $sql_maps = "SELECT * FROM maps;";
        $results_maps = $mysqli->query($sql_maps);
        if ( $results_maps == false ) {
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

        $match_id = $_GET['match_id'];

        $sql = "SELECT * FROM matches WHERE match_id = $match_id";

        $result = $mysqli->query($sql);

        if(!$result) {
            echo $mysqli->error;
            $mysqli->close();
            exit();
        }

        $match = $result->fetch_assoc();

        // Close DB Connection
        $mysqli->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="This page is where the user edits a match after pressing the edit button of a match in the match results page. The form works exactly the same way as the add form but will edit instead.">
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
        }

        .container {
            align-items: center;
            border-radius: 8px;
            text-align: center;
        }

        input {
            text-align: center;
            max-width: 500px;
            margin: 0 auto;
        }

        select {
            text-align: center;
            max-width: 500px;
            margin: 0 auto;
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
        <h2 class="mb-4">Editing Match Result</h2>

        <?php if (isset($error) && !empty($error)) : ?>
                
            <div class="col-12 text-danger">
                <!-- Show Error Messages Here. -->
                <?php echo $error ?>
            </div>

        <?php else : ?>

            <form action="edit_confirmation.php" method="POST" onsubmit="return validateForm()">

                <input type="hidden" name="match_id" value="<?php echo $_GET['match_id'] ?>">

                <!-- League Name -->
                <div class="form-group">
                    <label for="league-select">League:</label>
                    <select name="league_id" class="form-control" id="league-select" required>
                        <option value="" selected disabled>-- Choose the league played for --</option>

                        <?php while( $row = $results_leagues->fetch_assoc() ): ?>

                            <?php if( $row['league_id'] == $match['league_id']) : ?>

                                <option selected value="<?php echo $row['league_id']; ?>">
                                    <?php echo $row['league_name']; ?>
                                </option>
                            <?php else : ?>

                                <option value="<?php echo $row['league_id']; ?>">
                                    <?php echo $row['league_name']; ?>
                                </option>

                            <?php endif; ?>
                        <?php endwhile; ?>

                    </select>
                </div>
                <!-- Opponent Team Name -->
                <div class="form-group">
                    <label for="opponent-team">Opponent Team Name:</label>
                    <input type="text" class="form-control" id="opponent-team" placeholder="Enter opponent team name" name="opponent_team" value="<?php echo $match['opponent_name']; ?>"required>
                </div>

                <!-- Series Score -->
                <div class="form-group">
                    <label for="series-score">Series Score:</label>
                    <input type="text" class="form-control" id="series-score" placeholder="Enter series score (e.g., 2-1)" name="series_score" value="<?php echo $match['series_score']; ?>" required>
                </div>

                <!-- Maps Played -->
                <div class="form-group">
                    <label for="map-one-select" class="mt-2">Map 1:</label>
                    <select name="map_one_id" class="form-control" id="map-one-select" required>
                        <option value="" selected disabled>-- Choose map 1 --</option>
                            
                        <?php while( $row = $results_maps->fetch_assoc() ): ?>

                            <?php if( $row['map_id'] == $match['map_one_id']) : ?>

                                <option selected value="<?php echo $row['map_id']; ?>">
                                    <?php echo $row['map_name']; ?>
                                </option>

                            <?php else : ?>

                                <option value="<?php echo $row['map_id']; ?>">
                                    <?php echo $row['map_name']; ?>
                                </option>

                            <?php endif; ?>

                        <?php endwhile; ?>

                    </select>

                    <?php
                    // Reset the internal pointer of the result set
                    $results_maps->data_seek(0);
                    ?>

                    <label for="map-two-select" class="mt-2">Map 2:</label>
                    <select name="map_two_id" class="form-control" id="map-two-select">
                        <option value="">-- Choose map 2 (if played) --</option>
                        
                        <?php while( $row = $results_maps->fetch_assoc() ): ?>

                            <?php if( $row['map_id'] == $match['map_two_id']) : ?>

                                <option selected value="<?php echo $row['map_id']; ?>">
                                    <?php echo $row['map_name']; ?>
                                </option>

                            <?php else : ?>

                                <option value="<?php echo $row['map_id']; ?>">
                                    <?php echo $row['map_name']; ?>
                                </option>

                            <?php endif; ?>

                        <?php endwhile; ?>

                    </select>

                    <?php
                    // Reset the internal pointer of the result set
                    $results_maps->data_seek(0);
                    ?>

                    <label for="map-three-select" class="mt-2">Map 3:</label>
                    <select name="map_three_id" class="form-control" id="map-three-select">
                        <option value="">-- Choose map 3 (if played) --</option>
                        
                        <?php while( $row = $results_maps->fetch_assoc() ): ?>

                            <?php if( $row['map_id'] == $match['map_three_id']) : ?>

                                <option selected value="<?php echo $row['map_id']; ?>">
                                    <?php echo $row['map_name']; ?>
                                </option>

                            <?php else : ?>

                                <option value="<?php echo $row['map_id']; ?>">
                                    <?php echo $row['map_name']; ?>
                                </option>

                            <?php endif; ?>

                        <?php endwhile; ?>

                    </select>
                </div>

                <!-- Scores for Each Map Played -->
                <div class="form-group">
                    <label for="map-one-score" class="mt-2">Score for Map 1:</label>
                    <input type="text" class="form-control" id="map-one-score" placeholder="Enter score for map 1, USC score first (e.g., 13-10)" name="map_one_score" value="<?php echo $match['map_one_score']; ?>" required>

                    <label for="map-two-score" class="mt-2">Score for Map 2:</label>
                    <input type="text" class="form-control" id="map-two-score" placeholder="Enter score for map 2, if played, USC score first (e.g., 13-10)" name="map_two_score" value="<?php echo $match['map_two_score']; ?>">

                    <label for="map-three-score" class="mt-2">Score for Map 3:</label>
                    <input type="text" class="form-control" id="map-three-score" placeholder="Enter score for map 3, if played, USC score first (e.g., 13-10)" name="map_three_score" value="<?php echo $match['map_three_score']; ?>">
                </div>

                <!-- Agents Played on Each Map -->
                <div class="form-group">
                    <label for="opponent-comp-one" class="mt-2">Map 1 Opponent Comp:</label>
                    <input type="text" class="form-control" id="opponent-comp-one" placeholder="(e.g., Jett, Breach, Sova, Omen, Killjoy)" name="opponent_comp_one" value="<?php echo $match['opponent_comp_one']; ?>"required>

                    <label for="usc-comp-one" class="mt-2">Map 1 USC Comp:</label>
                    <input type="text" class="form-control" id="usc-comp-one" placeholder="(e.g., Jett, Breach, Sova, Omen, Killjoy)" name="usc_comp_one" value="<?php echo $match['usc_comp_one']; ?>" required>

                    <label for="opponent-comp-two" class="mt-2">Map 2 Opponent Comp:</label>
                    <input type="text" class="form-control" id="opponent-comp-two" placeholder="(e.g., Jett, Breach, Sova, Omen, Killjoy)" name="opponent_comp_two" value="<?php echo $match['opponent_comp_two']; ?>">

                    <label for="usc-comp-two" class="mt-2">Map 2 USC Comp:</label>
                    <input type="text" class="form-control" id="usc-comp-two" placeholder="(e.g., Jett, Breach, Sova, Omen, Killjoy)" name="usc_comp_two" value="<?php echo $match['usc_comp_two']; ?>">

                    <label for="opponent-comp-three" class="mt-2">Map 3 Opponent Comp:</label>
                    <input type="text" class="form-control" id="opponent-comp-three" placeholder="(e.g., Jett, Breach, Sova, Omen, Killjoy)" name="opponent_comp_three" value="<?php echo $match['opponent_comp_three']; ?>">

                    <label for="usc-comp-three" class="mt-2">Map 3 USC Comp:</label>
                    <input type="text" class="form-control" id="usc-comp-three" placeholder="(e.g., Jett, Breach, Sova, Omen, Killjoy)" name="usc_comp_three" value="<?php echo $match['usc_comp_three']; ?>">
                </div>

                <div class="form-group">
                    <label for="date-played" class="mt-2">Match Date:</label>
                    <input type="date" id="date-played" name="match_date" value="<?php echo $match['match_date']; ?>" min="2023-08-21" max="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <!-- Error Message -->
                <div id="error-message" class="mb-2"></div>

                <!-- Submit Button -->
                <button type="submit" class="mt-5 btn btn-primary">Submit</button>

            </form>
        <?php endif; ?>

    </div>
    
    <footer>
        <p>&copy; 2023 USC Trojan Esports (have other links here for things like Discord servers and social media)</p>
    </footer>

    <!-- Include Bootstrap JS (optional, only if you need Bootstrap JavaScript features) -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        const errorMessage = document.getElementById('error-message');
        const agentNames = ['astra', 'breach', 'brimstone', 'chamber', 'cypher', 
                            'deadlock', 'fade', 'gekko', 'harbor', 'iso',
                            'jett', 'kayo', 'killjoy', 'neon', 'omen', 
                            'phoenix', 'raze', 'reyna', 'sage', 'skye',
                            'sova', 'viper', 'yoru'];

        function validateForm() 
        {
            var opponentTeam = document.getElementById('opponent-team').value.trim();
            var seriesScore = document.getElementById('series-score').value.trim();

            var mapOne = document.getElementById('map-one-select').value;
            var mapTwo = document.getElementById('map-two-select').value;
            var mapThree = document.getElementById('map-three-select').value;

            var mapOneScore = document.getElementById('map-one-score').value.trim();
            var mapTwoScore = document.getElementById('map-two-score').value.trim();
            var mapThreeScore = document.getElementById('map-three-score').value.trim();

            var opponentCompOne = document.getElementById('opponent-comp-one').value.trim();
            var opponentCompTwo = document.getElementById('opponent-comp-two').value.trim();
            var opponentCompThree = document.getElementById('opponent-comp-three').value.trim();

            var uscCompOne = document.getElementById('usc-comp-one').value.trim();
            var uscCompTwo = document.getElementById('usc-comp-two').value.trim();
            var uscCompThree = document.getElementById('usc-comp-three').value.trim();

            var seriesScoreRegex = /^[0-2]-[0-2]$/;
            var mapScoreRegex = /^\d+-\d+$/;
            var compRegex = /^[\w\s]+(,[\w\s]+){4}$/;

            var mapsPlayed;

            // Clear error message
            errorMessage.textContent = '';
            
            // Check opponent name and series score
            if(opponentTeam == "" || seriesScore == "")
            {
                errorMessage.textContent = "Please fill out the required fields";
                return false;
            }
            if(!seriesScoreRegex.test(seriesScore))
            {
                errorMessage.textContent = "The series score is not in the right format, make sure it adds to between 1 and 3";
                return false;
            }

            // Figuring out amount of maps played (consider clearing unneeded info?)
            if(mapOne != "" && mapTwo == "" && mapThree == "") {
                mapsPlayed = 1;

                // Clear unneeded inputs that user may have left in for maps 2 and 3
                var m2s = document.getElementById('map-two-score');
                m2s.value = "";
                var oc2 = document.getElementById('opponent-comp-two');
                oc2.value = "";
                var uc2 = document.getElementById('usc-comp-two');
                uc2.value = "";
                var m3s = document.getElementById('map-three-score');
                m2s.value = "";
                var oc3 = document.getElementById('opponent-comp-three');
                oc2.value = "";
                var uc3 = document.getElementById('usc-comp-three');
                uc2.value = "";
            }
            else if(mapOne != "" && mapTwo != "" && mapThree == "")
            {
                mapsPlayed = 2;

                // Clear unneeded inputs that user may have left in for map 3
                var m3s = document.getElementById('map-three-score');
                m2s.value = "";
                var oc3 = document.getElementById('opponent-comp-three');
                oc2.value = "";
                var uc3 = document.getElementById('usc-comp-three');
                uc2.value = "";
            }
            else if(mapOne != "" && mapTwo != "" && mapThree != "")
            {
                mapsPlayed = 3;

                // Don't need to clear anything since everything would be needed
            }
            else if(mapOne != "" && mapTwo == "" && mapThree != "")
            {
                errorMessage.textContent = "Please choose map two before choosing a map three";
                return false;
            }

            if(mapOne == mapTwo || mapOne == mapThree || mapTwo == mapThree && mapTwo != "" && mapThree != "")
            {
                errorMessage.textContent = "One or more maps are the same";
                return false;
            }

            // Check to see if the series score matches the amount of maps played
            var seriesScoreSplit = seriesScore.split('-');
            var uscSeriesScore = parseInt(seriesScoreSplit[0]);
            var opponentSeriesScore = parseInt(seriesScoreSplit[1]);

            if(uscSeriesScore + opponentSeriesScore != mapsPlayed)
            {
                errorMessage.textContent = "Make sure that the series scores add up to the number of maps played";
                return false;
            }
            if(mapsPlayed == 1 && uscSeriesScore != 1 && opponentSeriesScore != 1)
            {
                errorMessage.textContent = "Make sure one team has enough map wins to win the match";
                return false;
            }

            if((mapsPlayed == 2 || mapsPlayed == 3) && uscSeriesScore != 2 && opponentSeriesScore != 2)
            {
                errorMessage.textContent = "Make sure one team has enough map wins to win the match";
                return false;
            }

            var uscMapWinsCount = 0;
            var opponentMapWinsCount = 0;

            // Check input fields based on number of maps
            if(mapsPlayed >= 1)
            {
                if(mapOneScore == "" || opponentCompOne == "" || uscCompOne == "")
                {
                    errorMessage.textContent = "Please fill out the fields for map one";
                    return false;
                }
                if(!mapScoreRegex.test(mapOneScore))
                {
                    errorMessage.textContent = "The map 1 score is not in the right format";
                    return false;
                }
                if(!compRegex.test(opponentCompOne))
                {
                    errorMessage.textContent = "The opponent's map 1 comp is not in the right format";
                    return false;
                }
                if(!compRegex.test(uscCompOne))
                {
                    errorMessage.textContent = "USC's map 1 comp is not in the right format";
                    return false;
                }
                if(!validateAgentNames(opponentCompOne))
                {
                    errorMessage.textContent = "The opponent's map 1 comp has at least one name that does not belong to an agent or comp has a duplicate agent name";
                    return false;
                }
                if(!validateAgentNames(uscCompOne))
                {
                    errorMessage.textContent = "USC's map 1 comp has at least one name that does not belong to an agent or comp has a duplicate agent name";
                    return false;
                }
                if(!validateMapScoreNumbers(mapOneScore))
                {
                    errorMessage.textContent = "The score for map 1 is not possible, first team to 13, win by two are the rules";
                    return false;
                }

                var mapOneScoreSplit = mapOneScore.split('-');
                var uscMapOneScore = parseInt(mapOneScoreSplit[0]);
                var opponentMapOneScore = parseInt(mapOneScoreSplit[1]);
                if(uscMapOneScore > opponentMapOneScore)
                {
                    uscMapWinsCount++;
                }
                else if(opponentMapOneScore > uscMapOneScore)
                {
                    opponentMapWinsCount++;
                }
            }
            if(mapsPlayed >= 2)
            {
                if(mapTwoScore == "" || opponentCompTwo == "" || uscCompTwo == "")
                {
                    errorMessage.textContent = "Please fill out the fields for map 2";
                    return false;
                }
                if(!mapScoreRegex.test(mapTwoScore))
                {
                    errorMessage.textContent = "The map 2 score is not in the right format";
                    return false;
                }
                if(!compRegex.test(opponentCompTwo))
                {
                    errorMessage.textContent = "The opponent's map 2 comp is not in the right format";
                    return false;
                }
                if(!compRegex.test(uscCompTwo))
                {
                    errorMessage.textContent = "USC's map 2 comp is not in the right format";
                    return false;
                }
                if(!validateAgentNames(opponentCompTwo))
                {
                    errorMessage.textContent = "The opponent's map 2 comp has at least one name that does not belong to an agent or comp has a duplicate agent name";
                    return false;
                }
                if(!validateAgentNames(uscCompTwo))
                {
                    errorMessage.textContent = "USC's map 2 comp has at least one name that does not belong to an agent or comp has a duplicate agent name";
                    return false;
                }
                if(!validateMapScoreNumbers(mapTwoScore))
                {
                    errorMessage.textContent = "The score for map 2 is not possible, first team to 13, win by two are the rules";
                    return false;
                }

                var mapTwoScoreSplit = mapTwoScore.split('-');
                var uscMapTwoScore = parseInt(mapTwoScoreSplit[0]);
                var opponentMapTwoScore = parseInt(mapTwoScoreSplit[1]);
                if(uscMapTwoScore > opponentMapTwoScore)
                {
                    uscMapWinsCount++;
                }
                else if(opponentMapTwoScore > uscMapTwoScore)
                {
                    opponentMapWinsCount++;
                }
            }
            if(mapsPlayed == 3)
            {
                if(mapThreeScore == "" || opponentCompThree == "" || uscCompThree == "")
                {
                    errorMessage.textContent = "Please fill out the fields for map 3";
                    return false;
                }
                if(!mapScoreRegex.test(mapThreeScore))
                {
                    errorMessage.textContent = "The map 3 score is not in the right format";
                    return false;
                }
                if(!compRegex.test(opponentCompThree))
                {
                    errorMessage.textContent = "The opponent's map 3 comp is not in the right format";
                    return false;
                }
                if(!compRegex.test(uscCompThree))
                {
                    errorMessage.textContent = "USC's map 3 comp is not in the right format";
                    return false;
                }
                if(!validateAgentNames(opponentCompThree))
                {
                    errorMessage.textContent = "The opponent's map 3 comp has at least one name that does not belong to an agent or comp has a duplicate agent name";
                    return false;
                }
                if(!validateAgentNames(uscCompThree))
                {
                    errorMessage.textContent = "USC's map 3 comp has at least one name that does not belong to an agent or comp has a duplicate agent name";
                    return false;
                }
                if(!validateMapScoreNumbers(mapThreeScore))
                {
                    errorMessage.textContent = "The score for map 3 is not possible, first team to 13, win by two are the rules";
                    return false;
                }

                var mapThreeScoreSplit = mapThreeScore.split('-');
                var uscMapThreeScore = parseInt(mapThreeScoreSplit[0]);
                var opponentMapThreeScore = parseInt(mapThreeScoreSplit[1]);
                if(uscMapThreeScore > opponentMapThreeScore)
                {
                    uscMapWinsCount++;
                }
                else if(opponentMapThreeScore > uscMapThreeScore)
                {
                    opponentMapWinsCount++;
                }
            }

            // Check if the series score makes sense with the map scores
            if(uscMapWinsCount != uscSeriesScore || opponentMapWinsCount != opponentSeriesScore)
            {
                errorMessage.textContent = "The map scores do not match up with the series score, remember to make sure USC's score comes first (e.g., 2-1 USC won 2 maps, or 13-4 USC won 13 rounds)";
                return false;
            }

            return true;
        }

        function validateAgentNames(namesString) 
        {
            // Split the input string into an array of names
            var inputNames = namesString.split(',').map(name => name.trim().toLowerCase());

            var seenNames = {};

            // Check for duplicate names
            for(var i = 0; i < inputNames.length; i++)
            {
                var name = inputNames[i].trim();

                if(seenNames[name])
                {
                    return false;
                }

                seenNames[name] = true;
            }

            // Check if every input name is in the agentNames list
            return inputNames.every(inputName => agentNames.includes(inputName));
        }

        function validateMapScoreNumbers(mapScoresString)
        {
            var mapScoresArray = mapScoresString.split('-').map(score => parseInt(score.trim()));

            // Check for a winning round score of 13 for one team
            if(mapScoresArray.some(score => score == 13))
            {
                // Winning team has to have at least 2 more rounds than opponent
                return (
                    (mapScoresArray[1] <= mapScoresArray[0] - 2) ||
                    (mapScoresArray[0] <= mapScoresArray[1] - 2)
                );
            }
            // Check for a winning round score in overtime
            if(mapScoresArray.some(score => score > 13))
            {
                // Winning team has to have 2 more rounds than opponent to win in overtime
                return (
                    (mapScoresArray[1] == mapScoresArray[0] - 2) ||
                    (mapScoresArray[0] == mapScoresArray[1] - 2)
                );
            }
        }

    </script>
</body>
</html>