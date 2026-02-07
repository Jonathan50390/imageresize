<?php
if (!defined('_PS_VERSION_')) { exit; }

class ImageProcessorService
{
    private const EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function resizeImage($source, $dest, $width, $height, $format = 'jpg')
    {
        @ini_set('memory_limit', '512M');
        return ImageManager::resize($source, $dest, (int)$width, (int)$height, $format, true);
    }

    public function findSourceFile($path, $filename)
    {
        foreach (self::EXTENSIONS as $ext) {
            $testFile = $path . $filename . '.' . $ext;
            if (file_exists($testFile)) return $testFile;
        }
        return null;
    }

    public function processProductImage(Image $image, array $types)
    {
        $dir = _PS_PROD_IMG_DIR_ . $image->getImgFolder();
        $source = $this->findSourceFile($dir, $image->id);
        if (!$source) return false;

        foreach ($types as $type) {
            $dest = $dir . $image->id . '-' . stripslashes($type['name']) . '.jpg';
            $this->resizeImage($source, $dest, $type['width'], $type['height']);
        }
        return true;
    }

    public function processCategoryImage($id, array $types)
    {
        $dir = _PS_CAT_IMG_DIR_;
        $source = $this->findSourceFile($dir, $id);
        if (!$source) return false;

        foreach ($types as $type) {
            $dest = $dir . $id . '-' . stripslashes($type['name']) . '.jpg';
            $this->resizeImage($source, $dest, $type['width'], $type['height']);
        }
        return true;
    }

    public function processManufacturerImage($id, array $types)
    {
        $dir = _PS_MANU_IMG_DIR_;
        $source = $this->findSourceFile($dir, $id);
        if (!$source) return false;

        foreach ($types as $type) {
            $dest = $dir . $id . '-' . stripslashes($type['name']) . '.jpg';
            $this->resizeImage($source, $dest, $type['width'], $type['height']);
        }
        return true;
    }

    public function processSlideImage($id, $imageFile)
    {
        $paths = [_PS_ROOT_DIR_ . '/modules/ps_imageslider/images/', _PS_ROOT_DIR_ . '/img/cms/'];
        foreach ($paths as $path) {
            if (file_exists($path . $imageFile)) {
                $types = [['width' => 1110, 'height' => 340, 'name' => 'homeslider']];
                foreach ($types as $t) {
                    $this->resizeImage($path . $imageFile, $path . 're-' . $imageFile, $t['width'], $t['height']);
                }
                return true;
            }
        }
        return false;
    }
}
