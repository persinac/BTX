<?php

/**
 * Created by PhpStorm.
 * User: apersinger
 * Date: 10/27/17
 */
class HTMLBuilder
{
    var $html;
    var $obj;

    /***
     * @param $obj
     *
     * obj structure:
     *  [
     *      headers: ["x","y","z"],
     *      data: [ <obj>, <obj>, <obj> ]
     *  ]
     *
     */
    function __construct($obj)
    {
        $this->obj = $obj;
        $this->html = '';
    }

    function BuildTable($order = array()) {
        $this->html = '<table class="standard_table">';
        $this->BuildTableHeaders();
        $this->BuildTableRows($order);
        $this->html .= '</table>';
    }

    function BuildTableHeaders() {
        $this->html .= '<tr>';
        foreach($this->obj as $items) {
            foreach($items["headers"] as $hdrs) {
                $this->html .= '<th>';
                $this->html .= $hdrs;
                $this->html .= '</th>';
            }
        }
        $this->html .= '</tr>';
    }

    function BuildTableRows($order = array())
    {
        foreach ($this->obj as $object) {
            foreach ($object["data"] as $items) {
                $this->html .= '<tr>';
                $itemDataArr = json_decode(json_encode($items), True);
                if (!is_null($itemDataArr)) {
                    foreach ($order as $fieldName) {
                        $val = $itemDataArr["$fieldName"];
                        if (!empty($val)) {
                            $this->html .= '<td class="'.$fieldName.'" data-'.$fieldName.'="'.$val.'">';
                            if($fieldName == "key") {$this->html .= '<a href="#'.$itemDataArr["id"].'">';}
                            $this->html .= $val;
                            if($fieldName == "key") {$this->html .= '</a>';}
                            $this->html .= '</td>';
                        }
                    }
                }
                $this->html .= '</tr>';
            }
        }
    }

    public function BuildDropdownListValues($values, $dataValueID) {
        $html = "";
        foreach ($values as $val) {
            $html .= '<li><a href="#" ';
            $html .= 'data-' . $dataValueID .'="'.$val.'">'.$val;
            $html .= '</a></li>';
        }
        return $html;
    }

    function GetHTML() {
        return $this->html;
    }
}