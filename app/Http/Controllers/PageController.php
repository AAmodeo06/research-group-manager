<?php

namespace App\Http\Controllers;
namespace App\Http\projects;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $projectCount = Project::count();
        return view('home', ['projectCount' => $projectCount]);        
    }

    public function about()
    {
        return '<h2>Chi siamo</h2>
                <p>Questo gestionale supporta la gestione di progetti e pubblicazioni del gruppo IVU Lab.</p>';
    }

    public function contact()
    {
        return '<h2>Contatti</h2>
                <p>Email: <a href="mailto:info@uniba.it">info@uniba.it</a></p>';
    }

    public function projects()
    {
        $projects = ['MORPHEUS', 'HERACLE', 'ATHENA'];
        $html = '<h2>Progetti di Ricerca</h2><ul>';
        foreach ($projects as $p) {
            $html .= '<li>' . $p . '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    public function showProject($name)
    {
        return '<h2>Dettagli progetto: ' . ucfirst($name) . '</h2>';
    }
}
