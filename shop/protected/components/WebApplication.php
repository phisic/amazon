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
        $string = preg_replace("/[^a-zA-Z0-9 -]/", "", $string);
        $string = strtolower($string);
        $string = str_replace(" ", '-', $string);
        return $this->createUrl($route.'-'.$string);
    }

}