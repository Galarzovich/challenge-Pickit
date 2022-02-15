<?php


namespace App\Enums;

use BenSampo\Enum\Enum;
final class TipoServicio extends Enum
{
    const ACEITE = 1;
    const FILTRO = 2;
    const CORREA = 3;
    const REVISION_GENERAL = 4;
    const PINTURA = 5;
    const OTRO = 6;
}
