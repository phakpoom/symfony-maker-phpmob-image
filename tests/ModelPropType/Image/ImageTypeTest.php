<?php

declare(strict_types=1);

namespace Tests\Bonn\Bundle\MediaBundle\ModelPropType\Image;

use Bonn\Maker\PhpMob\ModelPropType\ImageType;
use Test\ModelPropType\AbstractPropTypeTest;

class ImageTypeTest extends AbstractPropTypeTest
{
    public function testGenerated()
    {
        $this->generate([
            new ImageType('logo'),
        ]);

        $allCodes = $this->manager->getCodes();

        $this->assertCount(6, $allCodes);
        $this->assertEquals(file_get_contents(__DIR__ . '/1ExpectedModel.php'),
            $allCodes[$this->codeDir() . '/Mock.php']->getContent());
        $this->assertEquals(file_get_contents(__DIR__ . '/1ExpectedInterface.php'),
            $allCodes[$this->codeDir() . '/MockInterface.php']->getContent());

        $this->assertEquals(file_get_contents(__DIR__ . '/1ExpectedModelImage.php'),
            $allCodes[$this->codeDir() . '/MockImage.php']->getContent());

        $this->assertEquals(file_get_contents(__DIR__ . '/1ExpectedInterfaceImage.php'),
            $allCodes[$this->codeDir() . '/MockImageInterface.php']->getContent());

        $this->assertEquals(file_get_contents(__DIR__ . '/1ExpectedDoctrine.orm.xml'),
            $allCodes[$this->codeDir() . '/Mock.orm.xml']->getContent());

        $this->assertEquals(file_get_contents(__DIR__ . '/1ExpectedDoctrineImage.orm.xml'),
            $allCodes[$this->codeDir() . '/MockImage.orm.xml']->getContent());
    }
}
