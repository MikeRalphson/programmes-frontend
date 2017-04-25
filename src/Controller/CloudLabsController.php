<?php
declare(strict_types = 1);
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

class CloudLabsController extends BaseController
{
    public function __invoke(Request $request)
    {
        $atozService = $this->get('BBC\\ProgrammesPagesService\\Service\\AtozTitlesService');
        $tleos = $atozService->findTleosByFirstLetter('A', 20, 1);
        var_dump($tleos);
        die();
        return $this->render('cloud_labs/show.html.twig');
    }
}
