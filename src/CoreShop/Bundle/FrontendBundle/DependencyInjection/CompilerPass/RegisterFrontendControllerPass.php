<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Bundle\FrontendBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterFrontendControllerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $controllers = $container->getParameter('coreshop.frontend.controllers');

        foreach ($controllers as $key => $value) {
            $controllerKey = sprintf('coreshop.frontend.controller.%s', $key);
            $controllerClass = $container->getParameter($controllerKey);

            if ($container->hasDefinition($controllerClass)) {
                $customController = $container->getDefinition($controllerClass);

                $customController->addMethodCall('setContainer', [new Reference('service_container')]);
                $customController->addMethodCall('setTemplateConfigurator', [new Reference('coreshop.frontend.template_configurator')]);

                $container->setDefinition($controllerKey, $customController)->setPublic(true);

                continue;
            }

            $controllerDefinition = new Definition($controllerClass);
            $controllerDefinition->addMethodCall('setContainer', [new Reference('service_container')]);
            $controllerDefinition->addMethodCall('setTemplateConfigurator', [new Reference('coreshop.frontend.template_configurator')]);
            $controllerDefinition->setPublic(true);

            switch ($key) {
                case 'security':
                    $controllerDefinition->setArguments([
                        new Reference('security.authentication_utils'),
                        new Reference('form.factory'),
                        new Reference('coreshop.context.shopper'),
                    ]);
                break;

                case 'checkout':
                    $controllerDefinition->setArguments([
                        new Reference('coreshop.checkout_manager.factory')
                    ]);
                    break;

                case 'payment':
                    $controllerDefinition->setMethodCalls([
                        ['setContainer', [new Reference('service_container')]]
                    ]);
                    $controllerDefinition->setArguments([
                        new Reference('coreshop.order.payment_provider'),
                        new Reference('coreshop.repository.order'),
                        new Reference('coreshop.factory.payum_get_status'),
                        new Reference('coreshop.factory.payum_resolve_next_route'),
                        new Reference('coreshop.factory.payum_confirm_order'),
                    ]);
                    break;
            }

            $container->setDefinition($controllerKey, $controllerDefinition);
        }
    }
}
