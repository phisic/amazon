<?php

/* @property Part $part The amazon connection. */

class WebApplication extends CWebApplication {

    /**
     * Merging yii framework and my class maps
     */
    protected function preinit() {
        Yii::$classMap += require(Yii::app()->basePath . '/config/classmap.php');
    }

    public function createSeoUrl($route,$string) 
    {
        if(strlen($string)>100)
            $string = substr ($string, 0, 100);
        
        $string = preg_replace("/[^a-zA-Z0-9 -]/", "", $string);
        $string = strtolower($string);
        $pos = strrpos($route, '/');
        $param = substr($route, $pos+1);
        $route = substr($route,0,$pos+1);
        $string = str_replace(" ", '-', $string);
        return $this->createUrl($route.$string.'-'.$param);
    }

}