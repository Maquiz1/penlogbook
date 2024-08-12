<?php
include 'dompdf/autoload.inc.php';
require 'php/core/init.php';

use Dompdf\Dompdf;

class Pdf extends Dompdf
{
    public function __construct()
    {
        parent::__construct();
    }
}

