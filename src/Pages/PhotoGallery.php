<?php

namespace AndrewHoule\PhotoGallery\Pages;

use AndrewHoule\PhotoGallery\Models\PhotoAlbum;
use AndrewHoule\PhotoGallery\Models\PhotoItem;
use Page;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use TractorCow\SliderField\SliderField;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

class PhotoGallery extends Page
{

    private static $db = [
        'AlbumsPerPage' => 'Int',
        'AlbumThumbnailHeight' => 'Int',
        'AlbumThumbnailWidth' => 'Int',
        'AlbumDefaultTop' => 'Boolean',
        'ShowAllPhotoAlbums' => 'Boolean',
        'PhotosPerPage' => 'Int',
        'PhotoThumbnailHeight' => 'Int',
        'PhotoThumbnailWidth' => 'Int',
        'PhotoFullHeight' => 'Int',
        'PhotoFullWidth' => 'Int',
        'PhotoDefaultTop' => 'Boolean'
    ];

    private static $has_one = [
        'DefaultAlbumCover' => Image::class
    ];

    private static $has_many = [
        'PhotoAlbums' => PhotoAlbum::class,
        'PhotoItems' => PhotoItem::class
    ];

    private static $owns = [
        'DefaultAlbumCover'
    ];

    private static $defaults = [
        'AlbumsPerPage' => 6,
        'PhotosPerPage' => 20,
        'ShowAllPhotoAlbums' => true,
        'AlbumThumbnailWidth' => 400,
        'AlbumThumbnailHeight' => 400,
        'AlbumDefaultTop' => true,
        'PhotoThumbnailWidth' => 400,
        'PhotoThumbnailHeight' => 400,
        'PhotoFullWidth' => 1200,
        'PhotoFullHeight' => 1200,
        'PhotoDefaultTop' => true
    ];

    private static $icon = 'photogallery/img/photogallery';

    private static $table_name = 'PhotoGallery';

    public function GalleryPageFolderName()
    {
        $directoryName = 'photogallery';
        $defaultName = 'gallerypage';
        $name = $this->MenuTitle;

        if ($name) {
            if ($name = $this->MenuTitle) {
                $string = strtolower($name);
                $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
                $string = preg_replace("/[\s-]+/", " ", $string);
                $string = preg_replace("/[\s_]/", "-", $string);
                return $directoryName . '/' . $string;
            } else {
                return $directoryName . '/' . $defaultName;
            }
        } else {
            return $directoryName . '/' . $defaultName;
        }
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // Albums
        $fields->addFieldToTab('Root.Albums',
            GridField::create(
                'PhotoAlbums',
                'Albums',
                $this->PhotoAlbums(),
                GridFieldConfig_RecordEditor::create(100)
                    ->addComponent($sortableAlbums = new GridFieldSortableRows('SortID'))
            )
        );
        $sortableAlbums->setUpdateVersionedStage('Live');
        if ($this->AlbumDefaultTop == true) {
            $sortableAlbums->setAppendToTop(true);
        }

        // Album Settings
        $fields->addFieldsToTab('Root.AlbumSettings', [
            UploadField::create('DefaultAlbumCover')
                ->setDescription('jpg, gif and png filetypes allowed.')
                ->setFolderName($this->GalleryPageFolderName())
                ->setAllowedExtensions([
                    'jpg',
                    'jpeg',
                    'png',
                    'gif'
                ]),
            SliderField::create('AlbumsPerPage', 'Albums Per Page', 1, 100, $this->AlbumsPerPage),
            SliderField::create('AlbumThumbnailWidth', 'Album Cover Thumbnail Width', 50, 400, $this->AlbumThumbnailWidth),
            SliderField::create('AlbumThumbnailHeight', 'Album Cover Thumbnail Height', 50, 400, $this->AlbumThumbnailHeight),
            CheckboxField::create('ShowAllPhotoAlbums', $this->ShowAllPhotoAlbums)
                ->setTitle('Show photo album even if it\'s empty'),
            CheckboxField::create('AlbumDefaultTop', $this->AlbumDefaultTop)
                ->setTitle('Sort new albums to the top by default')
        ]);

        // Photo Settings
        $fields->addFieldsToTab('Root.PhotoSettings', [
            SliderField::create('PhotosPerPage', 'Photos Per Page', 1, 50, $this->PhotosPerPage),
            SliderField::create('PhotoThumbnailWidth', 'Photo Thumbnail Width', 50, 400, $this->PhotoThumbnailWidth),
            SliderField::create('PhotoThumbnailHeight', 'Photo Thumbnail Height', 50, 400, $this->PhotoThumbnailHeight),
            SliderField::create('PhotoFullWidth', 'Photo Fullsize Width', 400, 1900, $this->PhotoFullWidth),
            SliderField::create('PhotoFullHeight', 'Photo Fullsize Height', 400, 1200, $this->PhotoFullHeight),
            CheckboxField::create('PhotoDefaultTop', $this->PhotoDefaultTop)
                ->setTitle('Sort new photos to the top by default')
        ]);

        return $fields;
    }

}
