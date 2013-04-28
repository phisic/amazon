<?php

class SiteController extends Controller {

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {

        if (!($r = Yii::app()->cache->get('new-releases'))) {
            $r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('NewReleases')->browseNodeLookup(Yii::app()->params['node']);
            Yii::app()->cache->add('new-releases', $r);
        }

        if (!empty($r['BrowseNodes']['BrowseNode']['NewReleases']['NewRelease'])) {
            $asin = array();
            foreach ($r['BrowseNodes']['BrowseNode']['NewReleases']['NewRelease'] as $i) {
                $asin[] = $i['ASIN'];
            }
            if (!empty($asin)) {
                
                if (!($r = Yii::app()->cache->get('new-releases-body'))) {
                    $asin = join(',', $asin);
                    $r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('Medium')->lookup($asin);
                    Yii::app()->cache->add('new-releases-body', $r);
                }
                
                $s = new Statistics();
                $pricedrop = $this->renderPartial('pricedrop', array('items' => $s->getTopPriceDrops(10)), true);
                $this->render('index', array('items' => $r['Items']['Item'], 'pricedrop' => $pricedrop));
            }
        }
        else
            $this->render('index2');
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the contact page
     */
    public function actionContact() {
        $model = new ContactForm;
        if (isset($_POST['ContactForm'])) {
            $model->attributes = $_POST['ContactForm'];
            if ($model->validate()) {
                $name = '=?UTF-8?B?' . base64_encode($model->name) . '?=';
                $subject = '=?UTF-8?B?' . base64_encode($model->subject) . '?=';
                $headers = "From: $name <{$model->email}>\r\n" .
                        "Reply-To: {$model->email}\r\n" .
                        "MIME-Version: 1.0\r\n" .
                        "Content-type: text/plain; charset=UTF-8";

                mail(Yii::app()->params['adminEmail'], $subject, $model->body, $headers);
                Yii::app()->user->setFlash('contact', 'Thank you for contacting us. We will respond to you as soon as possible.');
                $this->refresh();
            }
        }
        $this->render('contact', array('model' => $model));
    }

	public function actionAjaxLogin()
	{
		$model = new LoginForm;
		$result = array('success' => false);
		// collect user input data
		if (isset($_POST['LoginForm'])) {
			$model->attributes = $_POST['LoginForm'];
			if ($model->validate() && $model->login())
				$result = array('success' => true, 'url' => Yii::app()->user->returnUrl);
		}

		echo CJSON::encode($result);
	}

    /**
     * Displays the login page
     */
    public function actionLogin() {

        $model = new LoginForm;

	    $service = Yii::app()->request->getQuery('service');
	    if (isset($service))
	    {
		    $authIdentity = Yii::app()->eauth->getIdentity($service);

		    if ($authIdentity->authenticate())
		    {
			    $model->username = $authIdentity->getAttribute('email');
			    if ($model->login(true))
				    $this->redirect('/');
		    }
		    // Something went wrong, redirect to login page
		    $this->redirect(array('site/login'));
	    }

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

}