<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Styling;
use App\Models\StylingImage;

class StylingController extends Controller
{
    public function show(string $id): void
    {
        $styling = (new Styling())->find((int) $id);
        if (!$styling) {
            (new HomeController())->notFound();
            return;
        }
        $this->view('styling/show', [
            'title'   => $styling['title'],
            'styling' => $styling,
            'images'  => (new StylingImage())->forStyling((int) $id),
        ]);
    }
}
