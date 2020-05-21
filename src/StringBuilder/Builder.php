<?php declare(strict_types=1);

namespace Initx\StringBuilder;

use Initx\StringBuilder\Exception\IndexOutOfBoundsException;
use IntlChar;
use function mb_strlen;
use function mb_substr;
use function strrev;

class Builder
{
    /**
     * @var string
     */
    private $str = '';

    /**
     * @param string|mixed $str
     */
    public function __construct($str = '')
    {
        $this->insert(0, $str);
    }

    public function create($str = '')
    {
        return new self($str);
    }

    /**
     * @param mixed $str
     * @param int|null $start
     * @param int|null $end
     * @return self
     */
    public function append($str, ?int $start = null, ?int $end = null): self
    {
        $this->insert(mb_strlen($this->str), $str, $start, $end);

        return $this;
    }

    public function appendCodePoint(int $codePoint): self
    {
        $this->append(IntlChar::chr($codePoint));

        return $this;
    }

    /**
     * @param int $offset
     * @param string|mixed $str
     * @param int|null $start
     * @param int|null $end
     * @return self
     */
    public function insert(int $offset, $str, ?int $start = null, ?int $end = null): self
    {
        $start = $start ?? 0;

        if ($start < 0) {
            throw new IndexOutOfBoundsException('Start must not be negative');
        }

        $len = null;

        if ($end !== null) {
            $len = $end - $start;
        }

        if ($start > $end) {
            throw new IndexOutOfBoundsException('Start must not be greater than end');
        }

        $str = (string)$str;

        if ($end > mb_strlen($str)) {
            throw new IndexOutOfBoundsException('End must not be greater than str length');
        }

        $str = mb_substr($str, $start, $len);

        $pre = mb_substr($this->str, 0, $offset);
        $post = mb_substr($this->str, $offset);

        $this->str = $pre . $str . $post;

        return $this;
    }

    public function toString(): string
    {
        return $this->str;
    }

    /**
     * @param string|mixed $str
     * @param int $offset
     * @return int
     */
    public function lastIndexOf($str, int $offset = 0): int
    {
        $pos = mb_strrpos($this->str, (string)$str, $offset);

        return $pos !== false ? $pos : -1;
    }

    public function reverse(): self
    {
        $this->str = strrev($this->str);

        return $this;
    }

    public function length(): int
    {
        return mb_strlen($this->str);
    }

    public function substring(int $start, int $end): string
    {
        $len = $this->length();

        if ($start < 0) {
            throw new IndexOutOfBoundsException('Start must not be negative');
        }
        if ($end < 0) {
            throw new IndexOutOfBoundsException('End must not be negative');
        }
        if ($start > $len) {
            throw new IndexOutOfBoundsException('Start must not be greater than length');
        }
        if ($end > $len) {
            throw new IndexOutOfBoundsException('End must not be greater than length');
        }
        if ($start > $end) {
            throw new IndexOutOfBoundsException('Start must not be greater than end');
        }

        return mb_substr($this->str, $start, $end - $start);
    }
}