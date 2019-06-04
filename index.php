<?php 
require 'include/sources.php';
require 'include/config.php';

// Insert all images to database, since all images are named after their number.
// for ($x = 1; $x < 87; $x++) {
//     $mysqli->query("INSERT INTO girls (name, score) " . "VALUES ('$x', '1500')");
// } 

if (isset($_POST['CHOSEN'])) { 

	// Calculate ranks. 
	$girl_one = $mysqli->escape_string($_POST['girl_one']);
	$girl_two = $mysqli->escape_string($_POST['girl_two']);
	$girl_one_score = $mysqli->escape_string($_POST['girl_one_score']);
	$girl_two_score = $mysqli->escape_string($_POST['girl_two_score']);
	$winner = $mysqli->escape_string($_POST['winner']);

	
	if($winner == 'girl_one'){
		$girl_scores = eloRating($girl_one_score, $girl_two_score, 'yes');
	}else{
		$girl_scores = eloRating($girl_one_score, $girl_two_score, 'no');
	}

	// Update database
	$mysqli->query("UPDATE girls SET score='$girl_one_new_score' WHERE name='$girl_scores[0]'");  
	$mysqli->query("UPDATE girls SET score='$girl_two_new_score' WHERE name='$girl_scores[1]'");  
}

function eloRating($player_A, $player_B, $won){
	$K = 25;
	
	$expA = 1/(1 + (pow(10,($player_B - $player_A)/400)));
	$expB = 1/(1 + (pow(10,($player_A - $player_B)/400)));

	$new_ratings = array(0, 0);

	//Player A wins/Girl 1, Player B loses
	if($won == 'yes'){
		$new_ratings[0] = $player_A + ($K * (2 - $expA)); // Girl 1
		$new_ratings[1] = $player_B - ($K * (2 - $expB)); // Girl 2
	}
	else{
		$new_ratings[0] = $player_A - ($K * (2 - $expA));
		$new_ratings[1] = $player_B + ($K * (2 - $expB));
	}
	//return new rating
	return $new_ratings;
}

?>
<!DOCTYPE html>
<html>
<head>
	<title> FACEMASH by unobatbayar </title>
	<center><div class="uk-container"></div><p uk-margin><button class="uk-button uk-button-primary uk-button-large" uk-toggle="target: .toggle">Facemash</button><p class="toggle" hidden>If you have me, you want to share me. If you share me, you haven't got me. Who am I? </p></p>
	<h2><span class="uk-text-muted">Were we let in for our looks? No. Will we be judged on them? Yes.</span></h2></div>
	</center>
</head>	
<body>

<!-- MAIN BODY -->
<center><div class="uk-container uk-container-expand">

	<ul class="uk-subnav uk-subnav-pill" uk-switcher>
		<li><a href="#">Start <span uk-icon="play"></span></a></li>
		<li><a href="#">Leaderboards <span uk-icon="menu"></span></a></li>
	</ul>

<ul class="uk-switcher uk-margin">

	<!-- Main -->
    <li>
		<h2 class="uk-heading-medium">Who's hotter? Click to Choose.</h2>
		<?php

		//Generate our opponents. Limit to 5 since we only have 5 images as example.
		$distinct = True;
		while($distinct){
			$girl_one = rand(1, 5);
			$girl_two = rand(1, 5);
			if($girl_one != $girl_two){
				$distinct = False;
			}
		}
		$query_one = $mysqli->query("SELECT score FROM girls WHERE name='$girl_one' LIMIT 1");
		$query_two = $mysqli->query("SELECT score FROM girls WHERE name='$girl_two' LIMIT 1");
		if ($query_one->num_rows > 0 ) {
			$fetch_one = $query_one->fetch_assoc();
			$girl_one_score = $fetch_one['score'];
		}
		if ($query_two->num_rows > 0 ) {
			$fetch_two = $query_two->fetch_assoc();
			$girl_two_score = $fetch_two['score'];
		}

		echo '<table class="uk-table uk-text-center">
		<td width="50%" valign="left">';
		// Girl one
		echo '<form class="uk-form" action="index.php" method="POST" >';
		echo '<input type="hidden" name="girl_one" value="'.$girl_one.'">';
		echo '<input type="hidden" name="girl_two" value="'.$girl_two.'">';
		echo '<input type="hidden" name="girl_one_score" value="'.$girl_one_score.'">';
		echo '<input type="hidden" name="girl_two_score" value="'.$girl_two_score.'">';
		echo '<input type="hidden" name="winner" value="girl_one">';
		echo '<button class="uk-button uk-button-default" name="CHOSEN" type="SUBMIT"><img class="uk-border-square" width="400" height="200" src="images/'.$girl_one.'.jpg"></button>'; 
		echo '</form>';
		echo '<span class="uk-label">'.$girl_one_score.'</span>'; 
		echo '</td>';
		
		echo '<td width="50%" valign="center">';
		// Girl two
		echo '<form class="uk-form" action="index.php" method="POST" >';
		echo '<input type="hidden" name="girl_one" value="'.$girl_one.'">';
		echo '<input type="hidden" name="girl_two" value="'.$girl_two.'">';
		echo '<input type="hidden" name="girl_one_score" value="'.$girl_one_score.'">';
		echo '<input type="hidden" name="girl_two_score" value="'.$girl_two_score.'">';
		echo '<input type="hidden" name="winner" value="girl_two">';
		echo '<button class="uk-button uk-button-default" name="CHOSEN" type="SUBMIT"><img class="uk-border-square" width="400" height="200" src="images/'.$girl_two.'.jpg"></button>'; 
		echo '</form>';
		echo '<span class="uk-label">'.$girl_two_score.'</span>'; 
		echo'</td></table>';
		?>
	

	</li>
	<!-- Leaderboards -->
    <li>
	<table class="uk-table uk-table-divider uk-table-small uk-table-striped">
    <h2 class="uk-text-center"> <span class="uk-label uk-label-success ">Top 10</span> </h2>
    <thead>
        <tr>
			<th>Rank</th>
            <th>Girl</th>
            <th>Rating</th>
        </tr>
    </thead>

    <tbody>

	<?php
        $i = 1;
        $data = $mysqli->query("SELECT * FROM girls ORDER BY score DESC LIMIT 10");
        while($row = $data->fetch_assoc()) {
			echo '<tr><td>'.$i.'</td>';
			echo '<td class="uk-text-capitalize"><img class="uk-border-square" width="100" height="100" src="images/'.$row['name'].'.jpg"></td>';
			echo '<td class="uk-text-capitalize">'.htmlspecialchars($row['score'], ENT_QUOTES, 'UTF-8').'</td><tr>';

        $i++;
        }
        ?>
		</tbody>
	</table>
	</li>
</ul>
	</div></center>

	<p class="uk-position-bottom uk-text-center uk-text-emphasis"> unobatbayar &copy; <?php echo date("Y");?> </p>
</body>
</html>


