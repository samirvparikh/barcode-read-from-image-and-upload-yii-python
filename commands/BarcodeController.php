<?php

namespace app\commands;

use yii\console\Controller;
use yii\helpers\Console;
use Yii;
use yii\helpers\FileHelper;

class BarcodeController extends Controller
{
    public function actionProcessImages()
    {
        $directory = '/var/www/html/samir/yii/basic/barcode_images';
        $processedDir = $directory . '/processed';
        // dd($directory);
        FileHelper::createDirectory($processedDir);

        $files = glob($directory . '/*.{jpg,jpeg,png}', GLOB_BRACE);

        if (empty($files)) {
            $this->stdout("No images found.\n", Console::FG_YELLOW);
            return;
        }

        foreach ($files as $file) {
            $filename = basename($file);
            $storagePath = Yii::getAlias('@app/runtime/uploads/' . $filename);
            FileHelper::createDirectory(dirname($storagePath));
            copy($file, $storagePath);

            $command = 'python3 ' . escapeshellarg(Yii::getAlias('@app/barcode_scanner/barcode_reader.py')) . ' ' . escapeshellarg($storagePath);
            $output = trim(shell_exec($command));

            if ($output === 'false' || empty($output)) {
                // Barcode not found → move to lostnfound
                $lostPath = Yii::getAlias('@app/runtime/lostnfound/' . $filename);
                FileHelper::createDirectory(dirname($lostPath));
                copy($file, $lostPath);
                unlink($storagePath);
                rename($file, $processedDir . '/' . $filename);

                $this->stderr("❌ Barcode not found. Moved to lostnfound: $filename\n", Console::FG_RED);
                continue;
            }

            // Barcode found → move to barcode folder
            $barcodeFolder = Yii::getAlias('@app/runtime/barcodes/' . $output);
            FileHelper::createDirectory($barcodeFolder);
            $finalPath = $barcodeFolder . '/' . $filename;
            copy($file, $finalPath);
            unlink($storagePath);
            rename($file, $processedDir . '/' . $filename);

            $this->stdout("✅ Scanned [$output] → saved to $finalPath\n", Console::FG_GREEN);
        }

        $this->stdout("🎉 All images processed.\n", Console::FG_CYAN);
    }
}
