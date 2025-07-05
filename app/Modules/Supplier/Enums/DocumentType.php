<?php

namespace App\Modules\Supplier\Enums;

enum DocumentType: string
{
    case CNPJ = 'CNPJ';
    case CPF = 'CPF';
}
