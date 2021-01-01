<?php

namespace App\Controller;

use App\Entity\JsonSerializable;
use Exception;

abstract class AbstractController
{
    /**
     * @param string $template_name
     * @param array $data
     */
    protected function show(string $template_name, array $data = []): void
    {
        require_once(__DIR__ . "/../View/$template_name.tpl");

        exit;
    }

    /**
     * @param Exception|null $error
     * @param array|JsonSerializable $data
     */
    protected function json(?Exception $error = null, $data = []): void
    {
        $data = $this->serializeData($data);

        if ($error !== null) {
            $message = $error->getMessage();
            $data['error'] = $message;
            header('HTTP/1.0 ' . $error->getCode() . " $message");
        }

        header('Content-Type: application/json');

        echo json_encode($data);

        exit;
    }

    /**
     * @param array|JsonSerializable $data
     * @return array
     */
    protected function serializeData($data): array
    {
        if (is_array($data)) {
            $new_data = [];
            foreach ($data as $key => $value) {
                if (is_int($key) && !(is_array($value)) && is_a($value, JsonSerializable::class) && $value->getId()) {
                    $key = $value->getId();
                }
                $new_data[$key] = $this->serializeData($value);
            }
            $data = $new_data;
        } elseif (is_a($data, JsonSerializable::class)) {
            $data = $data->jsonSerialize();
        }
        return $data;
    }
}