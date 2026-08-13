<?php

namespace App\Services;

use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToRetrieveMetadata;

class GoogleDriveAdapterWrapper implements FilesystemAdapter
{
    public function __construct(protected FilesystemAdapter $adapter) {}

    public function fileExists(string $path): bool
    {
        try {
            return $this->adapter->fileExists($path);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function directoryExists(string $path): bool
    {
        try {
            return $this->adapter->directoryExists($path);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function write(string $path, string $contents, Config $config): void
    {
        try {
            $this->adapter->write($path, $contents, $config);
        } catch (\Throwable $e) {
            $dir = dirname($path);
            if ($dir && $dir !== '.') {
                try {
                    $this->adapter->createDirectory($dir, $config);
                } catch (\Throwable $ignored) {
                }
            }
            $this->adapter->write($path, $contents, $config);
        }
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        try {
            $this->adapter->writeStream($path, $contents, $config);
        } catch (\Throwable $e) {
            $dir = dirname($path);
            if ($dir && $dir !== '.') {
                try {
                    $this->adapter->createDirectory($dir, $config);
                } catch (\Throwable $ignored) {
                }
            }
            $this->adapter->writeStream($path, $contents, $config);
        }
    }

    public function read(string $path): string
    {
        return $this->adapter->read($path);
    }

    public function readStream(string $path)
    {
        return $this->adapter->readStream($path);
    }

    public function delete(string $path): void
    {
        try {
            $this->adapter->delete($path);
        } catch (\Throwable $e) {
        }
    }

    public function deleteDirectory(string $path): void
    {
        try {
            $this->adapter->deleteDirectory($path);
        } catch (\Throwable $e) {
        }
    }

    public function createDirectory(string $path, Config $config): void
    {
        try {
            $this->adapter->createDirectory($path, $config);
        } catch (\Throwable $e) {
        }
    }

    public function setVisibility(string $path, string $visibility): void
    {
        try {
            $this->adapter->setVisibility($path, $visibility);
        } catch (\Throwable $e) {
        }
    }

    public function visibility(string $path): FileAttributes
    {
        try {
            return $this->adapter->visibility($path);
        } catch (\Throwable $e) {
            throw UnableToRetrieveMetadata::visibility($path, $e->getMessage());
        }
    }

    public function mimeType(string $path): FileAttributes
    {
        try {
            return $this->adapter->mimeType($path);
        } catch (\Throwable $e) {
            throw UnableToRetrieveMetadata::mimeType($path, $e->getMessage());
        }
    }

    public function lastModified(string $path): FileAttributes
    {
        try {
            return $this->adapter->lastModified($path);
        } catch (\Throwable $e) {
            throw UnableToRetrieveMetadata::lastModified($path, $e->getMessage());
        }
    }

    public function fileSize(string $path): FileAttributes
    {
        try {
            return $this->adapter->fileSize($path);
        } catch (\Throwable $e) {
            throw UnableToRetrieveMetadata::fileSize($path, $e->getMessage());
        }
    }

    public function listContents(string $path, bool $deep): iterable
    {
        try {
            $contents = $this->adapter->listContents($path, $deep);
            foreach ($contents as $item) {
                yield $item;
            }
        } catch (\Throwable $e) {
            return;
        }
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $this->adapter->move($source, $destination, $config);
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        $this->adapter->copy($source, $destination, $config);
    }
}
