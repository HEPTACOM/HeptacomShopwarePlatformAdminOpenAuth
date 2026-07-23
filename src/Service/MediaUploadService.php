<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Shopware\Core\Content\Media\Event\MediaUploadedEvent;
use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Content\Media\MediaCollection;
use Shopware\Core\Content\Media\MediaException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Util\Hasher;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class MediaUploadService
{
    /**
     * @param EntityRepository<MediaCollection> $mediaRepository
     */
    public function __construct(
        private EntityRepository $mediaRepository,
        private FileSaver $fileSaver,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * Upload a new media file from a local path.
     */
    public function uploadFromLocalPath(
        string $filePath,
        Context $context,
        MediaUploadParameters $params = new MediaUploadParameters()
    ): string {
        $size = \filesize($filePath);

        if ($size === false) {
            throw MediaException::fileNotFound($filePath);
        }

        $media = new MediaFile(
            $filePath,
            \mime_content_type($filePath) ?: '',
            \pathinfo($filePath, \PATHINFO_EXTENSION),
            $size,
            Hasher::hashFile($filePath, 'md5'),
        );

        return $this->upload($media, $context, $params);
    }

    private function upload(MediaFile $media, Context $context, MediaUploadParameters $params): string
    {
        if ($params->deduplicate && $media->getHash() && $existingId = $this->getMediaIdByHash($media->getHash(), $context)) {
            return $existingId;
        }

        $params->fillDefaultFileName($media->getFileName() . '.' . $media->getFileExtension());

        $changedMediaFile = new MediaFile(
            $media->getFileName(),
            $media->getMimeType(),
            $params->getFileNameExtension(),
            $media->getFileSize(),
            $media->getHash()
        );

        $mediaId = $this->createMedia($params, $context);
        try {
            $this->fileSaver->persistFileToMedia(
                $changedMediaFile,
                $params->getFileNameWithoutExtension(),
                $mediaId,
                $context
            );
        } catch (\Throwable $e) {
            // Delete failed upload item
            $context->scope(Context::SYSTEM_SCOPE, function (Context $context) use ($mediaId): void {
                $this->mediaRepository->delete([['id' => $mediaId]], $context);
            });

            throw $e;
        }

        $this->eventDispatcher->dispatch(new MediaUploadedEvent($mediaId, $context));

        return $mediaId;
    }

    private function getMediaIdByHash(string $hash, Context $context): ?string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('fileHash', $hash));

        return $this->mediaRepository->searchIds($criteria, $context)->firstId();
    }

    private function createMedia(MediaUploadParameters $params, Context $context): string
    {
        $id = $params->id ?? Uuid::randomHex();

        $payload = [
            'id' => $id,
            'private' => $params->private ?? false,
        ];

        if ($params->mediaFolderId) {
            $payload['mediaFolderId'] = $params->mediaFolderId;
        }

        $this->mediaRepository->create([$payload], $context);

        return $id;
    }
}
