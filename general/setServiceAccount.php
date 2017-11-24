<?php
/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 5/30/2017
 * Time: 8:50 AM
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/mongo/CRUD/classes/MongoUtility.php';
//var_dump([
//    \Sodium\library_version_major(),
//    \Sodium\library_version_minor(),
//    \Sodium\version_string()
//]);
//
if(isset($_POST["data"])) {
    $data = json_decode($_POST["data"]);
//    var_dump($data);
    $email = $data->email;
    $pw = $data->password;

    $hash_str = \Sodium\crypto_pwhash_str(
        $pw,
        \Sodium\CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
        \Sodium\CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
    );
    $dataToInsert = new stdClass();
    $dataToInsert->data->email = $email;
    $dataToInsert->data->password = $hash_str;

    /*
    if (\Sodium\crypto_pwhash_str_verify($hash_str, $pw)) {
        // recommended: wipe the plaintext password from memory
        \Sodium\memzero($pw);
        echo "TRUE";

        // Password was valid
    } else {
        // recommended: wipe the plaintext password from memory
        \Sodium\memzero($pw);
        echo "FALSE";

        // Password was invalid.
    }
    */

    $mongoObj = new MongoUtility();
    $collectionName = "SERVICE_ACCOUNT";
    $mongoObj->SelectDBToUse("test");
    $listOfCollections = $mongoObj->ListAllCollections();
    if(DoesCollectionExist($listOfCollections, $collectionName)) {
        $mongoObj->SelectCollection($collectionName);
    } else {
        $mongoObj->CreateNewCollection($collectionName);
        $mongoObj->SelectCollection($collectionName);
    }
//var_dump($dataToInsert);
    $mongoObj->InsertIntoCollection($dataToInsert);
}

function DoesCollectionExist($collections, $collectionName) {
    $result = false;
    foreach($collections as $cName) {
        if($cName == $collectionName) {
            $result = true;
        }
    }
    return $result;
}
