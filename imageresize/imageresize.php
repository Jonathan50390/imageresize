<?php
if (!defined('_PS_VERSION_')) { exit; }

require_once __DIR__ . '/src/Service/ImageProcessorService.php';
require_once __DIR__ . '/src/Service/EntityImageService.php';
require_once __DIR__ . '/src/Helper/FormHelper.php';

class ImageResize extends Module
{
    public $imageProcessorService;
    public $entityImageService;
    private $formHelper;

    public function __construct()
    {
        $this->name = 'imageresize';
        $this->tab = 'administration';
        $this->version = '2.4.0';
        $this->author = 'Jonathan Guillerm';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Image Resize');
        $this->description = $this->l('Redimensionne automatiquement les images selon les paramètres du thème actif');
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => '9.99.99'];
        $this->initializeServices();
    }

    private function initializeServices()
    {
        $this->imageProcessorService = new ImageProcessorService();
        $this->entityImageService = new EntityImageService($this->imageProcessorService);
        $this->formHelper = new FormHelper($this);
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('actionAdminControllerSetMedia')
            && $this->registerHook('actionObjectImageAddAfter')
            && $this->registerHook('actionObjectImageUpdateAfter')
            && $this->registerHook('actionObjectCategoryUpdateAfter')
            && $this->registerHook('actionObjectManufacturerUpdateAfter');
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submitImageResize')) {
            $output .= $this->processImageResizeRequest();
        }
        return $output . $this->formHelper->renderConfigurationForm();
    }

    private function processImageResizeRequest()
    {
        $entity = Tools::getValue('image_entity', 'products');
        try {
            $count = $this->entityImageService->regenerateImagesByEntity($entity);
            return $this->displayConfirmation(sprintf($this->l('%d image(s) traitée(s) avec succès'), $count));
        } catch (Exception $e) {
            PrestaShopLogger::addLog('ImageResize Error: ' . $e->getMessage(), 3);
            return $this->displayError($this->l('Erreur : ') . $e->getMessage());
        }
    }

    public function hookActionObjectImageUpdateAfter($params) { $this->processImageHook($params); }
    public function hookActionObjectImageAddAfter($params) { $this->processImageHook($params); }

    private function processImageHook($params) {
        if (isset($params['object']) && $params['object'] instanceof Image) {
            $this->imageProcessorService->processProductImage($params['object'], ImageType::getImagesTypes('products'));
        }
    }

    public function hookActionObjectCategoryUpdateAfter($params) {
        if (isset($params['object']) && $params['object'] instanceof Category && $params['object']->id_image) {
            $this->imageProcessorService->processCategoryImage($params['object']->id, ImageType::getImagesTypes('categories'));
        }
    }

    public function hookActionObjectManufacturerUpdateAfter($params) {
        if (isset($params['object']) && $params['object'] instanceof Manufacturer) {
            $this->imageProcessorService->processManufacturerImage($params['object']->id, ImageType::getImagesTypes('manufacturers'));
        }
    }
}
