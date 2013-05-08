<?php
class WatchController extends Controller {
    public function actionIndex() {
       header('Content-Type: application/json'); 
       
       $wf = new WatchForm;
       
       $data['ASIN'] = explode('-', $_POST['ASIN']);
       $data['NewUsed'] = $data['ASIN'][1];
       $data['Price'] = $data['ASIN'][2];
       $data['PriceDate'] = date('Y-m-d H:i:s');
       $data['ASIN'] = $data['ASIN'][0];
       
       if(Yii::app()->user->getIsGuest()){
           $data['Email'] = $_POST['Email'];
           $data['FirstName'] = $_POST['FirstName'];
       }else{
           $data['Email'] = Yii::app()->user->getState('email');
           $data['FirstName'] = Yii::app()->user->getState('first_name');
       }
       
       $wf->setAttributes($data,false);
       
       if($wf->save()){
           echo '{"ok":true}';
       }else{
           echo json_encode(array('error' => $wf->getErrors()));
       }
       
       Yii::app()->end();
    }
}