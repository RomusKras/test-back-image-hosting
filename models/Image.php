<?php

namespace app\models;

use Yii;

class Image extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'images';
    }
}
