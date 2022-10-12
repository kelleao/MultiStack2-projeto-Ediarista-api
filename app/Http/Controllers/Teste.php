<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TeamPickr\DistanceMatrix\Frameworks\Laravel\DistanceMatrix;
use TeamPickr\DistanceMatrix\Licenses\StandardLicense;


class Teste extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $license = new StandardLicense('AIzaSyDtYP3OMIuQXX8DxZi1EnfCR-0MDxSj24c');
        $response = DistanceMatrix::license($license)
            ->addOrigin('09715-340')
            ->addDestination('02221-000')
            ->request();
            dd($response);
    }
}
