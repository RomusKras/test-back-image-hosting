<?php

use app\models\Image;
use yii\helpers\Html;

$this->title = 'Images';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="image-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <table class="table">
        <tr>
            <th>Filename</th>
            <th>Uploaded At</th>
            <th>Preview</th>
            <th>Download</th>
        </tr>
        <?php /** @var Image $images */
        foreach ($images as $image): ?>
            <tr>
                <td><?= Html::encode($image->filename) ?></td>
                <td><?= Html::encode($image->uploaded_at) ?></td>
                <td>
                    <a href="<?= Yii::getAlias('@web/uploads/') . $image->filename ?>" target="_blank">
                        <?= Html::img(Yii::getAlias('@web/uploads/') . $image->filename, ['width' => '100']) ?>
                    </a>
                </td>
                <td><?= Html::a('Download', ['/download/'.$image->id], ['class' => 'btn btn-primary']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?= Html::a('Download All Images', ['/download'], ['class' => 'btn btn-primary']) ?>
</div>
