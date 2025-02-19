<?php

declare(strict_types=1);

namespace Photo\Controller\Plugin;

use Exception;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Paginator;
use Photo\Model\Album;
use Photo\Model\Photo;
use Photo\Service\Album as AlbumService;
use Photo\Service\Photo as PhotoService;

/**
 * This plugin helps with rendering the pages doing album related stuff.
 *
 * @psalm-import-type AlbumArrayType from Album as ImportedAlbumArrayType
 * @psalm-import-type PagesType from Paginator\Paginator as ImportedPagesType
 * @psalm-import-type PhotoArrayType from Photo as ImportedPhotoArrayType
 */
class AlbumPlugin extends AbstractPlugin
{
    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingTraversableTypeHintSpecification
     */
    public function __construct(
        private readonly AlbumService $albumService,
        private readonly PhotoService $photoService,
        private readonly array $photoConfig,
    ) {
    }

    /**
     * Gets an album page, but returns all objects as assoc arrays.
     *
     * @param int $albumId the id of the album
     *
     * @return ?array{
     *     album: ImportedAlbumArrayType,
     *     basedir: string,
     *     photos: ImportedPhotoArrayType[],
     *     albums: ImportedAlbumArrayType[],
     * }
     *
     * @throws Exception
     */
    public function getAlbumAsArray(int $albumId): ?array
    {
        $album = $this->albumService->getAlbum($albumId);

        if (null === $album) {
            return null;
        }

        $albumArray = $album->toArrayWithChildren();

        $photos = $albumArray['photos'];
        $albums = $albumArray['children'];

        $albumArray['photos'] = [];
        $albumArray['children'] = [];

        return [
            'album' => $albumArray,
            'basedir' => $this->photoService->getBaseDirectory(),
            'photos' => $photos,
            'albums' => $albums,
        ];
    }

    /**
     * Gets an album page, but returns all objects as assoc arrays.
     *
     * @param int $albumId    the id of the album
     * @param int $activePage the page of the album
     *
     * @return ?array{
     *     album: ImportedAlbumArrayType,
     *     basedir: string,
     *     pages: ImportedPagesType,
     *     photos: ImportedPhotoArrayType[],
     *     albums: ImportedAlbumArrayType[],
     * }
     *
     * @throws Exception
     */
    public function getAlbumPageAsArray(
        int $albumId,
        int $activePage,
    ): ?array {
        $page = $this->getAlbumPage($albumId, $activePage);

        if (null === $page) {
            return null;
        }

        $paginator = $page['paginator'];
        $photos = [];
        $albums = [];

        foreach ($paginator as $item) {
            if ('album' === $item->getResourceId()) {
                $albums[] = $item->toArray();
            } else {
                $photos[] = $item->toArray();
            }
        }

        return [
            'album' => $page['album']->toArray(),
            'basedir' => $page['basedir'],
            'pages' => $paginator->getPages(),
            'photos' => $photos,
            'albums' => $albums,
        ];
    }

    /**
     * Retrieves all data needed to display a page of an album.
     *
     * @param int    $albumId    the id of the album
     * @param int    $activePage the page of the album
     * @param string $type       "album"|"member"|"year"
     *
     * @return ?array{
     *     album: Album,
     *     basedir: string,
     *     paginator: Paginator\Paginator,
     * }
     *
     * @throws Exception
     */
    public function getAlbumPage(
        int $albumId,
        int $activePage,
        string $type = 'album',
    ): ?array {
        $album = $this->albumService->getAlbum($albumId, $type);

        if (null === $album) {
            return null;
        }

        $paginator = new Paginator\Paginator(
            new AlbumPaginatorAdapter(
                $this->photoService,
                $this->albumService,
                $album,
            ),
        );

        $paginator->setCurrentPageNumber($activePage);
        $paginator->setItemCountPerPage($this->photoConfig['max_photos_page']);
        $basedir = $this->photoService->getBaseDirectory();

        return [
            'album' => $album,
            'basedir' => $basedir,
            'paginator' => $paginator,
        ];
    }
}
