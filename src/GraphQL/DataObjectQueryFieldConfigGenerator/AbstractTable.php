<?php


namespace Pimcore\Bundle\DataHubBundle\GraphQL\DataObjectQueryFieldConfigGenerator;


use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pimcore\Bundle\DataHubBundle\GraphQL\Service;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Fieldcollection\Definition;

abstract class AbstractTable extends Base
{
    /**
     * @param $attribute
     * @param Data $fieldDefinition
     * @param null $class
     * @param null $container
     * @return mixed
     */
    public function getGraphQlFieldConfig($attribute, Data $fieldDefinition, $class = null, $container = null)
    {
        return $this->enrichConfig($fieldDefinition, $class, $attribute, [
            'name' => $fieldDefinition->getName(),
            'type' => $this->getFieldType($fieldDefinition, $class, $container),
            'resolve' => function ($value = null, $args = [], $context, ResolveInfo $resolveInfo = null) use ($fieldDefinition, $attribute) {
                $result = Service::resolveValue($value, $fieldDefinition, $attribute, $args);
                if ($result === null) {
                    return [];
                }

                /** @var \Pimcore\Model\DataObject\Data\StructuredTable $result */
                $rows = ($fieldDefinition instanceof Data\StructuredTable) ? $result->getData() : $result;

                foreach ($rows as &$row) {
                    $row = array_combine(
                        array_map(
                            function ($k){
                                return is_numeric($k) ? 'col'. $k : $k;
                            },
                            array_keys($row)
                        ),
                        $row
                    );
                }
                return $rows;
            }
        ], $container);
    }

    /**
     * @param Data $fieldDefinition
     * @param null $class
     * @param null $container
     * @return ListOfType
     */
    public function getFieldType(Data $fieldDefinition, $class = null, $container = null)
    {
        if ($class instanceof Definition) {
            $name = 'fieldcollection_' . $class->getKey() . '_' . $fieldDefinition->getName();
        } else if ($class instanceof \Pimcore\Model\DataObject\Objectbrick\Definition) {
            $name = 'objectbrick_' . $class->getKey() . '_' . $fieldDefinition->getName();
        } else {
            $name = 'object_' . $class->getName() . '_' . $fieldDefinition->getName();
        }

        foreach ($this->getTableColumnKeys($fieldDefinition) as $key) {
            $fields[$key] = Type::string();
        }

        $type = new ObjectType(
            [
                'name' => $name,
                'fields' => $fields
            ]
        );
        return Type::listOf($type);
    }

    /**
     * @param Data $fieldDefinition
     * @return array
     */
    abstract function getTableColumnKeys(Data $fieldDefinition): array;
}