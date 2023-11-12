<?php

declare(strict_types=1);

namespace Tests\Common;

use App\Tests\Application\Controller\Common\RequestActionGetterTraitImplementation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

class RequestActionGetterTraitTest extends TestCase
{
    public function testGetRequestAction(): void
    {
        $action = 'get';

        /** @var InputBag<string> */
        $queryInputBag = new InputBag(['action' => $action]);

        $request = $this->createMock(Request::class);
        $request->query = $queryInputBag;

        $class = new RequestActionGetterTraitImplementation();

        $this->assertSame($action, $class->getRequestActionForTesting($request));
    }
}
