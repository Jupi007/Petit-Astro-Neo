<?php

declare(strict_types=1);

namespace Tests\Common;

use App\Tests\Unit\Controller\Common\RequestActionGetterTraitImplementation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

class RequestActionGetterTraitTest extends TestCase
{
    public function testGetLocale(): void
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
