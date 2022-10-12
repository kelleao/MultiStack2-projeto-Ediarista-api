<?php

namespace App\Http\Controllers;

use App\Http\Hateoas\Index;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __construct(
        private Index $indexHateoas
    ){}

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $links = $this->indexHateoas->links();

        return response()->json([
            'links' => $links
        ]);
    }
}
