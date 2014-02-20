<?php

namespace ZineInc\Storage\Server\FileHandler;

use ZineInc\Storage\Common\AttributesBag;
use ZineInc\Storage\Common\FileHandler\VariantMatcher;
use ZineInc\Storage\Common\FileId;
use ZineInc\Storage\Server\FileSource;
use ZineInc\Storage\Server\FileType;

interface FileHandler extends VariantMatcher
{
    /**
     * @param FileSource $fileType
     * @return boolean Does given $file is supported by this handler
     */
    public function supports(FileType $fileType);

    /**
     * @return string Unique type code
     */
    public function type();

    /**
     * @return \ZineInc\Storage\Common\AttributesBag Extra attributes for given file. This attributes should be associative array of primitives
     */
    public function getStoreAttributes(FileSource $file);

    /**
     * Additional file processing before persistent store file
     *
     * @return FileSource
     */
    public function beforeStoreProcess(FileSource $file);

    /**
     * Additional file processing before file download
     *
     * @param FileSource $file
     * @param AttributesBag $attributes Supported attributes depends on handler type
     *
     * @return FileSource
     */
    public function beforeSendProcess(FileSource $file, FileId $fileId);
}