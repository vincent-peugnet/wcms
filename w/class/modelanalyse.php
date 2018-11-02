<?php



class Modelanalyse extends Modelart
{


	public function __construct() {
		parent::__construct();
    }
    
    public function analyseall()
    {
        $artlist = $this->getlister();

        $artlist2 = [];
        foreach ($artlist as $art) {
            $art->setlinkfrom($this->analyselinkfrom($art));
            $artlist2[] = $art;
        }
        foreach ($artlist2 as $art) {
            $art->setlinkto($this->analyselinkto($art->id(), $artlist));
            $this->update($art);
        }
    }


    public function analyse(Art2 $art)
	{        
        $art->setlinkfrom($this->analyselinkfrom($art));

        $artlist = $this->getlister();
        $art->setlinkto($this->analyselinkto($art->id(), $artlist));

        return $art;
	}




	public function analyselinkto($id, $artlist)
	{
        //analyse les liens vers cet article en fouillant tout les linkfrom de la bdd, génere un tableau à stocker dans l'article
        $linkto = [];
        foreach ($artlist as $link) {
            if (in_array($id, $link->linkfrom('array')) && $id != $link->id()) {
                $linkto[] = $link->id();
            }
        }
        return $linkto;
	}

	public function analyselinkfrom(Art2 $art)
	{
        $linkfrom = [];
        foreach (self::TEXT_ELEMENTS as $element) {
		    preg_match_all('#\]\((\?id=|=)(\w+)\)#', $art->$element(), $out);	
			$linkfrom = array_merge($linkfrom, $out[2]);
        }
        return array_unique($linkfrom);

	}

}







?>