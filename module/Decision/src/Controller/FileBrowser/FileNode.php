<?php

declare(strict_types=1);

namespace Decision\Controller\FileBrowser;

use RuntimeException;

use function end;
use function explode;
use function in_array;
use function mb_strtolower;
use function strtolower;

/**
 * Represents a node in a filesystem, which is either a file, or a directory.
 * Immutable.
 */
class FileNode
{
    /**
     * The extension (according to the file name).
     *
     * @var string extension according to the filesystem
     */
    private string $extension;

    /**
     * File extensions per FontAwesome icon.
     *
     * @var array<string, string[]>
     */
    private array $iconExtensions = [
        'fa-file-word' => ['docx', 'docm', 'doc', 'dotx', 'dotm', 'dot', 'odt', 'rtf', 'wpd'],
        'fa-file-powerpoint' => ['pptx', 'pptm', 'ppt', 'potx', 'potm', 'pot', 'ppsx', 'ppsm', 'pps', 'odp', 'ods'],
        'fa-file-excel' => ['xlsx', 'xlsm', 'xls', 'xlsb', 'xltx', 'xltm', 'xlt'],
        'fa-file-csv' => ['csv'], //Only available as solid icon in free version
        'fa-file-pdf' => ['pdf', 'pdfa'],
        'fa-file-video' => [
            'webm',
            'mpg',
            'mp2',
            'mpeg',
            'mpe',
            'mpv',
            'ogg',
            'mp4',
            'm4p',
            'm4v',
            'avi',
            'wmv',
            'mov',
            'qt',
            'flv',
            'swf',
            'avchd',
            'h264',
            'mpeg4',
        ],
        'fa-file-audio' => [
            '3gp',
            'aa',
            'aac',
            'aax',
            'act',
            'aiff',
            'alac',
            'amr',
            'ape',
            'au',
            'awb',
            'dct',
            'dss',
            'dvf',
            'flac',
            'gsm',
            'iklax',
            'ivs',
            'm4a',
            'm4b',
            'm4p',
            'mmf',
            'mp3',
            'mpc',
            'msv',
            'nmf',
            'nsf',
            'ogg, .oga, .mogg',
            'opus',
            'ra, .rm',
            'raw',
            'sln',
            'tta',
            'voc',
            'vox',
            'wav',
            'wma',
            'wv',
            'webm',
            '8svx',
        ],
        'fa-file-image' => [
            'ani',
            'anim',
            'apng',
            'art',
            'bmp',
            'bpg',
            'bsave',
            'cal',
            'cin',
            'cpc',
            'cpt',
            'dds',
            'dpx',
            'ecw',
            'exr',
            'fits',
            'flic',
            'flif',
            'fpx',
            'gif',
            'hdri',
            'hevc',
            'icer',
            'icns',
            'ico / cur',
            'ics',
            'ilbm',
            'jbig',
            'jbig2',
            'jng',
            'jpeg',
            'jpeg-ls',
            'jpeg 2000',
            'jpeg xr',
            'jpeg xt ',
            'jpeg-hdr',
            'jpg',
            'kra',
            'mng',
            'miff',
            'nrrd',
            'pam',
            'pbm',
            'pgm',
            'ppm',
            'pnm',
            'pcx',
            'pgf',
            'pictor',
            'png',
            'psd',
            'psb',
            'psp',
            'qtvr',
            'ras',
            'rgbe ',
            'logluv tiff',
            'sgi',
            'tga',
            'tiff',
            'tiff/ep',
            'tiff/it',
            'ufo',
            'ufp',
            'wbmp',
            'webp',
            'xbm',
            'xcf',
            'xpm',
            'xwd',
        ],
        'fa-file-archive' => [
            '.a',
            'ar',
            'cpio',
            'shar',
            'LBR',
            'iso',
            'lbr',
            'mar',
            'sbx',
            'tar',
            '7z',
            's7z',
            'ace',
            'afa',
            'alz',
            'apk',
            'ar',
            'ark',
            'arc',
            'cdx',
            'arj',
            'b1',
            'b6z',
            'ba',
            'bh',
            'cab',
            'car',
            'cfs',
            'cpt',
            'dar',
            'dd',
            'dgc',
            'dmg',
            'ear',
            'gca',
            'ha',
            'hki',
            'ice',
            'jar',
            'kgb',
            'lzh',
            'lha',
            'lzx',
            'pak',
            'partimg',
            'paq6',
            'paq7',
            'paq8',
            'pea',
            'pim',
            'pit',
            'qda',
            'rar',
            'rk',
            'sda',
            'sea',
            'sen',
            'sfx',
            'shk',
            'sit',
            'sitx',
            'sqx',
            'tar.gz',
            'tgz',
            'tar.Z',
            'tar.bz2',
            'tbz2',
            'tar.lzma',
            'tlz',
            'tar.xz',
            'txz',
            'uc',
            'uc0',
            'uc2',
            'ucn',
            'ur2',
            'ue2',
            'uca',
            'uha',
            'war',
            'wim',
            'xar',
            'xp3',
            'yz1',
            'zip',
            'zipx',
            'zoo',
            'zpaq',
            'zz',
        ],
        'fa-file-alt' => ['txt', 'tex', 'text', 'ini', 'md'],
        'fa-copy' => ['bak', 'tmp', 'dmp'],
        'fa-folder' => ['folder'],
        'fa-archive' => ['folder-archive'],
    ];

    public function __construct(
        private readonly string $kind,
        private readonly string $fullPath,
        private readonly string $name,
    ) {
        if (
            'dir' !== $this->kind
            && 'file' !== $this->kind
        ) {
            throw new RuntimeException('Kind is not supported.');
        }

        if ('dir' === $this->kind) {
            if ('archive' === mb_strtolower($this->name)) {
                $this->extension = 'folder-archive';
            } else {
                $this->extension = 'folder';
            }
        } else {
            $filenameSplitted = explode('.', $name);
            $this->extension = strtolower(end($filenameSplitted));
        }
    }

    /**
     * Gets kind.
     *
     * @return string either 'file' or 'dir'
     */
    public function getKind(): string
    {
        return $this->kind;
    }

    /**
     * Gets path of parent.
     *
     * @return string valid path
     */
    public function getFullPath(): string
    {
        return $this->fullPath;
    }

    /**
     * Name of file or dir.
     *
     * @return string valid name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Extension of file or dir.
     *
     * @return string valid name
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * Get corresponding FontAwesome file icon.
     *
     * @return string valid FontAwesome 5 icon
     */
    public function getFileIcon(): string
    {
        foreach ($this->iconExtensions as $icon => $extensions) {
            if (in_array($this->getExtension(), $extensions)) {
                return $icon;
            }
        }

        return 'fa-file';
    }
}
