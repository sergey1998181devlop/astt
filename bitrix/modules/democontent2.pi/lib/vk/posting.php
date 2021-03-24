<?php
/**
 * User: Ruslan Semagin
 * Email: pixel.365.24@gmail.com
 * Date: 19.12.2018
 * Time: 18:04
 * Product Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/
 * License Page: http://marketplace.1c-bitrix.ru/solutions/democontent2.pi/#tab-support-link
 */

namespace Democontent2\Pi\VK;

class Posting
{
    const API_VERSION = '5.62';

    /**
     * The application ID
     * @var integer
     */
    private $appId;
    /**
     * The application secret code
     * @var string
     */
    private $secret;
    /**
     * The scope for login URL
     * @var array
     */
    private $scope = [];
    /**
     * The URL to which the user will be redirected
     * @var string
     */
    private $redirect_uri;
    /**
     * The response type of login URL
     * @var string
     */
    private $responceType = 'code';
    /**
     * The current access token
     * @var \StdClass
     */
    private $accessToken;

    /**
     * The Vkontakte instance constructor for quick configuration
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config['access_token'])) {
            $this->setAccessToken(json_encode(['access_token' => $config['access_token']]));
        }
        if (isset($config['app_id'])) {
            $this->setAppId($config['app_id']);
        }
        if (isset($config['secret'])) {
            $this->setSecret($config['secret']);
        }
        if (isset($config['scopes'])) {
            $this->setScope($config['scopes']);
        }
        if (isset($config['redirect_uri'])) {
            $this->setRedirectUri($config['redirect_uri']);
        }
        if (isset($config['response_type'])) {
            $this->setResponceType($config['response_type']);
        }
    }

    /**
     * Get the user id of current access token
     * @return integer
     */
    public function getUserId()
    {
        return $this->accessToken->user_id;
    }

    /**
     * Set the application id
     * @param integer $appId
     * @return Posting
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
        return $this;
    }

    /**
     * Get the application id
     * @return integer
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Set the application secret key
     * @param string $secret
     * @return Posting
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
        return $this;
    }

    /**
     * Get the application secret key
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set the scope for login URL
     * @param array $scope
     * @return Posting
     */
    public function setScope(array $scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * Get the scope for login URL
     * @return array
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Set the URL to which the user will be redirected
     * @param string $redirect_uri
     * @return Posting
     */
    public function setRedirectUri($redirect_uri)
    {
        $this->redirect_uri = $redirect_uri;
        return $this;
    }

    /**
     * Get the URL to which the user will be redirected
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirect_uri;
    }

    /**
     * Set the response type of login URL
     * @param string $responceType
     * @return Posting
     */
    public function setResponceType($responceType)
    {
        $this->responceType = $responceType;
        return $this;
    }

    /**
     * Get the response type of login URL
     * @return string
     */
    public function getResponceType()
    {
        return $this->responceType;
    }

    /**
     * Set the access token
     * @param string $token The access token in json format
     * @return Posting
     */
    public function setAccessToken($token)
    {
        $this->accessToken = json_decode($token);
        return $this;
    }

    /**
     * Get the access token
     * @param string $code
     * @return string The access token in json format
     */
    public function getAccessToken()
    {
        return json_encode($this->accessToken);
    }

    /**
     * Make an API call to https://api.vk.com/method/
     * @return string The response, decoded from json format
     * @throws \Exception
     */
    public function api($method, array $query = [])
    {
        /* Generate query string from array */
        $parameters = [];
        foreach ($query as $param => $value) {
            $q = $param . '=';
            if (is_array($value)) {
                $q .= urlencode(implode(',', $value));
            } else {
                $q .= urlencode($value);
            }
            $parameters[] = $q;
        }
        $q = implode('&', $parameters);
        if (count($query) > 0) {
            $q .= '&';
        }
        $url = 'https://api.vk.com/method/' . $method . '?' . $q . 'access_token=' . $this->accessToken->access_token;
        $result = json_decode($this->curl($url));

        if (isset($result->response)) {
            return $result->response;
        }
        return $result;
    }

    /**
     * Make the curl request to specified url
     * @param string $url The url for curl() function
     * @return mixed The result of curl_exec() function
     * @throws \Exception
     */
    protected function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $result = curl_exec($ch);
        if (!$result) {
            $errno = curl_errno($ch);
            $error = curl_error($ch);
        }

        curl_close($ch);
        if (isset($errno) && isset($error)) {
            throw new \Exception($error, $errno);
        }
        return $result;
    }

    /**
     * @param $publicID int vk group official identifier
     * @param $text string message text
     * @param $fullServerPathToImage string full path to the image file, ex. /var/www/site/img/pic.jpg
     * @param $tags array message tags
     * @return bool true if operation finished successfully and false otherwise
     * @throws \Exception
     */
    public function postToPublic($publicID, $text, $fullServerPathToImage, $tags = [])
    {
        $response = $this->api('photos.getWallUploadServer', [
            'group_id' => $publicID,
            'v' => self::API_VERSION
        ]);

        $uploadURL = $response->upload_url;
        $output = [];
        exec("curl -X POST -F 'photo=@$fullServerPathToImage' '$uploadURL'", $output);
        $response = json_decode($output[0]);

        $response = $this->api('photos.saveWallPhoto', [
            'group_id' => $publicID,
            'photo' => $response->photo,
            'server' => $response->server,
            'hash' => $response->hash,
            'v' => self::API_VERSION
        ]);

        if ($tags) {
            $text .= "\n\n";
        }
        foreach ($tags as $tag) {
            $text .= ' #' . str_replace(' ', '_', $tag);
        }
        $text = html_entity_decode($text);

        $response = $this->api('wall.post',
            [
                'owner_id' => -$publicID,
                'from_group' => 1,
                'message' => $text,
                'attachments' => 'photo' . $response[0]->owner_id . '_' . $response[0]->id,
                'v' => self::API_VERSION
            ]);

        return isset($response->post_id);
    }
}