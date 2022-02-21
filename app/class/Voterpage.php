<?php

namespace Wcms;

trait Voterpage
{
    public function canedit(): bool
    {
        if ($this->user->issupereditor()) {
            return true;
        } elseif ($this->user->isinvite() || $this->user->iseditor()) {
            if (in_array($this->user->id(), $this->page->authors())) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
