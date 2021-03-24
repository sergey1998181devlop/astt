<?php

/**
 * The MIT License
 *
 * Copyright (c) 2017 NBCO Yandex.Money LLC
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace YandexCheckout;

use Psr\Log\LoggerInterface;
use YandexCheckout\Common\Exceptions\ApiException;
use YandexCheckout\Common\Exceptions\BadApiRequestException;
use YandexCheckout\Common\Exceptions\ForbiddenException;
use YandexCheckout\Common\Exceptions\JsonException;
use YandexCheckout\Common\Exceptions\InternalServerError;
use YandexCheckout\Common\Exceptions\NotFoundException;
use YandexCheckout\Common\Exceptions\ResponseProcessingException;
use YandexCheckout\Common\Exceptions\TooManyRequestsException;
use YandexCheckout\Common\Exceptions\UnauthorizedException;
use YandexCheckout\Common\HttpVerb;
use YandexCheckout\Common\LoggerWrapper;
use YandexCheckout\Common\ResponseObject;
use YandexCheckout\Helpers\Config\ConfigurationLoader;
use YandexCheckout\Helpers\Config\ConfigurationLoaderInterface;
use YandexCheckout\Helpers\TypeCast;
use YandexCheckout\Helpers\UUID;
use YandexCheckout\Model\PaymentInterface;
use YandexCheckout\Request\PaymentOptionsRequest;
use YandexCheckout\Request\PaymentOptionsRequestInterface;
use YandexCheckout\Request\PaymentOptionsRequestSerializer;
use YandexCheckout\Request\PaymentOptionsResponse;
use YandexCheckout\Request\Payments\CreatePaymentRequest;
use YandexCheckout\Request\Payments\CreatePaymentRequestInterface;
use YandexCheckout\Request\Payments\CreatePaymentResponse;
use YandexCheckout\Request\Payments\CreatePaymentRequestSerializer;
use YandexCheckout\Request\Payments\Payment\CancelResponse;
use YandexCheckout\Request\Payments\Payment\CreateCaptureRequest;
use YandexCheckout\Request\Payments\Payment\CreateCaptureRequestInterface;
use YandexCheckout\Request\Payments\Payment\CreateCaptureRequestSerializer;
use YandexCheckout\Request\Payments\Payment\CreateCaptureResponse;
use YandexCheckout\Request\Payments\PaymentResponse;
use YandexCheckout\Request\Payments\PaymentsRequest;
use YandexCheckout\Request\Payments\PaymentsRequestInterface;
use YandexCheckout\Request\Payments\PaymentsRequestSerializer;
use YandexCheckout\Request\Payments\PaymentsResponse;
use YandexCheckout\Request\Refunds\CreateRefundRequest;
use YandexCheckout\Request\Refunds\CreateRefundRequestInterface;
use YandexCheckout\Request\Refunds\CreateRefundRequestSerializer;
use YandexCheckout\Request\Refunds\CreateRefundResponse;
use YandexCheckout\Request\Refunds\RefundResponse;
use YandexCheckout\Request\Refunds\RefundsRequest;
use YandexCheckout\Request\Refunds\RefundsRequestInterface;
use YandexCheckout\Request\Refunds\RefundsRequestSerializer;
use YandexCheckout\Request\Refunds\RefundsResponse;

/**
 * ����� ������� API
 *
 * @package YandexCheckout
 *
 * @since 1.0.1
 */
class Client
{
    /**
     * ������� ������ ����������
     */
    const SDK_VERSION = '1.0.3';

    /**
     * ��� HTTP ���������, ������������� ��� �������� idempotence key
     */
    const IDEMPOTENCY_KEY_HEADER = 'Idempotence-Key';

    /**
     * �������� �� ��������� ������� �������� ����� ��������� ��� �������� ���������� ������� � ������ ���������
     * ������ � HTTP �������� 202
     */
    const DEFAULT_DELAY = 1800;

    /**
     * �������� �� ��������� ���������� ������� ��������� ���������� �� API ���� ������ ����� � HTTP �������� 202
     */
    const DEFAULT_TRIES_COUNT = 3;

    /**
     * �������� �� ��������� ���������� ������� ��������� ���������� �� API ���� ������ ����� � HTTP �������� 202
     */
    const DEFAULT_ATTEMPTS_COUNT = 3;

    /**
     * @var null|Client\ApiClientInterface
     */
    protected $apiClient;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var array
     */
    private $config;

    /**
     * ����� ����� ������� ����� �������������� ��������� �������
     * �������� �� ��������� - 1800 �����������.
     * @link https://kassa.yandex.ru/docs/checkout-api/?php#asinhronnost
     * @var int �������� � �������������
     */
    private $timeout;

    /**
     * ���������� ��������� �������� ��� ������ API �������� 202
     * �������� �� ��������� 3
     * @link https://kassa.yandex.ru/docs/checkout-api/?php#asinhronnost
     * @var int
     */
    private $attempts;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * Constructor
     *
     * @param Client\ApiClientInterface|null $apiClient
     * @param ConfigurationLoaderInterface|null $configLoader
     *
     * @internal-param null|ConfigurationLoader $config
     */
    public function __construct(
        Client\ApiClientInterface $apiClient = null,
        ConfigurationLoaderInterface $configLoader = null
    ) {
        if ($apiClient === null) {
            $apiClient = new Client\CurlClient();
        }

        if ($configLoader === null) {
            $configLoader = new ConfigurationLoader();
            $config       = $configLoader->load()->getConfig();
            $this->setConfig($config);
            $apiClient->setConfig($config);
        }
        $this->attempts  = self::DEFAULT_ATTEMPTS_COUNT;
        $this->apiClient = $apiClient;
    }

    /**
     * @param $login
     * @param $password
     *
     * @return Client $this
     */
    public function setAuth($login, $password)
    {
        $this->login    = $login;
        $this->password = $password;

        $this->apiClient
            ->setShopId($this->login)
            ->setShopPassword($this->password);

        return $this;
    }

    /**
     * @return Client\ApiClientInterface
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * @param Client\ApiClientInterface $apiClient
     *
     * @return Client
     */
    public function setApiClient(Client\ApiClientInterface $apiClient)
    {
        $this->apiClient = $apiClient;
        $this->apiClient->setConfig($this->config);
        $this->apiClient->setLogger($this->logger);

        return $this;
    }

    /**
     * ������������� ������ ����������
     *
     * @param null|callable|object|LoggerInterface $value ������� �������
     */
    public function setLogger($value)
    {
        if ($value === null || $value instanceof LoggerInterface) {
            $this->logger = $value;
        } else {
            $this->logger = new LoggerWrapper($value);
        }
        if ($this->apiClient !== null) {
            $this->apiClient->setLogger($this->logger);
        }
    }

    /**
     * ��������� ������� ������.
     * ����������� ���� �����, ����� �������� ������� ������ � ��������, ��������� ��� ������ ������.
     *
     * @param PaymentOptionsRequestInterface|array $paymentOptionsRequest
     *
     * @return PaymentOptionsResponse
     */
    public function getPaymentOptions($paymentOptionsRequest = null)
    {
        $path = "/payment_options";

        if ($paymentOptionsRequest === null) {
            $queryParams = array();
        } else {
            if (is_array($paymentOptionsRequest)) {
                $paymentOptionsRequest = PaymentOptionsRequest::builder()->build($paymentOptionsRequest);
            }
            $serializer  = new PaymentOptionsRequestSerializer();
            $queryParams = $serializer->serialize($paymentOptionsRequest);
        }

        $response = $this->execute($path, HttpVerb::GET, $queryParams);

        $result = null;
        if ($response->getCode() == 200) {
            $responseArray = $this->decodeData($response);
            $result        = new PaymentOptionsResponse($responseArray);
        } else {
            $this->handleError($response);
        }

        return $result;
    }

    /**
     * �������� ������ �������� ��������.
     *
     * @param PaymentsRequestInterface|array|null $filter
     *
     * @return PaymentsResponse
     */
    public function getPayments($filter = null)
    {
        $path = '/payments';

        if ($filter === null) {
            $queryParams = array();
        } else {
            if (is_array($filter)) {
                $filter = PaymentsRequest::builder()->build($filter);
            }
            $serializer  = new PaymentsRequestSerializer();
            $queryParams = $serializer->serialize($filter);
        }

        $response = $this->execute($path, HttpVerb::GET, $queryParams);

        $paymentResponse = null;
        if ($response->getCode() == 200) {
            $responseArray   = $this->decodeData($response);
            $paymentResponse = new PaymentsResponse($responseArray);
        } else {
            $this->handleError($response);
        }

        return $paymentResponse;
    }

    /**
     * �������� �������.
     *
     * ����� ������� ������, ���������� ������� ������ ������� � `Payment`. �� �������� ��� ����������� ����������
     * ��� ���������� ������ (�����, ������ � ������). � ������� �������� ��������� ����, �� ���������������
     * ��������� �� ������� � ������.
     *
     * ���������� ������� ���� �� ����������:
     * <ul>
     * <li>payment_token � ������ �� ������������ PaymentToken, ��������������� �������� Yandex.Checkout JS;</li>
     * <li>payment_method_id � ������ �� ����������� ��������� ������;</li>
     * <li>payment_method_data � ������ �� ����� ��������� ������.</li>
     * </ul>
     *
     * ���� �� ������ �� ���� �������� � `confirmation.type = redirect`, �� � �������� `confirmation_url`
     * ������������ ������, �� ������� ������������ ������ �������������� ������� ���������� ������ ������.
     * �������������� ���������:
     * <ul>
     * <li>confirmation � ����������, ���� ���������� �������� ������ ������������� �������;</li>
     * <li>recipient � ����������� ��� ������� ���������� �������;</li>
     * <li>metadata � �������������� ������ (���������� ���������).</li>
     * </ul>
     *
     * @param CreatePaymentRequestInterface|array $payment
     * @param string $idempotencyKey {@link https://kassa.yandex.ru/docs/checkout-api/?php#idempotentnost}
     *
     * @return CreatePaymentResponse
     */
    public function createPayment($payment, $idempotencyKey = null)
    {
        $path = '/payments';

        $headers = array();

        if ($idempotencyKey) {
            $headers[self::IDEMPOTENCY_KEY_HEADER] = $idempotencyKey;
        } else {
            $headers[self::IDEMPOTENCY_KEY_HEADER] = UUID::v4();
        }
        if (is_array($payment)) {
            $payment = CreatePaymentRequest::builder()->build($payment);
        }

        $serializer     = new CreatePaymentRequestSerializer();
        $serializedData = $serializer->serialize($payment);
        $httpBody       = $this->encodeData($serializedData);

        $response = $this->execute($path, HttpVerb::POST, null, $httpBody, $headers);

        $paymentResponse = null;
        if ($response->getCode() == 200) {
            $resultArray     = $this->decodeData($response);
            $paymentResponse = new CreatePaymentResponse($resultArray);
        } else {
            $this->handleError($response);
        }

        return $paymentResponse;
    }

    /**
     * �������� ���������� � �������
     *
     * ������ ������ ������� {@link PaymentInterface} �� ��� ����������� ��������������.
     *
     * @param string $paymentId
     *
     * @return PaymentInterface
     */
    public function getPaymentInfo($paymentId)
    {
        if ($paymentId === null) {
            throw new \InvalidArgumentException('Missing the required parameter $paymentId');
        } elseif (!TypeCast::canCastToString($paymentId)) {
            throw new \InvalidArgumentException('Invalid paymentId value: string required');
        } elseif (strlen($paymentId) !== 36) {
            throw new \InvalidArgumentException('Invalid paymentId value');
        }

        $path = '/payments/'.$paymentId;

        $response = $this->execute($path, HttpVerb::GET, null);

        $result = null;
        if ($response->getCode() == 200) {
            $resultArray = $this->decodeData($response);
            $result      = new PaymentResponse($resultArray);
        } else {
            $this->handleError($response);
        }

        return $result;
    }

    /**
     * ������������� �������
     *
     * ������������ ���� ���������� ������� ������. ������ ����� �����������, ������ ���� �� ���������
     * � ������� `waiting_for_capture`. ���� ������ ����������� ������� � ������, ������ ������, � �� ������ ������
     * ����� ��� ������� ������ ������������. �� ��������� ���� ����� ������������� ������ ������� � ������,
     * � ������.����� ��������� ������ �� ��� ��������� ����. ���� �� �� ������������� ������ �� �������, ����������
     * � `expire_at`, �� ��������� �� ����������, � ������ ������������ ������������. ��� ������ ���������� ������
     * � ��� ���� 7 ���� �� ������������� �������. ��� ��������� �������� ������ ������ ���������� �����������
     * � ������� 6 �����.
     *
     * @param CreateCaptureRequestInterface|array $captureRequest
     * @param $paymentId
     * @param $idempotencyKey {@link https://kassa.yandex.ru/docs/checkout-api/?php#idempotentnost}
     *
     * @return CreateCaptureResponse
     */
    public function capturePayment($captureRequest, $paymentId, $idempotencyKey = null)
    {
        if ($paymentId === null) {
            throw new \InvalidArgumentException('Missing the required parameter $paymentId');
        } elseif (!TypeCast::canCastToString($paymentId)) {
            throw new \InvalidArgumentException('Invalid paymentId value: string required');
        } elseif (strlen($paymentId) !== 36) {
            throw new \InvalidArgumentException('Invalid paymentId value');
        }

        $path = '/payments/'.$paymentId.'/capture';

        $headers = array();

        if ($idempotencyKey) {
            $headers[self::IDEMPOTENCY_KEY_HEADER] = $idempotencyKey;
        } else {
            $headers[self::IDEMPOTENCY_KEY_HEADER] = UUID::v4();
        }
        if (is_array($captureRequest)) {
            $captureRequest = CreateCaptureRequest::builder()->build($captureRequest);
        }

        $serializer     = new CreateCaptureRequestSerializer();
        $serializedData = $serializer->serialize($captureRequest);
        $httpBody       = $this->encodeData($serializedData);

        $response = $this->execute($path, HttpVerb::POST, null, $httpBody, $headers);

        $result = null;
        if ($response->getCode() == 200) {
            $resultArray = $this->decodeData($response);
            $result      = new CreateCaptureResponse($resultArray);
        } else {
            $this->handleError($response);
        }

        return $result;
    }

    /**
     * �������� ������������� ������ ������.
     *
     * �������� ������, ����������� � ������� `waiting_for_capture`. ������ ������� ������, ��� ��
     * �� ������ ������ ������������ ����� ��� ������� ������. ��� ������ �� ��������� ������, �� ��������
     * ���������� ������ �� ���� �����������. ��� �������� ����������� ������� ������ ���������� ���������.
     * ��� ��������� �������� ������ ������� ����� �������� �� ���������� ����.
     *
     * @param $paymentId
     * @param $idempotencyKey {@link https://kassa.yandex.ru/docs/checkout-api/?php#idempotentnost}
     *
     * @return CancelResponse
     */
    public function cancelPayment($paymentId, $idempotencyKey = null)
    {
        if ($paymentId === null) {
            throw new \InvalidArgumentException('Missing the required parameter $paymentId');
        } elseif (!TypeCast::canCastToString($paymentId)) {
            throw new \InvalidArgumentException('Invalid paymentId value: string required');
        } elseif (strlen($paymentId) !== 36) {
            throw new \InvalidArgumentException('Invalid paymentId value');
        }

        $path    = '/payments/'.$paymentId.'/cancel';
        $headers = array();
        if ($idempotencyKey) {
            $headers[self::IDEMPOTENCY_KEY_HEADER] = $idempotencyKey;
        } else {
            $headers[self::IDEMPOTENCY_KEY_HEADER] = UUID::v4();
        }

        $response = $this->execute($path, HttpVerb::POST, null, null, $headers);

        $result = null;
        if ($response->getCode() == 200) {
            $resultArray = $this->decodeData($response);
            $result      = new CancelResponse($resultArray);
        } else {
            $this->handleError($response);
        }

        return $result;
    }

    /**
     * �������� ������ ��������� ��������
     *
     * @param RefundsRequestInterface|array|null $filter
     *
     * @return RefundsResponse
     */
    public function getRefunds($filter = null)
    {
        $path = '/refunds';

        if ($filter === null) {
            $queryParams = array();
        } else {
            if (is_array($filter)) {
                $filter = RefundsRequest::builder()->build($filter);
            }
            $serializer  = new RefundsRequestSerializer();
            $queryParams = $serializer->serialize($filter);
        }

        $response = $this->execute($path, HttpVerb::GET, $queryParams);

        $refundsResponse = null;
        if ($response->getCode() == 200) {
            $resultArray     = $this->decodeData($response);
            $refundsResponse = new RefundsResponse($resultArray);
        } else {
            $this->handleError($response);
        }

        return $refundsResponse;
    }

    /**
     * ���������� �������� �������
     *
     * ������� ������ �������� � `Refund`. ���������� ������� ����������� ������ �� ����������� ��������������
     * ����� �������. �������� �������� �������� ������ ��� �������� � ������� `succeeded`. �������� �� ����������
     * �������� ���. ��������, ������� ������.����� ���� �� ���������� ��������� �������, �� ������������.
     *
     * @param CreateRefundRequestInterface|array $request
     * @param null $idempotencyKey {@link https://kassa.yandex.ru/docs/checkout-api/?php#idempotentnost}
     *
     * @return CreateRefundResponse
     */
    public function createRefund($request, $idempotencyKey = null)
    {
        $path = '/refunds';

        $headers = array();

        if ($idempotencyKey) {
            $headers[self::IDEMPOTENCY_KEY_HEADER] = $idempotencyKey;
        } else {
            $headers[self::IDEMPOTENCY_KEY_HEADER] = UUID::v4();
        }
        if (is_array($request)) {
            $request = CreateRefundRequest::builder()->build($request);
        }

        $serializer     = new CreateRefundRequestSerializer();
        $serializedData = $serializer->serialize($request);
        $httpBody       = $this->encodeData($serializedData);

        $response = $this->execute($path, HttpVerb::POST, null, $httpBody, $headers);

        $result = null;
        if ($response->getCode() == 200) {
            $resultArray = $this->decodeData($response);
            $result      = new CreateRefundResponse($resultArray);
        } else {
            $this->handleError($response);
        }

        return $result;
    }

    /**
     * �������� ���������� � ��������
     *
     * @param $refundId
     *
     * @return RefundResponse
     */
    public function getRefundInfo($refundId)
    {
        if ($refundId === null) {
            throw new \InvalidArgumentException('Missing the required parameter $refundId');
        } elseif (!TypeCast::canCastToString($refundId)) {
            throw new \InvalidArgumentException('Invalid refundId value: string required');
        } elseif (strlen($refundId) !== 36) {
            throw new \InvalidArgumentException('Invalid refundId value');
        }
        $path = '/refunds/'.$refundId;

        $response = $this->execute($path, HttpVerb::GET, null);

        $result = null;
        if ($response->getCode() == 200) {
            $resultArray = $this->decodeData($response);
            $result      = new RefundResponse($resultArray);
        } else {
            $this->handleError($response);
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * ��������� �������� �������� ����� ���������� ���������
     *
     * @param int $timeout
     *
     * @return Client
     */
    public function setRetryTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * ��������� �������� ���������� ������� ��������� �������� ��� ������� 202
     *
     * @param int $attempts
     *
     * @return Client
     */
    public function setMaxRequestAttempts($attempts)
    {
        $this->attempts = $attempts;

        return $this;
    }

    /**
     * @param $serializedData
     *
     * @return string
     * @throws \Exception
     */
    private function encodeData($serializedData)
    {
        $result = json_encode($serializedData);
        if ($result === false) {
            $errorCode = json_last_error();
            throw new JsonException("Failed serialize json.", $errorCode);
        }

        return $result;
    }

    /**
     * @param ResponseObject $response
     *
     * @return array
     */
    private function decodeData(ResponseObject $response)
    {
        $resultArray = json_decode($response->getBody(), true);
        if ($resultArray === null) {
            throw new JsonException('Failed to decode response', json_last_error());
        }

        return $resultArray;
    }

    /**
     * @param ResponseObject $response
     *
     * @throws ApiException
     * @throws BadApiRequestException
     * @throws ForbiddenException
     * @throws InternalServerError
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     */
    private function handleError(ResponseObject $response)
    {
        switch ($response->getCode()) {
            case BadApiRequestException::HTTP_CODE:
                throw new BadApiRequestException($response->getHeaders(), $response->getBody());
                break;
            case ForbiddenException::HTTP_CODE:
                throw new ForbiddenException($response->getHeaders(), $response->getBody());
                break;
            case UnauthorizedException::HTTP_CODE:
                throw new UnauthorizedException($response->getHeaders(), $response->getBody());
                break;
            case InternalServerError::HTTP_CODE:
                throw new InternalServerError($response->getHeaders(), $response->getBody());
                break;
            case NotFoundException::HTTP_CODE:
                throw new NotFoundException($response->getHeaders(), $response->getBody());
                break;
            case TooManyRequestsException::HTTP_CODE:
                throw new TooManyRequestsException($response->getHeaders(), $response->getBody());
                break;
            case ResponseProcessingException::HTTP_CODE:
                throw new ResponseProcessingException($response->getHeaders(), $response->getBody());
                break;
            default:
                if ($response->getCode() > 399) {
                    throw new ApiException(
                        'Unexpected response error code',
                        $response->getCode(),
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
        }
    }

    /**
     * �������� ����� ���������� ���������
     *
     * @param $response
     */
    protected function delay($response)
    {
        $timeout      = $this->timeout;
        $responseData = $this->decodeData($response);
        if ($timeout) {
            $delay = $timeout;
        } else {
            if (isset($responseData['retry_after'])) {
                $delay = $responseData['retry_after'];
            } else {
                $delay = self::DEFAULT_DELAY;
            }
        }
        usleep($delay * 1000);
    }

    /**
     * ���������� ������� � ��������� 202 �������
     *
     * @param $path
     * @param $method
     * @param $queryParams
     * @param null $httpBody
     * @param array $headers
     *
     * @return ResponseObject
     */
    private function execute($path, $method, $queryParams, $httpBody = null, $headers = array())
    {
        $attempts = $this->attempts;
        $response = $this->apiClient->call($path, $method, $queryParams, $httpBody, $headers);

        while ($response->getCode() == 202 && $attempts > 0) {
            $this->delay($response);
            $attempts--;
            $response = $this->apiClient->call($path, $method, $queryParams, $httpBody, $headers);
        }

        return $response;
    }
}