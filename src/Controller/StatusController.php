<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class StatusController
{
    public function status(): Response
    {
        return new Response(
            '<html><body>It\'s ok</body></html>'
        );
    }
}