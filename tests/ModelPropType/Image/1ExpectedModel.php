<?php

declare(strict_types=1);

namespace App\Model;

class Mock implements MockInterface
{
    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var MockImageInterface
     */
    protected $logo;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogo(): ?MockImageInterface
    {
        return $this->logo;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogo(?MockImageInterface $logo): void
    {
        $this->logo = $logo;
        if ($logo) {
            $this->logo->setOwner($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFileBasePath()
    {
        return "mock";
    }
}
