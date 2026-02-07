<?php
if (!defined('_PS_VERSION_')) { exit; }

class EntityImageService
{
    private $processor;

    public function __construct(ImageProcessorService $processor)
    {
        $this->processor = $processor;
    }

    public function regenerateImagesByEntity($entity)
    {
        switch ($entity) {
            case 'products': return $this->handleProducts();
            case 'categories': return $this->handleCategories();
            case 'manufacturers': return $this->handleManufacturers();
            case 'slides': return $this->handleSlides();
            default: return 0;
        }
    }

    private function handleProducts()
    {
        $types = ImageType::getImagesTypes('products');
        $images = Image::getAllImages();
        $count = 0;
        foreach ($images as $img) {
            if ($this->processor->processProductImage(new Image($img['id_image']), $types)) $count++;
        }
        return $count;
    }

    private function handleCategories()
    {
        $types = ImageType::getImagesTypes('categories');
        $categories = Category::getCategories(false, false);
        $count = 0;
        foreach ($categories as $cat) {
            if ($this->processor->processCategoryImage($cat['id_category'], $types)) $count++;
        }
        return $count;
    }

    private function handleManufacturers()
    {
        $types = ImageType::getImagesTypes('manufacturers');
        $manufacturers = Manufacturer::getManufacturers();
        $count = 0;
        foreach ($manufacturers as $m) {
            if ($this->processor->processManufacturerImage($m['id_manufacturer'], $types)) $count++;
        }
        return $count;
    }

    private function handleSlides()
    {
        $count = 0;
        $slides = Db::getInstance()->executeS('SELECT image FROM ' . _DB_PREFIX_ . 'homeslider_slides_lang');
        if ($slides) {
            foreach ($slides as $s) {
                if (!empty($s['image']) && $this->processor->processSlideImage(null, $s['image'])) $count++;
            }
        }
        return $count;
    }
}
