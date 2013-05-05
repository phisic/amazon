<?php
class WatchController extends Controller {
    public function actionIndex() {
       header('Content-Type: application/json'); 
       $wf = new WatchForm;
       $wf->setAttributes($_POST);
       if($wf->validate()){
           echo '{"ok":true}';
           
       }else{
           echo json_encode(array('error' => $wf->getErrors()));
       }
       
       Yii::app()->end();
    }
}