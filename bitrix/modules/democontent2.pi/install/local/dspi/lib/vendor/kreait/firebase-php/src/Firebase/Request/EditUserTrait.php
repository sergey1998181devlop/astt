<?php

declare(strict_types=1);

namespace Kreait\Firebase\Request;

use Kreait\Firebase\Exception\InvalidArgumentException;
use Kreait\Firebase\Value\ClearTextPassword;
use Kreait\Firebase\Value\Email;
use Kreait\Firebase\Value\PhoneNumber;
use Kreait\Firebase\Value\Uid;
use Kreait\Firebase\Value\Url;

/**
 * @codeCoverageIgnore
 */
trait EditUserTrait
{
    /**
     * @var Uid|null
     */
    protected $uid;

    /**
     * @var Email|null
     */
    protected $email;

    /**
     * @var string|null
     */
    protected $displayName;

    /**
     * @var bool|null
     */
    protected $emailIsVerified;

    /**
     * @var PhoneNumber|null
     */
    protected $phoneNumber;

    /**
     * @var Url|null
     */
    protected $photoUrl;

    /**
     * @var bool|null
     */
    protected $markAsEnabled;

    /**
     * @var bool|null
     */
    protected $markAsDisabled;

    /**
     * @var ClearTextPassword|null
     */
    protected $clearTextPassword;

    /**
     * @param static $request
     *
     * @throws InvalidArgumentException when invalid properties have been provided
     *
     * @return static
     */
    protected static function withEditableProperties($request, array $properties)
    {
        foreach ($properties as $key => $value) {
            switch (\mb_strtolower(\preg_replace('/[^a-z]/i', '', $key))) {
                case 'uid':
                case 'localid':
                    $request = $request->withUid($value);
                    break;
                case 'email':
                    $request = $request->withEmail($value);
                    break;
                case 'unverifiedemail':
                    $request = $request->withUnverifiedEmail($value);
                    break;
                case 'verifiedemail':
                    $request = $request->withVerifiedEmail($value);
                    break;
                case 'emailverified':
                    if ($value === true) {
                        $request = $request->markEmailAsVerified();
                    } elseif ($value === false) {
                        $request = $request->markEmailAsUnverified();
                    }
                    break;
                case 'displayname':
                    $request = $request->withDisplayName($value);
                    break;
                case 'phone':
                case 'phonenumber':
                    $request = $request->withPhoneNumber($value);
                    break;
                case 'photo':
                case 'photourl':
                    $request = $request->withPhotoUrl($value);
                    break;
                case 'disableuser':
                case 'disabled':
                case 'isdisabled':
                    if ($value === true) {
                        $request = $request->markAsDisabled();
                    } elseif ($value === false) {
                        $request = $request->markAsEnabled();
                    }
                    break;
                case 'enableuser':
                case 'enabled':
                case 'isenabled':
                    if ($value === true) {
                        $request = $request->markAsEnabled();
                    } elseif ($value === false) {
                        $request = $request->markAsDisabled();
                    }
                    break;
                case 'password':
                case 'cleartextpassword':
                    $request = $request->withClearTextPassword($value);
                    break;
            }
        }

        return $request;
    }

    /**
     * @param Uid|mixed $uid
     *
     * @return static
     */
    public function withUid($uid)
    {
        $request = clone $this;
        $request->uid = $uid instanceof Uid ? $uid : new Uid((string) $uid);

        return $request;
    }

    /**
     * @param Email|string $email
     *
     * @return static
     */
    public function withEmail($email)
    {
        $request = clone $this;
        $request->email = $email instanceof Email ? $email : new Email($email);

        return $request;
    }

    /**
     * @param Email|string $email
     *
     * @return static
     */
    public function withVerifiedEmail($email)
    {
        $request = clone $this;
        $request->email = $email instanceof Email ? $email : new Email($email);
        $request->emailIsVerified = true;

        return $request;
    }

    /**
     * @param Email|string $email
     *
     * @return static
     */
    public function withUnverifiedEmail($email)
    {
        $request = clone $this;
        $request->email = $email instanceof Email ? $email : new Email($email);
        $request->emailIsVerified = false;

        return $request;
    }

    /**
     * @return static
     */
    public function withDisplayName(string $displayName)
    {
        $request = clone $this;
        $request->displayName = $displayName;

        return $request;
    }

    /**
     * @param string|PhoneNumber $phoneNumber
     *
     * @return static
     */
    public function withPhoneNumber($phoneNumber)
    {
        if ($phoneNumber) {
            $phoneNumber = $phoneNumber instanceof PhoneNumber
                ? $phoneNumber
                : new PhoneNumber($phoneNumber);
        }
        $request = clone $this;
        $request->phoneNumber = $phoneNumber;

        return $request;
    }

    /**
     * @param Url|string $url
     *
     * @return static
     */
    public function withPhotoUrl($url)
    {
        $request = clone $this;
        $request->photoUrl = $url instanceof Url ? $url : Url::fromValue($url);

        return $request;
    }

    /**
     * @return static
     */
    public function markAsDisabled()
    {
        $request = clone $this;
        $request->markAsEnabled = null;
        $request->markAsDisabled = true;

        return $request;
    }

    /**
     * @return static
     */
    public function markAsEnabled()
    {
        $request = clone $this;
        $request->markAsDisabled = null;
        $request->markAsEnabled = true;

        return $request;
    }

    /**
     * @return static
     */
    public function markEmailAsVerified()
    {
        $request = clone $this;
        $request->emailIsVerified = true;

        return $request;
    }

    /**
     * @return static
     */
    public function markEmailAsUnverified()
    {
        $request = clone $this;
        $request->emailIsVerified = false;

        return $request;
    }

    /**
     * @param ClearTextPassword|string $clearTextPassword
     *
     * @return static
     */
    public function withClearTextPassword($clearTextPassword)
    {
        $request = clone $this;
        $request->clearTextPassword = $clearTextPassword instanceof ClearTextPassword
            ? $clearTextPassword
            : new ClearTextPassword($clearTextPassword);

        return $request;
    }

    public function prepareJsonSerialize(): array
    {
        $disableUser = null;
        if ($this->markAsDisabled) {
            $disableUser = true;
        } elseif ($this->markAsEnabled) {
            $disableUser = false;
        }

        $data = \array_filter([
            'localId' => $this->uid,
            'disableUser' => $disableUser,
            'displayName' => $this->displayName,
            'email' => $this->email,
            'emailVerified' => $this->emailIsVerified,
            'phoneNumber' => $this->phoneNumber,
            'photoUrl' => $this->photoUrl,
            'password' => $this->clearTextPassword,
        ], static function ($value) {
            return $value !== null;
        });

        return $data;
    }

    public function hasUid(): bool
    {
        return (bool) $this->uid;
    }
}
