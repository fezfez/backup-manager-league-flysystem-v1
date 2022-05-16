<?php

declare(strict_types=1);

namespace Fezfez\BackupManager;

use Fezfez\BackupManager\Filesystems\BackupManagerFilesystemAdapter;
use Fezfez\BackupManager\Filesystems\BackupManagerRessource;
use Fezfez\BackupManager\Filesystems\CantDeleteFile;
use Fezfez\BackupManager\Filesystems\CantReadFile;
use Fezfez\BackupManager\Filesystems\CantWriteFile;
use League\Flysystem\FilesystemInterface;
use Throwable;

use function sprintf;

final class LeagueFilesystemAdapterV1 implements BackupManagerFilesystemAdapter
{
    private FilesystemInterface $fileSystem;

    public function __construct(FilesystemInterface $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    public function readStream(string $path): BackupManagerRessource
    {
        try {
            return new BackupManagerRessource($this->fileSystem->readStream($path));
        } catch (Throwable $exception) {
            throw new CantReadFile(sprintf('cant read file %s', $path), 0, $exception);
        }
    }

    public function writeStream(string $path, BackupManagerRessource $resource): void
    {
        try {
            if ($this->fileSystem->writeStream($path, $resource->getResource()) !== true) {
                throw new CantWriteFile(sprintf('cant write file %s', $path));
            }
        } catch (Throwable $exception) {
            throw new CantWriteFile(sprintf('cant write file %s', $path), 0, $exception);
        }
    }

    public function delete(string $path): void
    {
        try {
            if ($this->fileSystem->delete($path) !== true) {
                throw new CantDeleteFile(sprintf('cant delete file %s', $path));
            }
        } catch (Throwable $exception) {
            throw new CantDeleteFile(sprintf('cant delete file %s', $path), 0, $exception);
        }
    }
}
