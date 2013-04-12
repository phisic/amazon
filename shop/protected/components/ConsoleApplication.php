<?php

class ConsoleApplication extends CConsoleApplication
{

	/**
	 * Merging yii framework and my class maps
	 */
	protected function preinit()
	{
		Yii::$classMap += require(Yii::app()->basePath . '/config/classmap.php');
	}

}