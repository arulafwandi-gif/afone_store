<?php
require_once 'includes/helpers.php';

$q = trim($_GET['q'] ?? '');

$games = get_games(true);

// Kalau belum mengetik
if ($q == '') {

    echo "<h6 class='mb-3'>🔥 GAME POPULER</h6>";

    foreach ($games as $game) {

        echo '
        <a href="game.php?slug='.$game['slug'].'" class="search-item">

            <img src="'.image_src($game['image_url']).'">

            <div>
                <strong>'.$game['name'].'</strong><br>
                <small>'.category_label($game['category']).'</small>
            </div>

        </a>';
    }

    exit;
}
$q = strtolower($q);

foreach ($games as $game){

    if(
        stripos($game['name'],$q)!==false ||
        stripos($game['publisher'],$q)!==false
    ){

        echo '
        <a href="game.php?slug='.$game['slug'].'" class="search-item">

            <img src="'.image_src($game['image_url']).'">

            <div>
                <strong>'.$game['name'].'</strong><br>
                <small>'.category_label($game['category']).'</small>
            </div>

        </a>';
    }
}
