<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    public $imageFile;
    public $barcode;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'extensions' => 'png, jpg, jpeg'],
            [['barcode'], 'string'],
        ];
    }

    public function uploadAndProcess()
    {
        if ($this->validate()) {
            $path = 'uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension;
            if ($this->imageFile->saveAs($path)) {
                $output = shell_exec("python3 barcode_scanner/barcode_reader.py " . escapeshellarg($path));
                $barcode = trim($output);

                if ($barcode === 'false' || empty($barcode)) {
                    rename($path, 'lostnfound/' . basename($path));
                } else {
                    if (!is_dir("uploads/$barcode")) {
                        mkdir("uploads/$barcode", 0777, true);
                    }
                    rename($path, "uploads/$barcode/" . basename($path));
                }

                return true;
            }
        }
        return false;
    }
}
