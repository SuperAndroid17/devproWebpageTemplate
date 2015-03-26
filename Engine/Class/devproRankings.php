<?php
/*
 * DevPro Rankings Class
 * Copyright 2015 @ Benjamin Knecht
 * 
 * This Class graps Rankings Requests and return the Info
 * 
 */

 class devproRankings extends devpro
{
     
 
/*
 * @return array
 */
public function getRankings($array, $number) {
    
    $query = ("SELECT * FROM rankings ORDER BY SingleElo DESC LIMIT " . $number);
    
    $db_result = $this->queryBuilder($array, $query);
    
    return $db_result;
    
} 

/*
 * @return array
 */
public function getMatchRankings($array, $number) {
    
    $query = ("SELECT * FROM rankings ORDER BY Elo DESC LIMIT " . $number);
    
    $db_result = $this->queryBuilder($array, $query);
    
    return $db_result;
    
}  
 
}