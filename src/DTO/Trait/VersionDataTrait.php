<?php

namespace App\DTO\Trait;

trait VersionDataTrait
{
    private ?\DateTime $versionFrom = null;
    private ?\DateTime $versionTo = null;

    public function getVersionFrom(): ?\DateTime
    {
        return $this->versionFrom;
    }

    public function setVersionFrom(?\DateTime $versionFrom): self
    {
        $this->versionFrom = $versionFrom;

        return $this;
    }

    public function getVersionTo(): ?\DateTime
    {
        return $this->versionTo;
    }

    public function setVersionTo(?\DateTime $versionTo): self
    {
        $this->versionTo = $versionTo;

        return $this;
    }
}
