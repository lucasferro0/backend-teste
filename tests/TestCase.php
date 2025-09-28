<?php

namespace Tests;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use WithFaker;
    use CreatesApplication;
    // Não deixaria a herança dessa trait nessa classe de TestCase,
    // visto que pode ter algum cenário de teste que não queria usar transaction e sim o refresh database.
    // Teria que ficar em cada classe de teste, de forma individual.
    use DatabaseTransactions;
}
