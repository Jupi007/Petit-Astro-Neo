<?php

declare(strict_types=1);

namespace Tests\Common;

use App\Tests\Application\Controller\Common\LocaleGetterTraitImplementation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

class LocaleGetterTraitTest extends TestCase
{
    public function testGetLocale(): void
    {
        $locale = 'fr';

        /** @var InputBag<string> */
        $queryInputBag = new InputBag(['locale' => $locale]);

        $request = $this->createMock(Request::class);
        $request->query = $queryInputBag;

        $class = new LocaleGetterTraitImplementation();

        $this->assertSame($locale, $class->getLocaleForTesting($request));
    }
}
