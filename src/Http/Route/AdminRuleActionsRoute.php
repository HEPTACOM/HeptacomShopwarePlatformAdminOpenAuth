<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route;

use Heptacom\AdminOpenAuth\Contract\RuleActionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminRuleActionsRoute extends AbstractController
{
    public function __construct(
        #[AutowireIterator('heptacom_open_auth.rule_action')]
        private readonly iterable $actions,
    ) {
    }

    #[Route(
        path: '/api/_action/heptacom_admin_open_auth_rule_actions/list',
        name: 'api.heptacom.admin_open_auth.rule_actions.list',
        defaults: [
            '_routeScope' => ['administration'],
        ],
        methods: ['GET']
    )]
    public function getActions(): Response
    {
        $actions = \array_map(static fn (RuleActionInterface $action): array => [
            'name' => $action->getName(),
            'component' => $action->getActionConfigurationComponent(),
        ], \iterator_to_array($this->actions));

        return new JsonResponse($actions);
    }
}
