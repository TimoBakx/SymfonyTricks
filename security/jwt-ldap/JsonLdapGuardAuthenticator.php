<?php
declare(strict_types=1);

namespace App\Authentication\Ldap;

use LdapTools\Bundle\LdapToolsBundle\Security\LdapGuardAuthenticator;
use Symfony\Component\HttpFoundation\Request;

class JsonLdapGuardAuthenticator extends LdapGuardAuthenticator
{
    /**
     * @var array
     */
    private $requestContent;

    /**
     * @param Request $request
     *
     * @return array|null|false
     */
    private function getContent(Request $request)
    {
        if ($this->requestContent === null) {
            $this->requestContent = json_decode($request->getContent(), true);
        }

        return $this->requestContent;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        $content = $this->getContent($request);
        return $content !== null && $content !== false;
    }

    /**
     * @param Request $request
     * @return null|string
     */
    protected function getRequestDomain(Request $request): ?string
    {
        return null;
    }

    /**
     * @param string $param
     * @param Request $request
     * @return string|null
     */
    protected function getRequestParameter($param, Request $request): ?string
    {
        $content = $this->getContent($request);

        if (isset($content[$param])) {
            return $content[$param];
        }

        return null;
    }
}
