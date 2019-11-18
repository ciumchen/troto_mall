<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;
/**
* UploadForm is the model behind the upload form.
*/
class UploadForm extends Model
{
    /**
    * @var UploadedFile file attribute
    */
    public $file;

    /**
    * @return array the validation rules.
    */
    
    public function rules()
    {
        return [
             [['file'], 'file', 'extensions' => 'jpg, png', 'mimeTypes' => 'image/jpeg, image/png',],
        ];
    }

}