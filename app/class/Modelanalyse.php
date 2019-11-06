<?php

namespace Wcms;

class Modelanalyse extends Modelpage
{


	public function __construct() {
		parent::__construct();
    }
    
    public function analyseall()
    {
        $pagelist = $this->getlister();

        $pagelist2 = [];
        foreach ($pagelist as $page) {
            $page->setlinkfrom($this->analyselinkfrom($page));
            $pagelist2[] = $page;
        }
        foreach ($pagelist2 as $page) {
            $page->setlinkto($this->analyselinkto($page->id(), $pagelist));
            $this->update($page);
        }
    }


    public function analyse(Page $page)
	{        
        $page->setlinkfrom($this->analyselinkfrom($page));

        $pagelist = $this->getlister();
        $page->setlinkto($this->analyselinkto($page->id(), $pagelist));

        return $page;
	}




	public function analyselinkto($id, $pagelist)
	{
        //analyse les liens vers cet pageicle en fouillant tout les linkfrom de la bdd, génere un tableau à stocker dans l'pageicle
        $linkto = [];
        foreach ($pagelist as $link) {
            if (in_array($id, $link->linkfrom('array')) && $id != $link->id()) {
                $linkto[] = $link->id();
            }
        }
        return $linkto;
	}

	public function analyselinkfrom(Page $page)
	{
        $linkfrom = [];
        foreach (self::TEXT_ELEMENTS as $element) {
		    preg_match_all('#\]\((\?id=|=)(\w+)\)#', $page->$element(), $out);	
			$linkfrom = array_merge($linkfrom, $out[2]);
        }
        return array_unique($linkfrom);

	}

}







?>