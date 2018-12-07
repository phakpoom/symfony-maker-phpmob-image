<?php

declare(strict_types=1);

namespace Bonn\Maker\PhpMob\ModelPropType;

use Bonn\Maker\Generator\DoctrineXmlMappingGenerator;
use Bonn\Maker\Manager\CodeManagerInterface;
use Bonn\Maker\Model\Code;
use Bonn\Maker\ModelPropType\ManagerAwareInterface;
use Bonn\Maker\ModelPropType\ManagerAwareTrait;
use Bonn\Maker\ModelPropType\NamespaceModifyableInterface;
use Bonn\Maker\ModelPropType\PropTypeInterface;
use Bonn\Maker\Utils\NameResolver;
use Bonn\Maker\Utils\PhpDoctypeCode;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use PhpMob\MediaBundle\Model\FileAwareInterface;
use PhpMob\MediaBundle\Model\Image;
use PhpMob\MediaBundle\Model\ImageInterface;

/**
 * @commandValueSkip
 */
class ImageType implements PropTypeInterface, NamespaceModifyableInterface, ManagerAwareInterface
{
    use ManagerAwareTrait;

    /** @var string */
    protected $name;

    public function __construct(string $name, ?string $value = null)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeName(): string
    {
        return 'image';
    }

    /**
     * {@inheritdoc}
     */
    public function addProperty(ClassType $classType)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addGetter(ClassType $classType)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addSetter(ClassType $classType)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addDoctrineMapping(string $className, \SimpleXMLElement $XMLElement, CodeManagerInterface $codeManager, array $options)
    {
        $fullClassName = $className;
        $onlyClassName = NameResolver::resolveOnlyClassName($fullClassName);
        $imageMappingLocaled = $options['doctrine_mapping_dir'] . '/' . $onlyClassName . 'Image.orm.xml';

        $xml = DoctrineXmlMappingGenerator::createDoctrineMappingXml();
        $mappedSuper = $xml->addChild('mapped-superclass');
        $mappedSuper->addAttribute('name', $fullClassName . 'Image');
        $mappedSuper->addAttribute('table', strtolower(explode('\\', $fullClassName)[0]) . '_' . NameResolver::camelToUnderScore($onlyClassName) . '_image');

        $oneToOne = $mappedSuper->addChild('one-to-one');
        $oneToOne->addAttribute('field', 'owner');
        $oneToOne->addAttribute('target-entity', $fullClassName . 'Interface');
        $oneToOne->addAttribute('inversed-by', $this->name);
        $join = $oneToOne->addChild('join-column');

        $join->addAttribute('name', 'owner_id');
        $join->addAttribute('referenced-column-name', 'id');
        $join->addAttribute('nullable', 'false');
        $join->addAttribute('on-delete', 'CASCADE');

        // add parent
        $field = $XMLElement->addChild('one-to-one');
        $field->addAttribute('field', $this->name);
        $field->addAttribute('target-entity', $fullClassName . 'ImageInterface');
        $field->addAttribute('mapped-by', 'owner');
        $field->addAttribute('orphan-removal', 'true');
        $cascade = $field->addChild('cascade');
        $cascade->addChild('cascade-all');

        $dom = DoctrineXmlMappingGenerator::createDomWithRoot($xml);
        $codeManager->persist(new Code($dom->saveXML(), $imageMappingLocaled, [
            'doctrine_mapping_xml' => $xml,
        ]));
    }

    /**
     * need namespace
     *
     * @param ClassType $classType
     * @param PhpNamespace $namespace
     */
    protected function _addGetter(ClassType $classType, PhpNamespace $namespace)
    {
        $method = $classType
            ->addMethod('get' . ucfirst($this->name))
            ->setVisibility('public');
        $method->setReturnNullable(true);
        $method->setReturnType($this->getImageInterfaceName($namespace, $classType, true));
        $method
            ->setBody('return $this->' . $this->name . ';')
            ->setComment("\n@return " . $this->getImageInterfaceName($namespace, $classType) . "|null\n");

    }

    /**
     * need namespace
     *
     * @param ClassType $classType
     * @param PhpNamespace $namespace
     */
    protected function _addSetter(ClassType $classType, PhpNamespace $namespace)
    {
        $method = $classType
            ->addMethod('set' . ucfirst($this->name))
            ->setVisibility('public')
            ->setBody('$this->' . $this->name . ' = $' . $this->name . ';')
            ->addBody("\nif ($$this->name) {")
            ->addBody("\t" . '$this->' . $this->name . '->setOwner($this);')
            ->addBody('}');

        $method->setReturnType('void');

        $method
            ->addParameter($this->name)
            ->setNullable(true)
            ->setTypeHint($this->getImageInterfaceName($namespace, $classType, true));

        $method->setComment("\n@param " . $this->getImageInterfaceName($namespace, $classType) . "|null $$this->name \n");
    }

    /**
     * need namespace
     *
     * @param ClassType $classType
     * @param PhpNamespace $namespace
     */
    protected function _addProperty(ClassType $classType, PhpNamespace $namespace)
    {
        $prop = $classType
            ->addProperty($this->name)
            ->setVisibility('protected');

        $prop->setComment("\n@var " . $this->getImageInterfaceName($namespace, $classType) . "\n");
    }


    /**
     * {@inheritdoc}
     */
    public function modify(PhpNamespace $classNameSpace, PhpNamespace $interfaceNameSpace): void
    {
        /** @var ClassType $classType */
        $classType = current($classNameSpace->getClasses());
        $this->_addProperty($classType, $classNameSpace);
        $this->_addGetter($classType, $classNameSpace);
        $this->_addSetter($classType, $classNameSpace);
        // ClassImage.php
        $classNameSpace->addUse($this->getImageInterfaceName($classNameSpace, $classType, true));
        $imageClassNameSpace = new PhpNamespace($classNameSpace->getName());
        $imageClassNameSpace->addUse(Image::class);
        $imageClass = $imageClassNameSpace->addClass($this->getImageClassName($classNameSpace, $classType));
        $imageClass->addExtend(Image::class);
        $imageClass->addImplement($this->getImageInterfaceName($classNameSpace, $classType, true));

        $this->manager->persist(new Code(PhpDoctypeCode::render($imageClassNameSpace->__toString()),
            $this->getOption()['model_dir'] . "/{$imageClass->getName()}.php", [
                'namespace' => $imageClassNameSpace,
            ]));

        /** @var ClassType $classType */
        $interfaceType = current($interfaceNameSpace->getClasses());
        $this->_addGetter($interfaceType, $interfaceNameSpace);
        $this->_addSetter($interfaceType, $interfaceNameSpace);
        // ClassImageInterface.php
        $interfaceNameSpace->addUse(FileAwareInterface::class);
        $interfaceType->addExtend(FileAwareInterface::class);
        $imageInterfaceNameSpace = new PhpNamespace($interfaceNameSpace->getName());
        $imageInterfaceNameSpace->addUse(ImageInterface::class);
        $imageInterfaceClass = $imageInterfaceNameSpace->addInterface($this->getImageInterfaceName($imageInterfaceNameSpace, $classType));
        $imageInterfaceClass->addExtend(ImageInterface::class);

        $this->manager->persist(new Code(PhpDoctypeCode::render($imageInterfaceNameSpace->__toString()),
            $this->getOption()['model_dir'] . "/{$imageInterfaceClass->getName()}.php", [
                'namespace' => $imageInterfaceNameSpace,
            ]));

        // move getFileBasePath to bottom
        // Add getFileBasePath method
        $classType->removeMethod('getFileBasePath');
        $classType
            ->addMethod('getFileBasePath')
            ->setVisibility('public')
            ->setBody('return "' . NameResolver::camelToUnderScore($classType->getName()) . '";');
    }

    protected function getImageInterfaceName(PhpNamespace $namespace, ClassType $classType, $isFull = false): string
    {
        $className = str_replace('Interface', '', $classType->getName());
        $className = $className . 'ImageInterface';

        return $isFull ? $namespace->getName() . '\\' . $className : $className;
    }

    protected function getImageClassName(PhpNamespace $namespace, ClassType $classType, $isFull = false): string
    {
        $className = $classType->getName() . 'Image';

        return $isFull ? $namespace->getName() . '\\' . $className : $className;
    }
}
