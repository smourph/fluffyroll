<?php

namespace FluffyRollBundle\DependencyInjection\Utils;

use Symfony\Component\HttpFoundation\File\File;
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
        $prefix = $this->sanitize($prefix, true, true);
        $fileName = $prefix.'-'.md5(uniqid()).'.'.$file->guessExtension();
        $file->move($this->_targetDir, $fileName);

        return $fileName;
    }

    /**
     * @param string $fileName
     */
    public function removeFile(string $fileName)
    {
        unlink($this->_targetDir.'/'.$fileName);
    }

    /**
     * @param string $fileName
     * @return File
     */
    public function getFile(string $fileName)
    {
        return new File($this->_targetDir.'/'.$fileName);
    }

    /**
     * @param $string
     * @param bool $lowercase
     * @param bool $fullReplace
     * @return string
     */
    private function sanitize($string, $lowercase = true, $fullReplace = false)
    {
        $strip = ["~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
            "}", "\\", "|", ";", ":", "\"", "'", "", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
            "â€”", "â€“", ",", "<", ".", ">", "/", "?"];

        $clean = trim(str_replace($strip, "", strip_tags($string)));
        $clean = preg_replace('/\s+/', "_", $clean);
        $clean = ($fullReplace) ? preg_replace("/[^a-zA-Z0-9\-_]/", "", $clean) : $clean;

        return ($lowercase) ?
            (function_exists('mb_strtolower')) ?
                mb_strtolower($clean, 'UTF-8') :
                strtolower($clean) :
            $clean;
    }
}