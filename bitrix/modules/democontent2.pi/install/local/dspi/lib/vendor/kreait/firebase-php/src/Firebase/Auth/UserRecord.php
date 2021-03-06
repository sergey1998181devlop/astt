<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth;

use DateTimeImmutable;
use Kreait\Firebase\Util\DT;
use Kreait\Firebase\Util\JSON;

class UserRecord implements \JsonSerializable
{
    /**
     * @var string
     */
    public $uid;

    /**
     * @var string|null
     */
    public $email;

    /**
     * @var bool
     */
    public $emailVerified = false;

    /**
     * @var string|null
     */
    public $displayName;

    /**
     * @var string|null
     */
    public $photoUrl;

    /**
     * @var string|null
     */
    public $phoneNumber;

    /**
     * @var bool
     */
    public $disabled;

    /**
     * @var UserMetaData
     */
    public $metadata;

    /**
     * @var UserInfo[]
     */
    public $providerData;

    /**
     * @var string|null
     */
    public $passwordHash;

    /**
     * @var array
     */
    public $customAttributes;

    /**
     * @var DateTimeImmutable|null
     */
    public $tokensValidAfterTime;

    public function __construct()
    {
    }

    public static function fromResponseData(array $data): self
    {
        $record = new self();
        $record->uid = $data['localId'];
        $record->email = $data['email'] ?? null;
        $record->emailVerified = $data['emailVerified'] ?? false;
        $record->displayName = $data['displayName'] ?? null;
        $record->photoUrl = $data['photoUrl'] ?? null;
        $record->phoneNumber = $data['phoneNumber'] ?? null;
        $record->disabled = $data['disabled'] ?? false;
        $record->metadata = self::userMetaDataFromResponseData($data);
        $record->providerData = self::userInfoFromResponseData($data);
        $record->passwordHash = $data['passwordHash'] ?? null;

        if ($data['validSince'] ?? null) {
            $record->tokensValidAfterTime = DT::toUTCDateTimeImmutable($data['validSince']);
        }

        if ($customAttributes = $data['customAttributes'] ?? '{}') {
            $record->customAttributes = JSON::decode($customAttributes, true);
        }

        return $record;
    }

    private static function userMetaDataFromResponseData(array $data): UserMetaData
    {
        return UserMetaData::fromResponseData($data);
    }

    private static function userInfoFromResponseData(array $data): array
    {
        return \array_map(static function (array $userInfoData) {
            return UserInfo::fromResponseData($userInfoData);
        }, $data['providerUserInfo'] ?? []);
    }

    public function toArray(): array
    {
        return \get_object_vars($this);
    }

    public function jsonSerialize()
    {
        $data = $this->toArray();
        $data['metadata'] = $this->metadata->jsonSerialize();

        if ($data['tokensValidAfterTime']) {
            $data['tokensValidAfterTime'] = $data['tokensValidAfterTime']->format(\DATE_ATOM);
        }

        return $data;
    }
}
