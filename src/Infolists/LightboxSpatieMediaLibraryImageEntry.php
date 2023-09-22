<?php

namespace Njxqlus\Filament\Components\Infolists;

use Filament\Infolists\ComponentContainer;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Njxqlus\Filament\Components\GLightBox;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class LightboxSpatieMediaLibraryImageEntry extends SpatieMediaLibraryImageEntry
{
    use GLightBox;

    protected string $view = 'filament-lightbox::infolists.lightbox-spatie-media-library-image-entry';

    public function getChildComponentContainer($key = null): ComponentContainer
    {
        if (filled($key)) {
            return $this->getChildComponentContainers()[$key];
        }

        return LightboxComponentContainer::make($this->getLivewire())
            ->parentComponent($this)
            ->components(fn() => $this
                ->getRecord()
                ->getRelationValue('media')
                ->filter(fn(Media $media): bool => blank($this->getCollection()) || ($media->getAttributeValue('collection_name') === $this->getCollection()))
                ->sortBy('order_column')
                ->transform(fn(Media $item) => $this->makeLightboxEntryFromMedia($item))
                ->toArray()
            );
    }

    protected function makeLightboxEntryFromMedia(Media $media)
    {
        $entry = LightboxImageEntry::make($media->uuid)
            ->label(false)
            ->slideGallery($this->getStatePath());

        if ($media->hasGeneratedConversion($this->getConversion())) {
            $entry->state($media->getFullUrl($this->getConversion()));
        } else {
            $entry->state($media->getFullUrl());
        }

        $entry->href($media->getFullUrl());

        if ($this->isCircular()) $entry->circular();
        if ($this->isSquare()) $entry->square();
        if ($this->getHeight()) $entry->height($this->getHeight());
        if ($this->getWidth()) $entry->width($this->getWidth());
        if ($this->getVisibility()) $entry->visibility($this->getVisibility());
        if ($this->getDefaultImageUrl()) $entry->defaultImageUrl($this->getDefaultImageUrl());

        if ($this->getSlideWidth()) $entry->slideWidth($this->getSlideWidth());
        if ($this->getSlideHeight()) $entry->slideHeight($this->getSlideHeight());
        if ($this->getSlideZoomable()) $entry->slideZoomable($this->getSlideZoomable());
        if ($this->getSlideDraggable()) $entry->slideDraggable($this->getSlideDraggable());
        if ($this->getSlideEffect()) $entry->slideEffect($this->getSlideEffect());

        return $entry;
    }
}
