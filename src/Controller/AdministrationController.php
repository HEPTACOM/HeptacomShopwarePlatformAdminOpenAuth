<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Controller;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Heptacom\AdminOpenAuth\Contract\AuthorizationUrl\LoginUrlGeneratorInterface;
use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Contract\MetadataClientContract;
use Heptacom\AdminOpenAuth\Contract\OpenAuthenticationFlowInterface;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviourFactoryInterface;
use Heptacom\AdminOpenAuth\Contract\StateFactory\ConfirmStateFactoryInterface;
use Heptacom\AdminOpenAuth\Database\ClientDefinition;
use Heptacom\AdminOpenAuth\Database\ClientEntity;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderRepositoryContract;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Api\Response\ResponseFactoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @RouteScope(scopes={"administration"})
 */
final class AdministrationController extends AbstractController
{
    public function __construct(
        private readonly OpenAuthenticationFlowInterface $flow,
        private readonly ClientLoaderInterface $clientLoader,
        private readonly ConfirmStateFactoryInterface $confirmStateFactory,
        private readonly LoginUrlGeneratorInterface $loginUrlGenerator,
        private readonly RouterInterface $router,
        private readonly RedirectBehaviourFactoryInterface $redirectBehaviourFactory,
    ) {
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     name="api.heptacom.admin_open_auth.client.list",
     *     path="/api/_admin/open-auth/client/list"
     * )
     */
    public function clientList(Context $context): Response
    {
        /** @var AdminApiSource $adminApiSource */
        $adminApiSource = $context->getSource();

        $criteria = new Criteria();
        $criteria->getAssociation('userKeys')
            ->addFilter(
                new EqualsFilter('userId', $adminApiSource->getUserId())
            );

        $clients = $this->flow->getAvailableClients($criteria, $context)
            ->map(static fn (ClientEntity $client): array => [
                'id' => $client->getId(),
                'name' => $client->name,
                'connected' => $client->userKeys?->count() > 0,
            ]);

        return new JsonResponse([
            'data' => \array_values($clients),
        ]);
    }

    /**
     * @Route(
     *     methods={"POST"},
     *     name="api.heptacom.admin_open_auth.remote_connect",
     *     path="/api/_action/open-auth/{clientId}/connect",
     *     defaults={"_acl"={"user_change_me"}}
     * )
     */
    public function remoteConnect(string $clientId, Context $context): Response
    {
        /** @var AdminApiSource $adminApiSource */
        $adminApiSource = $context->getSource();

        return new JsonResponse([
            'target' => $this->flow->getRedirectUrlToConnect(
                $clientId,
                $adminApiSource->getUserId(),
                $this->getSystemContext($context)
            ),
        ]);
    }

    /**
     * @Route(
     *     methods={"POST"},
     *     name="api.heptacom.admin_open_auth.remote_disconnect",
     *     path="/api/_action/open-auth/{clientId}/disconnect",
     *     defaults={"_acl"={"user_change_me"}}
     * )
     */
    public function remoteDisconnect(string $clientId, Context $context): Response
    {
        /** @var AdminApiSource $adminApiSource */
        $adminApiSource = $context->getSource();

        $this->flow->disconnectClient($clientId, $adminApiSource->getUserId(), $this->getSystemContext($context));

        return new JsonResponse();
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     name="api.heptacom.admin_open_auth.confirm",
     *     path="/api/_admin/open-auth/{clientId}/confirm"
     * )
     */
    public function confirmUrl(string $clientId, Context $context): Response
    {
        /** @var AdminApiSource $adminApiSource */
        $adminApiSource = $context->getSource();

        $systemContext = $this->getSystemContext($context);

        $state = $this->confirmStateFactory->create($clientId, $adminApiSource->getUserId(), $systemContext);

        return new JsonResponse([
            'target' => $this->loginUrlGenerator->generate(
                $clientId,
                $state,
                $this->redirectBehaviourFactory->createRedirectBehaviour($clientId, $context),
                $systemContext
            ),
        ]);
    }

    /**
     * @Route(
     *     methods={"POST"},
     *     name="api.heptacom.admin_open_auth.provider.redirect-url",
     *     path="/api/_action/heptacom_admin_open_auth_provider/client-redirect-url"
     * )
     */
    public function getRedirectUrl(Request $request): Response
    {
        $clientId = $request->get('client_id');

        return new JsonResponse([
            'target' => $this->router->generate('administration.heptacom.admin_open_auth.login', [
                'clientId' => $clientId,
            ], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
    }

    /**
     * @Route(
     *     methods={"POST"},
     *     name="api.heptacom.admin_open_auth.provider.metadata-url",
     *     path="/api/_action/heptacom_admin_open_auth_provider/client-metadata-url"
     * )
     */
    public function getMetadataUrl(Request $request, Context $context): Response
    {
        $clientId = $request->get('client_id');

        $client = $this->clientLoader->load($clientId, $context);
        if ($client instanceof MetadataClientContract) {
            $metadataUrl = $this->router->generate('administration.heptacom.admin_open_auth.metadata', [
                'clientId' => $clientId,
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        } else {
            $metadataUrl = null;
        }

        return new JsonResponse([
            'target' => $metadataUrl,
        ]);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     name="api.heptacom.admin_open_auth.provider.list",
     *     path="/api/_action/heptacom_admin_open_auth_provider/list"
     * )
     */
    public function providerList(ClientProviderRepositoryContract $providerRepository): Response
    {
        return new JsonResponse([
            'data' => $providerRepository->getProviderKeys(),
        ]);
    }

    /**
     * @Route(
     *     methods={"POST"},
     *     name="api.heptacom.admin_open_auth.provider.factorize",
     *     path="/api/_action/heptacom_admin_open_auth_provider/factorize"
     * )
     */
    public function createClient(
        Request $request,
        ResponseFactoryInterface $responseFactory,
        ClientDefinition $definition,
        EntityRepository $clientsRepository,
        Context $context
    ): Response {
        $providerKey = $request->get('provider_key');
        $clientId = $this->clientLoader->create($providerKey, $context);
        $criteria = new Criteria();
        $criteria->setIds([$clientId]);
        $entity = $clientsRepository->search($criteria, $context)->first();

        return $responseFactory->createDetailResponse($criteria, $entity, $definition, $request, $context, false);
    }

    private function getSystemContext(Context $context): Context
    {
        return new Context(
            new SystemSource(),
            $context->getRuleIds(),
            $context->getCurrencyId(),
            $context->getLanguageIdChain(),
            $context->getVersionId(),
            $context->getCurrencyFactor(),
            $context->considerInheritance(),
            $context->getTaxState(),
            $context->getRounding()
        );
    }
}
