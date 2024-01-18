<?php

namespace zonuexe\examples;

use zonuexe\TypeProvider\Json;

#[Json('{"name": "Perfect PHP", "publisher": "Gijutsu-Hyoron Co., Ltd."}')]
final readonly class Book
{
    public function __construct()
    {
    }
}
