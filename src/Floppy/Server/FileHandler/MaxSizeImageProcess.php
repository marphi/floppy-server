<?php


namespace Floppy\Server\FileHandler;


use Floppy\Server\FileHandler\Exception\FileProcessException;
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Floppy\Common\AttributesBag;
use Floppy\Common\FileSource;
use Floppy\Common\Stream\StringInputStream;

class MaxSizeImageProcess implements ImageProcess
{
    private $maxWidth;
    private $maxHeight;

    function __construct($maxWidth, $maxHeight)
    {
        $this->maxHeight = (int) $maxHeight;
        $this->maxWidth = (int) $maxWidth;
    }

    public function process(ImagineInterface $imagine, FileSource $fileSource, AttributesBag $attrs)
    {
        try {
            $image = $imagine->load($fileSource->content());

            $size = $image->getSize();

            if ($size->getWidth() <= $this->maxWidth && $size->getHeight() <= $this->maxHeight) {
                return $fileSource;
            }

            $maxRatio = $this->maxWidth / $this->maxHeight;
            $ratio = $size->getWidth() / $size->getHeight();

            $newSize = $ratio > $maxRatio ? new Box($this->maxWidth, $this->maxWidth / $ratio)
                : new Box($this->maxHeight * $ratio, $this->maxHeight);

            $image->resize($newSize);

            $content = $image->get($fileSource->fileType()->prefferedExtension());
            $fileSource->discard();

            return new FileSource(new StringInputStream($content), $fileSource->fileType());
        } catch (\Imagine\Exception\Exception $e) {
            throw new FileProcessException('Image processing error', $e);
        }
    }
}