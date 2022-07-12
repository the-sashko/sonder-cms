<?php

namespace Sonder\Models\Shortener\Forms;

use Sonder\CMS\Essentials\BaseForm;
use Sonder\Exceptions\ValuesObjectException;
use Sonder\Interfaces\IModelFormObject;
use Sonder\Models\Shortener\Interfaces\IShortenerForm;

#[IModelFormObject]
#[IShortenerForm]
final class ShortenerForm extends BaseForm implements IShortenerForm
{
    final public const URL_EMPTY_ERROR_MESSAGE = 'URL is empty';

    final public const URL_IS_TOO_LONG_ERROR_MESSAGE = 'URL is too long';

    final public const URL_HAS_BAD_FORMAT_ERROR_MESSAGE = 'URL has bad format';

    final public const SHORTENER_NOT_EXISTS_ERROR_MESSAGE = 'Short link with id "%d" not exists';

    final public const SHORTENER_ALREADY_EXISTS_ERROR_MESSAGE = 'Short link with URL "%s" already exists and has "%s" code';

    private const URL_MAX_LENGTH = 512;

    private const URL_PATTERN = '/^((http)|(https)):\/\/(.*?)\.(.*?)$/su';

    /**
     * @return void
     * @throws ValuesObjectException
     */
    final public function checkInputValues(): void
    {
        $this->setStatusSuccess();

        $this->_validateUrlValue();
    }

    /**
     * @return int|null
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function getUrl(): ?string
    {
        if ($this->has('url')) {
            $url = $this->get('url');

            $url = preg_replace('/(\s+)/u', '', $url);

            return empty($url) ? null : $url;
        }

        return null;
    }

    /**
     * @return string|null
     * @throws ValuesObjectException
     */
    final public function getCode(): ?string
    {
        if ($this->has('code')) {
            $code = $this->get('code');

            $code = preg_replace('/(\s+)/u', '', $code);

            return empty($code) ? null : $code;
        }

        return null;
    }

    /**
     * @return bool
     * @throws ValuesObjectException
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
     * @throws ValuesObjectException
     */
    final public function setId(?int $id = null): void
    {
        $this->set('id', $id);
    }

    /**
     * @param string|null $url
     * @return void
     * @throws ValuesObjectException
     */
    final public function setUrl(?string $url = null): void
    {
        $url = preg_replace('/(\s+)/u', '', $url);

        $this->set('url', $url);
    }

    /**
     * @param string|null $code
     * @return void
     * @throws ValuesObjectException
     */
    final public function setCode(?string $code = null): void
    {
        $code = preg_replace('/(\s+)/u', '', $code);

        $this->set('url', $code);
    }

    /**
     * @param bool $isActive
     * @return void
     * @throws ValuesObjectException
     */
    final public function setIsActive(bool $isActive = false): void
    {
        $this->set('is_active', $isActive);
    }

    /**
     * @return void
     * @throws ValuesObjectException
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
