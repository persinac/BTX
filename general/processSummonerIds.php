<?php
/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 6/2/2016
 * Time: 9:11 AM
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once ("$root/mongo/CRUD/classes/LeagueCollection.php");
require_once("$root/CRUD/classes/SummonerBucket.php");
require_once("$root/CRUD/classes/RiotApi.php");
$riot = new RiotApi();
$mysql = new SummonerBucket();
$collectionToUse = "league_degree";
$mongoObj = new LeagueCollection();
$mongoObj->SelectDBToUse(MONGO_DB);
$mongoObj->SelectCollection($collectionToUse);
$max_id = $mongoObj->GetMaxBucketId();

$html = "";
$html .= "<h2>Players</h2>";
$html .= "<table><tr>";
$html .= "<th>Bucket ID</th>";
$html .= "<th>Summoner ID</th>";
$html .= "<th>Match?</th>";
$html .= "<th>Summoner Name</th>";
$html .= "<th>Created On</th>";
$html .= "</tr>";

$api_call_count = 0;

if($mysql->CheckControlTable("processSummonerIds") == 1) {
    for ($count = 1; $count <= $max_id; $count++) {
        $query = array('bucket_id' => $count);
        $cursor = $mongoObj->FindSpecific($query);
        $summonerInfo = new SummonerInfo();
//    var_dump($i);
        foreach ($cursor as $document) {
            foreach ($document["summoners"] as $summoner) {
                //$riot->insertIntoAPILog("processSummonerIds.php","Has summoner been processed? " . $summoner["has_been_processed"] . "","processSummonerIds.php -> has been processed");
                if ($summoner["has_been_processed"] == 9) {
                    $api_call_count = $api_call_count + 1;
                    $riot->insertIntoAPILog("processSummonerIds.php","API_CALL_COUNT: $api_call_count > ". MAX_BUCKET_SIZE ."","processSummonerIds.php -> cursor count");
                    if ($api_call_count > MAX_BUCKET_SIZE) {
                        $riot->insertIntoAPILog("processSummonerIds.php","PROCESS IS STILL RUNNING - SLEEPING FOR 11 SECONDS - : $api_call_count","processSummonerIds.php -> cursor count");
                        sleep(15);
                        $api_call_count = 0;
                    }
                    $html .= "<tr>";
                    $html .= "<td>" . $document["bucket_id"] . "</td>";
                    $html .= "<td>" . $summoner["s_id"] . "</td>";

                    $obj = json_decode($summonerInfo->SearchForSummonerByID($summoner["s_id"]));
                    foreach ($obj AS $j => $val) {
                        if ($j == "status") {
                            $html .= "<td>No</td>";
                            $html .= "<td></td>";
                            $mongoObj->UpdateSpecifcSummonerById(
                                $document["bucket_id"]
                                , $summoner["s_id"]
                                , "has_been_processed"
                                , 1);
                            $mongoObj->UpdateSpecifcSummonerById(
                                $document["bucket_id"]
                                , $summoner["s_id"]
                                , "is_actual_user"
                                , 0);
                            $mysql->UpdateSummonerHasBeenProcById(1, $summoner["s_id"]);
                            $mysql->UpdateSummonerIsActualUserById(0, $summoner["s_id"]);
                        } else {
                            $html .= "<td>Yes</td>";
                            $html .= "<td>" . $val->name . "</td>";
                            $mongoObj->UpdateSpecifcSummonerById(
                                $document["bucket_id"]
                                , $summoner["s_id"]
                                , "has_been_processed"
                                , 1);
                            $mongoObj->UpdateSpecifcSummonerById(
                                $document["bucket_id"]
                                , $summoner["s_id"]
                                , "is_actual_user"
                                , 1);
                            $mysql->UpdateSummonerHasBeenProcById(1, $summoner["s_id"]);
                            $mysql->UpdateSummonerIsActualUserById(1, $summoner["s_id"]);
                        }
                    }
                    $html .= "<td>" . $document["created_on"] . "</td>";
                    $html .= "</tr>";
                }
            }
        }
    }
    $html .= "</table>";
} else {
    $riot->insertIntoAPILog("processSummonerIds.php","PROCESS IS CURRENTLY DISABLED","processSummonerIds.php -> cursor for loop");
    $html = "<h3>PROCESS IS CURRENTLY DISABLED</h3>";
}
$riot->CloseConnection();
$mysql->CloseConnection();
echo $html;