<?php

namespace app\models;

use Transliterator;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\Inflector;

class ImageForm extends Model
{
    public $images;

    public function rules()
    {
        return [
            [['images'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg', 'maxFiles' => 5],
        ];
    }

    private function transliterate($string)
    {
        // Массив соответствия кириллических символов английским
        $transliterationTable = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya', ' ' => '_', '-' => '_', ',' => '', '.' => ''
        );

        // Заменяем все кириллические символы на английские
        $string = mb_strtolower($string, 'UTF-8');
        $string = strtr($string, $transliterationTable);

        return $string;
    }

    /**
     * @throws Exception
     */
    private function generateUniqueFilename($filename): string
    {
        $baseFilename = Inflector::slug(pathinfo($filename, PATHINFO_FILENAME));
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $newFilename = $baseFilename . '.' . $extension;
        while (file_exists(Yii::getAlias('@webroot/uploads/') . $newFilename)) {
            $newFilename = $baseFilename . '-' . Yii::$app->security->generateRandomString(8) . '.' . $extension;
        }
        return $newFilename;
    }

    /**
     * @throws Exception
     */
    public function upload(): bool
    {
        if ($this->validate()) {
            foreach ($this->images as $image) {
                $filename = $this->generateUniqueFilename($this->transliterate($image->baseName) . '.' . $image->extension);
                $path = Yii::getAlias('@webroot/uploads/') . $filename;
                $image->saveAs($path);

                $model = new Image();
                $model->filename = $filename;
                $model->uploaded_at = date('Y-m-d H:i:s');
                $model->save();
            }
            return true;
        } else {
            return false;
        }
    }
}
