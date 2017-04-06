<?php
declare(strict_types = 1);
namespace AppBundle\Controller;

use GuzzleHttp\Client;
use Proxy\Adapter\Guzzle\GuzzleAdapter;
use Proxy\Filter\RemoveEncodingFilter;
use Proxy\Proxy;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Request;

class CloudLabsController extends BaseController
{
    public function showAction(Request $request)
    {
        return $this->render('cloud_labs/show.html.twig');
    }

    public function proxyAction(Request $request)
    {
        /** @var Client $guzzle */
        $guzzle = $this->get('csa_guzzle.client.proxy');
        $backendDomain = $this->getParameter('v2_backend');
        $proxy = new Proxy(new GuzzleAdapter($guzzle));
        $proxy->filter(new RemoveEncodingFilter());
        $server = $request->server->all();
        $requestUri = preg_replace('/\/_cloudlabs/', '', $request->getPathInfo());
        $server['REQUEST_URI'] = $requestUri;
        $server['SERVER_PORT'] = null;
        $server['host'] = $server['x-bbc-edge-host'];
        $proxRequest = $request->duplicate(null, null, null, null, null, $server);
        $psr7factory = new DiactorosFactory();
        $psrRequest = $psr7factory->createRequest($proxRequest);
        $schema = $server['x-bbc-edge-scheme'];
        if (!in_array($schema, ['http', 'https'])) {
            $schema = 'http';
        }
        $toUrl = $schema . '://' . $backendDomain;
        $response = $proxy->forward($psrRequest)
            ->filter(function ($request, $response, $next) {
                // Manipulate the request object.

                // Call the next item in the middleware.
                $response = $next($request, $response);
                // Manipulate the response object.
                $response = $response->withHeader('X-Programmes-Proxy', '1');

                return $response;
            })
            ->to($toUrl);
        $httpFoundationFactory = new HttpFoundationFactory();
        $symfonyResponse = $httpFoundationFactory->createResponse($response);
        return $symfonyResponse;
    }
}
