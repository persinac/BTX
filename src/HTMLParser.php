<?php

/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 10/29/2017
 * Time: 7:55 PM
 */
namespace HTMLTagCounter;

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '\markup-project-persinac\src\classes\HTMLTagMetadata.php';

class HTMLParser
{
    private $file;
    private $fileHandler; //for open files passed in
    private $fileContents;
    private $htmlTagCount;

    private function __construct($file)
    {
        $this->file = $file;
        $this->htmlTagCount = array();
        if ($this->fileHandler = fopen($this->file, "r")) {
            # Processing
            $this->fileContents = fread($this->fileHandler, filesize($this->file));
        }
    }

    public static function CreateNewHTMLParser($file)
    {
        return new HTMLParser($file);
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return bool|resource
     */
    public function getFileHandler()
    {
        return $this->fileHandler;
    }

    public function getFileContents() {
        return $this->fileContents;
    }

    public function closeFile() {
        fclose($this->fileHandler);
    }

    public function countHTMLTags($tagsToCount = "") {
        foreach ($tagsToCount as $tag) {
            $details = HTMLTagMetadata::HTMLTagMetadataFromIndividualParams($tag, $this->countHTMLTag($tag->tag));
//            var_dump($details);
            if($details->GetCount() > 0) {
                $this->htmlTagCount[] = $details;
            }
        }
    }

    private function countHTMLTag($tag) {
        $findFirstSpace = 0;
        $openTag = substr_count ($this->fileContents, '<' . $tag);
        $firstPosNoCarat = stripos($this->fileContents, $tag);
        if($firstPosNoCarat === false) {
            $firstPosNoCarat = 0;
        } else {
            //need to make sure the tag is correct
            $findFirstSpace = substr($this->fileContents, 0, strpos($this->fileContents, ' '));
            if($findFirstSpace === $tag) {
                $firstPosNoCarat = 1;
            } else {
                $firstPosNoCarat = 0;
            }
        }
        $counter = $openTag + $firstPosNoCarat;
        return $counter;
    }

    /*
     * Need to revisit this function...
     * Not really "returning" the counts per-se...
     * */
    public function returnHTMLTagCounts() {
        return $this->htmlTagCount;
    }
}