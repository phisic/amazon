<?php

class WatchForm extends CFormModel {

    public $FirstName;
    public $Email;
    public $ASIN;
    public $NewUsed;
    public $Price;
    public $PriceDate;

    public function rules() {
        return array(
            array('Email, FirstName, ASIN, NewUsed, Price, PriceDate', 'required'),
            array('Email', 'email'),
            array('ASIN', 'uniqASINEmail'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
            'FirstName' => 'First name',
        );
    }

    public function uniqASINEmail() {
        $c = new CDbCriteria();
        $c->addColumnCondition(array('ASIN' => $this->ASIN, 'NewUsed' => $this->NewUsed));
        if (Yii::app()->user->getIsGuest()) {
            $c->addColumnCondition(array('Email' => $this->Email));
        } else {
            $c->addColumnCondition(array('UserId' => Yii::app()->user->getId()));
        }

        $rowCount = Yii::app()->db->getCommandBuilder()->createCountCommand('watch', $c)->queryScalar();
        if ($rowCount)
            $this->addError('Email', (Yii::app()->user->getIsGuest() ? 'Already in watch for this email.' : 'Already in your watch.'));
    }

    public function save() {
        if ($this->validate()) {
            $data = $this->getAttributes();

            $data['UserId'] = Yii::app()->user->getIsGuest() ? 0 : Yii::app()->user->getId();
            return Yii::app()->db->getCommandBuilder()->createInsertCommand('watch', $data)->execute();
        }
    }

    public function remove($id) {
        $c = new CDbCriteria();
        $c->addColumnCondition(array('id' => $id));
        return Yii::app()->db->getCommandBuilder()->createDeleteCommand('watch', $c)->execute();
    }

}
