<?php

namespace Wcms;

use DomainException;
use LogicException;
use RuntimeException;
use Wcms\Exception\Missingextensionexception;
use Wcms\Exception\Filesystemexception;
use Imagick;
use ImagickException;

class Serviceimageoptimizer
{
    public const IMG_MAX_WIDTH     = 1920;
    public const IMG_MAX_HEIGHT    = 1920;
    public const IMG_QUALITY       = 60;
    public const IMG_MAX_BPP       = 0.5;
    public const IMG_ALLOWED_EXT   = [
        'jpg',
        'jpeg',
        'png',
        'webp',
        'bmp',
    ];

    public const EXT_GD      = 'gd';
    public const EXT_IMAGICK = 'imagick';

    protected string $extension;

    /**
     * @throws Missingextensionexception    If nor imagick or is installed
     */
    public function __construct()
    {
        if (extension_loaded(self::EXT_IMAGICK)) {
            $this->extension = self::EXT_IMAGICK;
        } elseif (extension_loaded(self::EXT_GD)) {
            $this->extension = self::EXT_GD;
        } else {
            throw new Missingextensionexception('Nor imagick or gd PHP extension is installed');
        }
    }

    /**
     * Try to convert the image to an highly compressed WebP
     *
     * The file is untouched if the image is too small and compression already high
     *
     * @return Media                        Compressed image
     *
     * @throws Filesystemexception          If deleting the original media failed, or if file creation failed.
     * @throws RuntimeException             In case of other failures
     */
    public function optimize(Media $media, bool $deleteoriginal = true): Media
    {
        if ($media->type() !== Media::IMAGE) {
            throw new DomainException('Given Media should be an image');
        }

        try {
            $canbeoptimized = $this->canbeoptimized($media);
        } catch (RuntimeException $e) {
            Logger::error(
                "image optimizer: read dimensions of '%s': %s",
                $media->filename(),
                $e->getMessage()
            );
            return $media; // PHP could not get image dimensions. It may be beccause of unsupported format.
        }

        if (!$canbeoptimized) {
            return $media;
        }

        $convertmediapath = $media->dir() . '/' . $media->getbasefilename() . '.webp';

        try {
            $this->encode($media, $convertmediapath);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                "encode using %s on '%s': %s",
                $this->extension,
                $media->filename(),
                $e->getMessage()
            ));
        }

        if ($deleteoriginal) {
            Fs::deletefile($media->getlocalpath());
        }

        $encoded = new Media($convertmediapath);
        Logger::info("optimized image using %s: '%s'", $this->extension, $encoded->filename());

        return $encoded;
    }

    /**
     * Check if image extension is part of authorized formats for optimization
     * If yes, also check size, and current compression level (by comparing bit per pixel)
     *
     * @throws RuntimeException if reading width/height of image failed
     */
    protected function canbeoptimized(Media $media): bool
    {
        return (in_array($media->extension(), self::IMG_ALLOWED_EXT) &&
            (
                $media->bitperpixel() > self::IMG_MAX_BPP ||
                $media->width() >= self::IMG_MAX_WIDTH ||
                $media->height() >= self::IMG_MAX_HEIGHT
            )
        );
    }

    /**
     * Decode and then encode the image
     *
     * @throws RuntimeException             If an error occured while reading, decoding, encoding or writing file
     */
    protected function encode(Media $media, string $convertmediapath): void
    {
        switch ($this->extension) {
            case self::EXT_IMAGICK:
                $this->encodeimagick($media, $convertmediapath);
                break;

            case self::EXT_GD:
                $this->encodegd($media, $convertmediapath);
                break;

            default:
                throw new LogicException('Serviceimageoptimizer->extension should be set to `imagick` or `gd`');
        }
    }

    /**
     * Decode and then encode the image using Image Magick
     *
     * @throws RuntimeException             If reading, write file or encoding, decoding process failed
     */
    protected function encodeimagick(Media $media, string $convertmediapath): void
    {
        try {
            $image = new Imagick($media->getlocalpath());

            $this->fixorientationimagick($image);

            $image->adaptiveResizeImage(
                min($image->getImageWidth(), self::IMG_MAX_WIDTH),
                min($image->getImageHeight(), self::IMG_MAX_HEIGHT),
                true
            );
            $image->setImageFormat('webp');
            $image->setImageCompressionQuality(self::IMG_QUALITY);

            if (!$image->writeImage($convertmediapath)) {
                throw new RuntimeException('unknown error');
            }
        } catch (ImagickException $e) {
            throw new RuntimeException('Imagick: ' . $e->getMessage());
        }
    }

    /**
     * Decode and then encode the image using GD
     *
     * @throws Filesystemexception          If error with reading file
     * @throws RuntimeException             If error with decoding, encoding or writing file occured
     */
    protected function encodegd(Media $media, string $convertmediapath): void
    {
        $image = imagecreatefromstring(Fs::readfile($media->getlocalpath()));

        if ($image === false) {
            throw new RuntimeException(sprintf(
                "image decoding using GD for '%s': %s",
                $media->filename(),
                error_get_last()['message'] // get PHP E_WARNING message
            ));
        }

        $this->fixorientationgd($image, $media->getabsolutepath());

        $heightdiff = $media->height() - self::IMG_MAX_HEIGHT;
        $widthdiff = $media->width() - self::IMG_MAX_WIDTH;

        if ($heightdiff > 0 || $widthdiff > 0) {
            if ($heightdiff > $widthdiff) {
                $height = self::IMG_MAX_HEIGHT;
                $width = intval($media->width() * (self::IMG_MAX_HEIGHT / $media->height()));
            } else {
                $height = intval($media->height() * (self::IMG_MAX_WIDTH / $media->width()));
                $width = self::IMG_MAX_WIDTH;
            }

            $image = imagescale($image, $width, $height);
        }
        if (!imagewebp($image, $convertmediapath, self::IMG_QUALITY)) {
            throw new RuntimeException('unknown error');
        }
    }



    /** _____________________________________________________ Orientation fixers */

    /**
     * @throws ImagickException                 in case of Imagick errors
     */
    protected function fixorientationimagick(Imagick $image): void
    {
        if (method_exists($image, 'getImageProperty')) {
            $orientation = $image->getImageProperty('exif:Orientation');
        } else {
            $filename = $image->getImageFilename();

            if (empty($filename)) {
                $filename = 'data://image/jpeg;base64,' . base64_encode($image->getImageBlob());
            }

            $exif = exif_read_data($filename);
            $orientation = isset($exif['Orientation']) ? $exif['Orientation'] : null;
        }

        if (empty($orientation)) {
            return;
        }

        switch ($orientation) {
            case 2:
                $image->flopImage();
                break;

            case 3:
                $image->rotateImage('#000000', 180);
                break;

            case 4:
                $image->flipImage();
                break;

            case 5:
                $image->flopImage();
                $image->rotateImage('#000000', -90);
                break;

            case 6:
                $image->rotateImage('#000000', 90);
                break;

            case 7:
                $image->flopImage();
                $image->rotateImage('#000000', 90);
                break;

            case 8:
                $image->rotateImage('#000000', -90);
                break;
        }
    }

    /**
     * @param mixed $image
     *
     * @throws RuntimeException if fixing orientation failed
     *
     * @todo update $image var type when dropping PHP7.4 support *
     */
    protected function fixorientationgd(&$image, string $filename): void
    {
        $exif = @exif_read_data($filename); // prevent from throwing errors if the file does'nt have EXIF

        if ($exif === false) {
            return;
        }

        if (empty($exif['Orientation'])) {
            return;
        }

        switch ($exif['Orientation']) {
            case 2:
                $success = imageflip($image, IMG_FLIP_HORIZONTAL);
                break;

            case 3:
                $image = imagerotate($image, 180, 0);
                break;

            case 4:
                $success = imageflip($image, IMG_FLIP_VERTICAL);
                break;

            case 5:
                $success = imageflip($image, IMG_FLIP_HORIZONTAL);
                $image = imagerotate($image, 90, 0);
                break;

            case 6:
                $image = imagerotate($image, -90, 0);
                break;

            case 7:
                $success = imageflip($image, IMG_FLIP_HORIZONTAL);
                $image = imagerotate($image, -90, 0);
                break;

            case 8:
                $image = imagerotate($image, 90, 0);
                break;
        }

        if ($image === false || (isset($success) && $success === false)) {
            throw new RuntimeException('orientation fix failed');
        }
    }
}
