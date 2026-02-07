<?php
class ImageProcessorService
{
    private const EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function resizeImage($source, $dest, $width, $height, $format = 'jpg')
    {
        @ini_set('memory_limit', '512M'); // Sécurité pour les grandes images
        return ImageManager::resize($source, $dest, (int)$width, (int)$height, $format, true);
    }

    public function findSourceFile($path, $filename)
    {
        foreach (self::EXTENSIONS as $ext) {
            if (file_exists($path . $filename . '.' . $ext)) return $path . $filename . '.' . $ext;
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
}
