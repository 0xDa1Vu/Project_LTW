<?php
namespace App\Controllers;

use App\Core\Controller;

class PageController extends Controller
{
    public function about(): void
    {
        $this->view('pages/about', ['title' => 'About']);
    }

    public function care(): void
    {
        $this->view('pages/care', ['title' => 'Whenever Care']);
    }

    public function faq(): void
    {
        $this->view('pages/faq', ['title' => 'FAQ']);
    }
}
