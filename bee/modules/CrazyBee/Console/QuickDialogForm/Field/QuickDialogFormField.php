<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Console\QuickDialogForm\Field;


/**
 * QuickDialogFormField
 * @author Lingtalfi
 * 2015-05-09
 *
 */
class QuickDialogFormField
{

    private $name;
    private $label;
    private $dialogType;
    private $validationInfo;
    private $defaultValue;

    public function __construct(array $fields)
    {
        $names = [
            'name' => '',
            'label' => '',
            'dialogType' => null,
            'validationInfo' => null,
            'defaultValue' => '',
        ];

        foreach ($names as $property => $propertyVal) {
            if (null !== $value = array_shift($fields)) {
                $this->$property = $value;
            }
            else {
                $this->$property = $propertyVal;
            }
        }
    }


    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function getDialogType()
    {
        return $this->dialogType;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValidationInfo()
    {
        return $this->validationInfo;
    }


}
