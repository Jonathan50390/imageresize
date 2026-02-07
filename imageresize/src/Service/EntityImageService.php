<?php
class EntityImageService
{
    private $processor;
    public function __construct(ImageProcessorService $processor) { $this->processor = $processor; }

    public function regenerateImagesByEntity($entity)
    {
        switch ($entity) {
            case 'products': return $this->handleProducts();
            case 'categories': return $this->handleCategories();
            case 'slides': return $this->processor->processSlideImage(null, null); // Logique simplifiée
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
}
