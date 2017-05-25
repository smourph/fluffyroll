<?php

namespace FluffyRollBundle\DependencyInjection\Namer;

use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Vich\UploaderBundle\Exception\NameGenerationException;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\ConfigurableInterface;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Naming\Polyfill\FileExtensionTrait;
use Vich\UploaderBundle\Util\Transliterator;

/**
 * PropertyAndUniqIdNamer.
 */
class PropertyAndUniqIdNamer implements NamerInterface, ConfigurableInterface
{
    use FileExtensionTrait;

    /**
     * @var string
     */
    private $propertyPath;

    /**
     * @var bool
     */
    private $transliterate = false;

    /**
     * @param array $options Options for this namer. The following options are accepted:
     *                       - property: path to the property used to name the file. Can be either an attribute or a method.
     *                       - transliterate: whether the filename should be transliterated or not
     */
    public function configure(array $options)
    {
        if (empty($options['property'])) {
            throw new \InvalidArgumentException('Option "property" is missing or empty.');
        }

        $this->propertyPath = $options['property'];
        $this->transliterate = isset($options['transliterate']) ? (bool)$options['transliterate'] : $this->transliterate;
    }

    /**
     * {@inheritdoc}
     */
    public function name($object, PropertyMapping $mapping)
    {
        if (empty($this->propertyPath)) {
            throw new \LogicException(
                'The property to use can not be determined. Did you call the configure() method?'
            );
        }

        $file = $mapping->getFile($object);

        try {
            $name = $this->getPropertyValue($object, $this->propertyPath);
        } catch (NoSuchPropertyException $e) {
            throw new NameGenerationException(
                sprintf('File name could not be generated: property %s does not exist.', $this->propertyPath),
                $e->getCode(),
                $e
            );
        }

        if (empty($name)) {
            throw new NameGenerationException(
                sprintf('File name could not be generated: property %s is empty.', $this->propertyPath)
            );
        }

        if ($this->transliterate) {
            $name = Transliterator::transliterate($name);
        }

        $name = $this->sanitize($name, true, true);

        $name .= '-'.uniqid();

        // append the file extension if there is one
        if ($extension = $this->getExtension($file)) {
            $name = sprintf('%s.%s', $name, $extension);
        }

        return $name;
    }

    /**
     * @param $object
     * @param $propertyPath
     * @return mixed
     */
    private function getPropertyValue($object, $propertyPath)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($object, $propertyPath);
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