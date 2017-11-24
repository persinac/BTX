<?php
/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 4/23/2017
 * Time: 8:53 PM
 */

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require 'mailer.php';
require 'insertExcelDataIntoDB.php';


$target_dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";
$target_file = $target_dir . basename($_FILES["file"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    unlink($target_file);
    echo "File already exists - unlinking.";
    $uploadOk = 1;
}
// Check file size
if ($_FILES["file"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
echo "Image file type: ".$imageFileType . " </br>";
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" && $imageFileType != "xls" && $imageFileType != "xlsx") {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["file"]["name"]). " has been uploaded to: " . $target_file . "<br/>";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

if($uploadOk = 1) {
    /* Parse excel file */
    $dataToInsert = ParseExcelData(pathinfo($target_file,PATHINFO_BASENAME), $_POST["fileType"]);
    $monthDataArr[] = array();
    if(count($dataToInsert) > 0) {
        if($_POST["fileType"] == 0) {
            echo "<br/>Insert Month data<br/>";
            foreach($dataToInsert as $md) {
                if(is_object($md)) {
                    if(strlen($md->empEmail) > 0) {
                        $emailToSearch = $md->empEmail;
                        echo $emailToSearch . ' - ' . $md->date . '</br>';
                        $searchResults = getEmployeeIdByEmail($emailToSearch);
                        foreach($searchResults as $var) {
                            $idFound = $var->employee->id;
                        }
                        $monthData = new MonthData(
                            "",
                            $idFound,
                            $md->empEmail,
                            $md->date,
                            null,
                            $md->status
                        );
                        $monthDataArr[] = $monthData;
                    }
                }
            }
            if(count($monthDataArr) > 0) {
                $currMthYr = GetCurrentMonth() . GetCurrentYear();
                $delOutput = DeleteAllDocuments($currMthYr);
                $insertOutput = InsertExcelData($monthDataArr);
                $count = 0;
                foreach($insertOutput as $doc) {
                    $count++;
                }
                echo $delOutput . '</br> Inserted ' . $count . ' records for blogs.';
            }
        } else {
            /*
            Because we don't have user management yet,
            just delete all employees, then upload
             */
            $delOutput = DeleteAllDocuments("Employees");
            $insertOutput = InsertExcelData($dataToInsert, "Employees");

            /**
             * Format the insert object.
             * Eventually it'll be its own f(x)
             */
            $output[] = array();
            foreach($insertOutput as $doc) {
                if(!empty($doc->employee)) {
                    $details = new Employee(
                        "",
                        $doc->id,
                        $doc->firstName,
                        $doc->lastName,
                        $doc->email,
                        $doc->phone
                    );
                    $output[] = $details;
                }
            }

            echo $delOutput . " </br> Inserted " . count($output) . " employees.";
        }

    } else {
        echo "<br/>No data to insert!";
    }

}
echo '<br/><br/>';

function includeDir($path) {
    $filesInDir      = scandir($path);
    foreach ($filesInDir as $file) {
        if (preg_match('%\.php$%', $file)) {
            include $path . $file;
        }
    }
}

