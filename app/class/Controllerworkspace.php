<?php

namespace Wcms;

class Controllerworkspace extends Controller
{
    public function update()
    {
        if ($this->user->isinvite()) {
            $_SESSION['workspace']['showeditorrightpanel'] = isset($_POST['showeditorrightpanel']);
            $_SESSION['workspace']['showeditorleftpanel'] = isset($_POST['showeditorleftpanel']);
            if (isset($_POST['fontsize'])) {
                $fontsize = intval($_POST['fontsize']);
                if ($fontsize >= 5 && $fontsize <= 100) {
                    $_SESSION['workspace']['fontsize'] = $fontsize;
                }
            }
        }
        if (isset($_POST['page'])) {
            $this->routedirect('pageedit', ['page' => $_POST['page']]);
        } else {
            $this->routedirect('home');
        }
    }
}
