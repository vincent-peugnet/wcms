<?php

namespace Wcms;

interface Matchable
{
    public function fullmatch(): string;

    public function options(): string;
}
