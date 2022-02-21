<?php

namespace Sonder\Models\Shortener;

use Exception;
use Sonder\Core\ModelFormObject;

final class ShortenerForm extends ModelFormObject
{
    const URL_MAX_LENGTH = 512;

    const URL_PATTERN = '/^((http)|(https)):\/\/(.*?)\.(.*?)$/su';

    const URL_EMPTY_ERROR_MESSAGE = 'URL is empty';

    const URL_IS_TOO_LONG_ERROR_MESSAGE = 'URL is too long';

    const URL_HAS_BAD_FORMAT_ERROR_MESSAGE = 'URL has bad format';

    const SHORTENER_NOT_EXISTS_ERROR_MESSAGE = 'Short link with id "%d" not ' .
    'exists';

    const SHORTENER_ALREADY_EXISTS_ERROR_MESSAGE = 'Short link with URL "%s" ' .
    'already exists and has "%s" code';

    /**
     * @throws Exception
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();

        $this->_validateUrlValue();
    }

    /**
     * @return int|null
     * @throws Exception
     */
    final public function getId(): ?int
    {
        if (!$this->has('id')) {
            return null;
        }

        $id = $this->get('id');

        if (empty($id)) {
            return null;
        }

        return (int)$id;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getUrl(): ?string
    {
        if ($this->has('url')) {
            $url = $this->get('url');

            $url = preg_replace('/(\s+)/su', '', $url);

            return empty($url) ? null : $url;
        }

        return null;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    final public function getCode(): ?string
    {
        if ($this->has('code')) {
            $code = $this->get('code');

            $code = preg_replace('/(\s+)/su', '', $code);

            return empty($code) ? null : $code;
        }

        return null;
    }

    /**
     * @return bool
     * @throws Exception
     */
    final public function isActive(): bool
    {
        if (!$this->has('is_active')) {
            return false;
        }

        return (bool)$this->get('is_active');
    }

    /**
     * @param int|null $id
     * @return void
     * @throws Exception
     */
    final public function setId(?int $id = null): void
    {
        $this->set('id', $id);
    }

    /**
     * @param string|null $url
     * @return void
     * @throws Exception
     */
    final public function setUrl(?string $url = null): void
    {
        $url = preg_replace('/(\s+)/su', '', $url);

        $this->set('url', $url);
    }

    /**
     * @param string|null $code
     * @return void
     * @throws Exception
     */
    final public function setCode(?string $code = null): void
    {
        $url = preg_replace('/(\s+)/su', '', $code);

        $this->set('url', $code);
    }

    /**
     * @param bool $isActive
     * @return void
     * @throws Exception
     */
    final public function setIsActive(bool $isActive = false): void
    {
        $this->set('is_active', $isActive);
    }

    /**
     * @return void
     * @throws Exception
     */
    private function _validateUrlValue(): void
    {
        $url = $this->getUrl();

        if (empty($url)) {
            $this->setError(ShortenerForm::URL_EMPTY_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (!empty($url) && mb_strlen($url) > ShortenerForm::URL_MAX_LENGTH) {
            $this->setError(ShortenerForm::URL_IS_TOO_LONG_ERROR_MESSAGE);
            $this->setStatusFail();
        }

        if (
            !empty($url) &&
            !preg_match(ShortenerForm::URL_PATTERN, $url)
        ) {
            $this->setError(
                ShortenerForm::URL_HAS_BAD_FORMAT_ERROR_MESSAGE
            );

            $this->setStatusFail();
        }
    }
}
