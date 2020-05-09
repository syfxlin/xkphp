<?php

namespace App\Http;

use App\Exceptions\Http\AlreadyMovedException;
use App\Exceptions\Http\FailMoveFileException;
use App\Exceptions\Http\UploadFailException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use function is_resource;
use function is_string;
use function move_uploaded_file;
use function pathinfo;
use function str_random;

class UploadFile implements UploadedFileInterface
{
    public static $error_msg = [
        UPLOAD_ERR_OK => 'There is no error, the file uploaded with success',
        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
    ];

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $temp_file;

    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $error;

    /**
     * @var StreamInterface|null
     */
    private $stream;

    /**
     * @var bool
     */
    private $moved = false;

    public function __construct(
        $temp_file,
        int $size,
        int $error,
        string $filename,
        string $type
    ) {
        if ($error === UPLOAD_ERR_OK) {
            if (is_string($temp_file)) {
                $this->temp_file = $temp_file;
            }
            if (is_resource($temp_file)) {
                $this->stream = new Stream($temp_file);
            }
        }
        $this->size = $size;
        $this->error = $error;
        $this->filename = $filename;
        $this->type = $type;
    }

    private function checkError(): void
    {
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new UploadFailException(self::$error_msg[$this->error]);
        }
    }

    private function checkMoved(): void
    {
        if ($this->moved) {
            throw new AlreadyMovedException('File already moved');
        }
    }

    /**
     * @inheritDoc
     */
    public function getStream(): StreamInterface
    {
        $this->checkError();
        $this->checkMoved();
        if ($this->stream instanceof StreamInterface) {
            return $this->stream;
        }
        $this->stream = new Stream($this->temp_file);
        return $this->stream;
    }

    /**
     * @inheritDoc
     */
    public function moveTo($targetPath): void
    {
        $this->checkError();
        $this->checkMoved();
        $result = move_uploaded_file($this->temp_file, $targetPath);
        if ($result === false) {
            throw new FailMoveFileException('Failed to move file');
        }
    }

    public function store($path): void
    {
        $this->storeAs(
            $path,
            str_random(10) . '.' . pathinfo($this->filename, PATHINFO_EXTENSION)
        );
    }

    public function storeAs($path, $filename = null): void
    {
        $this->moveTo($path . ($filename ?? $this->filename));
    }

    /**
     * @inheritDoc
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @inheritDoc
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * @inheritDoc
     */
    public function getClientFilename(): string
    {
        return $this->filename;
    }

    /**
     * @inheritDoc
     */
    public function getClientMediaType(): string
    {
        return $this->type;
    }

    public function isValid(): bool
    {
        return $this->error === UPLOAD_ERR_OK;
    }

    public function path(): string
    {
        return $this->temp_file;
    }

    public function name(): string
    {
        return $this->filename;
    }

    public function type(): string
    {
        return $this->type;
    }
}
