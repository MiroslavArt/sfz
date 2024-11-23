<?php
function climbingLeaderboard($ranked, $player) {
    // Write your code here

    $optranks = array_unique($ranked);
    //rsort($optranks);

    print_r($optranks);

    function calcrank($ranks, $val) {
        //$ranks = array_unique($ranks);
        rsort($ranks);
        $rank = current(array_keys($ranks,$val))+1;
        return $rank;
    }
    //print_r($ranked);
    //print_r($player);

    $allranks = [];

    foreach($player as $value) {
        if(!in_array($value,$optranks)) {
            $optranks[] = $value;
        }

        //print_r($ranked);
        //print_r(calcrank($ranked, $value));
        $allranks[] = calcrank($optranks, $value);

    }



    //$total = array_merge($ranked, $player);
    //$total = array_unique($total);
    //sort($total);
    //print_r($total);
    //$ranks = array_keys($total, $player);
    //print_r($ranks);
    /*$newranks = [];
    $drank = count($total);
    foreach($total as $key => $value) {
        $drank--;
        if(in_array($value, $player)) {
            $newranks[] = $drank;
        }
    }*/
    print_r($allranks);
    return $allranks;


}


