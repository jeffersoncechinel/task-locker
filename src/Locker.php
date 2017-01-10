<?php

namespace JC\TaskLocker;

/**
 * Class Locker
 * @package JC\TaskLocker
 */
class Locker
{
    /**
     * Default runtime path
     */
    const RUNTIME_PATH = '/tmp';
    /**
     * Default lockfile extension
     */
    const LOCKFILE_EXT = '.lck';
    /**
     * @var null
     */
    public $name;
    /**
     * @var
     */
    public $fileExtension;
    /**
     * @var string
     */
    public $runtimePath;
    /**
     * @var
     */
    public $expiry;
    /**
     * @var
     */
    protected $lockFile;

    /**
     * Locker constructor.
     * @param null $name
     * @param string $runtimePath
     * @param int $expiry
     */
    public function __construct($name = null, $runtimePath = self::RUNTIME_PATH, $expiry = 0)
    {
        $this->setName($name);
        $this->setRuntimePath($runtimePath);
        $this->setExpiry($expiry);
        $this->setFileExtension();
    }

    /**
     * Sets the task locker unique identification name
     * @param null $name
     * @return Locker
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Gets the task locker unique identification name
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $fileExtension
     * @return $this
     */
    public function setFileExtension($fileExtension = self::LOCKFILE_EXT)
    {
        $this->fileExtension = $fileExtension;

        return $this;
    }

    /**
     * Gets file extension
     * @return mixed
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Sets expiry time for the lock file in seconds.
     * @param int $time
     * @return $this|bool
     */
    public function setExpiry($time = 0)
    {
        if ($time <= 0) {
            return false;
        }

        $time = ($time * -1);
        $this->expiry = $time . ' seconds';

        return $this;
    }

    /**
     * Gets the expiry value
     * @return mixed
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * Sets the runtime path
     * @param string $runtimePath
     * @return Locker
     */
    public function setRuntimePath($runtimePath)
    {
        $this->runtimePath = $runtimePath;
        return $this;
    }

    /**
     * Gets the runtime path
     * @return string
     */
    public function getRuntimePath()
    {
        return $this->runtimePath;
    }

    /**
     * Gets the full lockfile path and name
     * @return string
     */
    private function getLockFile()
    {
        return $this->getRuntimePath() . '/' . $this->getName() . $this->getFileExtension();
    }

    /**
     * Checks if lock file exists
     * @return bool
     */
    public function isLocked()
    {
        $this->checkConfig();

        $lockFile = $this->getLockFile();

        if (!file_exists($lockFile)) {
            return false;
        }

        if ($this->getExpiry()) {
            if (filemtime($lockFile) > strtotime($this->getExpiry())) {
                return true;
            } else {
                $this->unlock();
                return false;
            }
        }

        return true;
    }

    /**
     * Creates lock file
     * @return bool
     */
    public function lock()
    {
        $this->checkConfig();

        if (file_exists($this->getLockFile())) {
            return false;
        }

        touch($this->getLockFile());

        return true;
    }

    /**
     * Deletes lock file
     * @return bool
     */
    public function unlock()
    {
        $this->checkConfig();
        $lockFile = $this->getLockFile();

        if (file_exists($lockFile)) {
            unlink($lockFile);

            return true;
        }

        return false;
    }

    /**
     * Required properties check
     * @return bool
     */
    private function checkConfig()
    {
        if (is_null($this->getName())) {
            throw new \InvalidArgumentException('Invalid Task Name');
        }

        return true;
    }
}