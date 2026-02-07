// Dans imageresize.php, ajoutez ces hooks à l'installation :
public function install()
{
    return parent::install()
        && $this->registerHook('actionAdminControllerSetMedia')
        && $this->registerHook('actionObjectImageAddAfter')
        && $this->registerHook('actionObjectImageUpdateAfter')
        && $this->registerHook('actionObjectCategoryUpdateAfter') // Nouveau
        && $this->registerHook('actionObjectManufacturerUpdateAfter'); // Nouveau
}

// Exemple pour les catégories
public function hookActionObjectCategoryUpdateAfter($params)
{
    if (isset($params['object']) && $params['object']->id_image) {
        $imageTypes = ImageType::getImagesTypes('categories');
        $this->imageProcessorService->processCategoryImage($params['object']->id, $imageTypes);
    }
}
