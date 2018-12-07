<?php

declare(strict_types=1);

namespace App\Model;

use PhpMob\MediaBundle\Model\FileAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface MockInterface extends ResourceInterface, FileAwareInterface
{
    /**
     * @return MockImageInterface|null
     */
    public function getLogo(): ?MockImageInterface;

    /**
     * @param MockImageInterface|null $logo
     */
    public function setLogo(?MockImageInterface $logo): void;
}
