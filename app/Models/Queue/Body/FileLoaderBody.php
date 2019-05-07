<?php

namespace App\Models\Queue\Body;

class FileLoaderBody
{
    private $fromURL;
    private $saveTo;
    private $name;

    /**
     * @return mixed
     */
    public function getFromURL()
    {
        return $this->fromURL;
    }

    /**
     * @param mixed $fromURL
     */
    public function setFromURL($fromURL): void
    {
        $this->fromURL = $fromURL;
    }

    /**
     * @return mixed
     */
    public function getSaveTo()
    {
        return $this->saveTo;
    }

    /**
     * @param mixed $saveTo
     */
    public function setSaveTo($saveTo): void
    {
        $this->saveTo = $saveTo;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }
}