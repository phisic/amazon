<?php

/* @property Part $part The amazon connection. */

class WebApplication extends CWebApplication {

    /**
     * Merging yii framework and my class maps
     */
    protected function preinit() {
        Yii::$classMap += require(Yii::app()->basePath . '/config/classmap.php');
    }

    public function getSeoUrl($text) 
    {
        //Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
        $text = strtolower($text);
        $text = strtr($text, array(';'=>'','/'=>'',':'=>'','@'=>'','&'=>'','='=>'','+'=>'','$'=>'',','=>'','.'=>'','!'=>'','~'=>'','*'=>'','"'=>'',"'"=>'',')'=>'','('=>''));
        return $string;
    }

}