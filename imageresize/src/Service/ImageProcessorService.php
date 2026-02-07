<?php
// src/Service/ImageProcessorService.php

class ImageProcessorService
{
    private const SUPPORTED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function resizeImage($sourceFile, $destFile, $width, $height, $format = 'jpg')
    {
        // Augmentation de la mémoire pour gérer les grandes images sources
        @ini_set('memory_limit', '512M');

        // Utilisation du paramètre de qualité PrestaShop si disponible
        return ImageManager::resize(
            $sourceFile,
            $destFile,
            (int)$width,
            (int)$height,
            $format,
            true // Force le maintien du ratio d'aspect
        );
    }

    public function processProductImage(Image $image, array $imageTypes)
    {
        $imageDir = defined('_PS_PROD_IMG_DIR_') ? _PS_PROD_IMG_DIR_ : _PS_ROOT_DIR_ . '/img/p/';
        $sourceFile = $this->findSourceFile($imageDir . $image->getImgFolder(), $image->id);

        if (!$sourceFile) return false;

        foreach ($imageTypes as $imageType) {
            // Nettoyage du nom du type d'image pour le nom de fichier
            $typeName = str_replace([' ', '/'], '-', stripslashes($imageType['name']));
            $destFile = $imageDir . $image->getImgFolder() . $image->id . '-' . $typeName . '.jpg';

            if (!$this->resizeImage($sourceFile, $destFile, $imageType['width'], $imageType['height'])) {
                PrestaShopLogger::addLog("ImageResize: Échec ID " . $image->id, 3);
            }
        }
        return true;
    }
    // ... (appliquer des nettoyages similaires aux autres méthodes processX)
}
