<?php
// src/Service/EntityImageService.php

public function regenerateSlideImages()
{
    $count = 0;
    // ... (logique de sélection des tables existante)

    foreach ($slides as $slide) {
        $imageFile = $this->extractImageName($slide, $hasImage, $hasImageUrl);

        if ($imageFile) {
            // Vérification stricte du processeur avant d'incrémenter
            if ($this->imageProcessor->processSlideImage($slide['id_homeslider_slides'], $imageFile)) {
                $count++;
            }
        }
    }

    if ($count === 0) {
        PrestaShopLogger::addLog('ImageResize: Aucune image de slide traitée.', 2);
    }

    return $count;
}

private function extractImageName($slide, $hasImage, $hasImageUrl) {
    if ($hasImage && !empty($slide['image'])) return $slide['image'];
    if ($hasImageUrl && !empty($slide['image_url'])) return basename($slide['image_url']);
    return null;
}
