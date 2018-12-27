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

        $this
            ->assertCountFilesWillBeCreated(6)
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.php', file_get_contents(__DIR__ . '/1ExpectedModel.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockInterface.php', file_get_contents(__DIR__ . '/1ExpectedInterface.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockImage.php', file_get_contents(__DIR__ . '/1ExpectedModelImage.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockImageInterface.php', file_get_contents(__DIR__ . '/1ExpectedInterfaceImage.php'))
            ->assertFileWillBeCreated($this->codeDir() . '/Mock.orm.xml', file_get_contents(__DIR__ . '/1ExpectedDoctrine.orm.xml'))
            ->assertFileWillBeCreated($this->codeDir() . '/MockImage.orm.xml', file_get_contents(__DIR__ . '/1ExpectedDoctrineImage.orm.xml'))
        ;
    }
}
