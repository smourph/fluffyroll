<?php

namespace FluffyRollBundle\DependencyInjection\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileUploader
 * @package FluffyRollBundle\DependencyInjection\Utils
 */
class FileUploader
{
    /**
     * @var string
     */
    private $_targetDir;

    /**
     * FileUploader constructor.
     * @param string $targetDir
     */
    public function __construct(string $targetDir)
    {
        $this->_targetDir = $targetDir;
    }

    /**
     * @param UploadedFile $file
     * @param string $prefix
     * @return string
     */
    public function upload(UploadedFile $file, string $prefix = ''): string
    {
        $fileName = $prefix.'-'.md5(uniqid()).'.'.$file->guessExtension();
        $file->move($this->_targetDir, $fileName);

        return $fileName;
    }

    /**
     * @param string $fileName
     */
    public function removeFile(string $fileName)
    {
        unlink($this->_targetDir.$fileName);
    }
}