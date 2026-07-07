<?php
require_once 'includes/helpers.php';

$q = strtolower(trim($_GET['q'] ?? ''));

$games = get_games(true);

foreach($games as $game){

    if(stripos($game['name'],$q)!==false){

        echo '
        <a class="search-item" href="game.php?slug='.$game['slug'].'">

            <img src="'.image_src($game['image_url']).'">

            <span>'.$game['name'].'</span>

        </a>
        ';
    }

}