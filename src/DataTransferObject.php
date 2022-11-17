<?php

namespace SoftInvest\DTO;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class DataTransferObject
{
    /**
     * @throws ReflectionException
     */
    public function __construct(array $arr = [])
    {
        if ($arr) {
            $this->hydrate($arr);
        }
    }

    /**
     * @throws ReflectionException
     */
    public function hydrate(array $arr): void
    {
        $reflect = new ReflectionClass($this);
        foreach ($arr as $field => $value) {
            $prop = $reflect->getProperty($field);

            $fieldValue = match ((string)$prop->getType()) {
                'bool', '?bool' => (bool)$value,
                'int', '?int' => (int)$value,
                'float', '?float' => (float)$value,
                'string', '?string' => (string)$value,
                default => $value
            };

            $className = str_replace('?', '', (string)$prop->getType());
            if (class_exists($className)) {
                $this->$field = new $className($value);
            } else {
                $this->$field = $fieldValue;
            }
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        $arr = [];
        foreach ($props as $prop) {
            /**
             * @var ReflectionProperty $prop
             */
            $arr[$prop->getName()] = $this->{$prop->getName()};
        }

        return $arr;
    }
}
