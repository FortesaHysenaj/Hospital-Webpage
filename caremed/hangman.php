<?php 

	session_start(); 
	
	$words = array("shkolla","fakullteti","librat","lapsi");
	
	$word = (!isset($_SESSION['word'])) ? $words[rand(0, count($words))] : $_SESSION['word'];
	$hangman = (!isset($_SESSION['hangman'])) ? 0 : $_SESSION['hangman'];
	$guessLength = (!isset($_SESSION['guessLength'])) ? 0 : $_SESSION['guessLength'];
	$alreadyGuessed = (!isset($_SESSION['alreadyGuessed'])) ? array() : $_SESSION['alreadyGuessed'];
	$message="you lost";
    $isGuessed="";
	$hangmanImage = array('&nbsp;&nbsp;O<br />', '--', '|', '--<br />', '&nbsp;&nbsp;/', '\\<br />');
	
 	if (!isset($_SESSION['display'])){
		$display = array();
		
		while (count($display) < strlen($word)) {
			$display[] = '_';
		}
	} else { 
		$display = $_SESSION['display'];
	}

	if ($guessLength < strlen($word) && $hangman < 6) { 
		$message = "The game's not over yet...";
		$match = false;
		$guess = isset($_POST['guess']) ? $_POST['guess'][0] : '';

		$positions = array();
		
		if (in_array(strtolower($guess), $alreadyGuessed)) {
			$message = "You already guessed '" . $guess . "'!";
			$isGuessed = true;
		}
		
		if (!$isGuessed && $guess != null) {
			$alreadyGuessed[] = strtolower($guess);
		
			for ($j = 0; $j < strlen($word); $j++) { 
				if (strcasecmp(substr($word, $j, 1), $guess) == 0) { 
					$match = true; 
					$positions[] = $j; 
				}
			}
			if ($match == true) { 
				foreach ($positions as $k => $position) { 
					if (strcmp($display[$position], '_') == 0) { 
						$display[$position] = $guess;
						$guessLength++;	
						if ($guessLength == strlen($word)) {
							$message = "Game over, you win!";
						}
					}
				}
			} else {
				$hangman++; 
				if ($hangman == 6) {
					$message = "You lost, game over.";
				}
			}
		}
	}
	
	$_SESSION['word'] = $word;
	$_SESSION['display'] = $display;
	$_SESSION['hangman'] = $hangman;
	$_SESSION['guessLength'] = $guessLength;
	$_SESSION['alreadyGuessed'] = $alreadyGuessed;

	echo $hangman . ' bad guesses.<br />';

	foreach ($display as $l => $letter) {
		echo $letter . ' ';
	}

	echo '<br />' . $message . '<br />'; 
	echo 'You have already guessed: ' . implode(', ', $alreadyGuessed);
	if (isset($_POST["restart"]))
{
	if ($_POST['restart'] === "New Game") {
		session_destroy();	
		header("Location: hangman.php");
	}}
?>

<form method="post">
	Pick a letter... <input type="text" name="guess" maxlength="1" />
	<input type="submit" value="Guess!" />
	<input type="submit" value="New Game" name="restart" />
</form>
<?php
for ($i = 0; $i < $hangman; $i++) {
	echo $hangmanImage[$i];
}
?>