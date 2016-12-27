<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Rules;

use CoreShop\Exception;
use CoreShop\Model\AbstractModel;
use Pimcore\Tool;

/**
 * Class AbstractRule
 * @package CoreShop\Model\Rules
 */
abstract class AbstractRule extends AbstractModel
{
    /**
     * possible types of a condition.
     *
     * @var array
     */
    public static $availableConditions = [];

    /**
     * possible types of a action.
     *
     * @var array
     */
    public static $availableActions = [];

    /**
     * @var string
     */
    public static $type;

    /**
     * @var string
     */
    public $name;


    /**
     * @var array
     */
    public $conditions = [];

    /**
     * @var array
     */
    public $actions = [];

    /**
     * @param $actions
     * @param $actionNamespace
     * @return array
     *
     * @throws \CoreShop\Exception
     */
    public function prepareActions($actions, $actionNamespace)
    {
        $actionInstances = [];


        foreach ($actions as $action) {
            $class = $actionNamespace.ucfirst($action['type']);

            if (Tool::classExists($class)) {
                $instance = new $class();
                $instance->setValues($action);

                $actionInstances[] = $instance;
            } else {
                throw new Exception(sprintf('Action with type %s in namespace %s not found', $action['type'], $actionNamespace));
            }
        }

        return $actionInstances;
    }

    /**
     * @param $conditions
     * @param $conditionNamespace
     * @return mixed
     * @throws \CoreShop\Exception
     */
    public function prepareConditions($conditions, $conditionNamespace)
    {
        $conditionInstances = [];

        foreach ($conditions as $condition) {
            $class = $conditionNamespace.ucfirst($condition['type']);

            if (Tool::classExists($class)) {
                if ($condition['type'] === "conditions") {
                    $nestedConditions = static::prepareConditions($condition['conditions'], $conditionNamespace);
                    $condition['conditions'] = $nestedConditions;
                }

                $instance = new $class();
                $instance->setValues($condition);

                $conditionInstances[] = $instance;
            } else {
                throw new Exception(sprintf('Condition with type %s in namespace %s not found', $condition['type'], $conditionNamespace));
            }
        }

        return $conditionInstances;
    }

    /**
     * Add Condition Type.
     *
     * @param $condition
     */
    public static function addCondition($condition)
    {
        if (!in_array($condition, static::$availableConditions)) {
            static::$availableConditions[] = $condition;
        }
    }

    /**
     * Add Action Type.
     *
     * @param $action
     */
    public static function addAction($action)
    {
        if (!in_array($action, static::$availableActions)) {
            static::$availableActions[] = $action;
        }
    }

    /**
     * @return array
     */
    public static function getAvailableConditions()
    {
        return static::$availableConditions;
    }

    /**
     * @return array
     */
    public static function getAvailableActions()
    {
        return static::$availableActions;
    }

    /**
     * @return string
     */
    public static function getType()
    {
        return static::$type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s (%s)", $this->getName(), $this->getId());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param array $conditions
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
    }
}
