<?php
/**
 * Created by PhpStorm.
 * User: apfba
 * Date: 12/5/2017
 * Time: 11:26 PM
 */

namespace src;

use JsonSerializable;

class APIReturnObject implements JsonSerializable
{
    private $success;
    private $errorCode;
    private $errorMessage;
    private $response;

    public function __construct(
        $success, $errorCode = "", $errorMessage = "", $response
    )
    {
        $this->success = $success;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->response = $response;
    }

    public static function CreateNewAPIReturnObject(
        $success, $errorCode = "", $errorMessage = "", $response
    ) {
        return new APIReturnObject(
            $success, $errorCode = "", $errorMessage = "", $response
        );
    }

    /**
     * @return mixed
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @param mixed $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param string $errorCode
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function returnJSONEncodedAPIObject() {
        return json_encode($this);
    }

    public function jsonSerialize() {
        return [
            'success' => $this->success,
            'errorCode' => $this->errorCode,
            'errorMessage' => $this->errorMessage,
            'response' => $this->response
        ];
    }
}