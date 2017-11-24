<?php
/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 10/27/17
 */
namespace HTMLTagCounter;

class HTMLTagMetadata
{
    var $tag;
    var $count;
    private function __construct($tag, $count)
    {
        $this->tag = $tag;
        $this->count = $count;
    }

    public static function HTMLTagMetadataFromObj($obj)
    {
        return new HTMLTagMetadata($obj->tag, $obj->count);
    }

    public static function HTMLTagMetadataFromIndividualParams($tag, $count)
    {
        return new HTMLTagMetadata($tag, $count);
    }

    /* Getters */
    function GetTag() {
        return $this->tag;
    }
    function GetCount() {
        return $this->count;
    }

    /* Setters */
    function SetTag($val) {
        $this->tag = $val;
    }
    function SetCount($val) {
        $this->count = $val;
    }
}