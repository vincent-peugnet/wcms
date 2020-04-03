<?php

namespace Wcms;

class Summary extends Item
{
    /** @var string full regex match */
    protected $fullmatch;

    /** @var string full options code line */
    protected $options = '';

    /** @var int Minimum summary level*/
    protected $min = 1;

    /** @var int Maximum summary level*/
    protected $max = 6;

    /** @var array Headers datas */
    protected $sum = [];

    /** @var string Name of element to display */
    protected $element = null;





    public function __construct(array $datas = [])
    {
        $this->hydrate($datas);
        $this->readoptions();
    }


    public function readoptions()
    {
        parse_str(htmlspecialchars_decode($this->options), $datas);
        $this->hydrate($datas);
    }


	/**
	 * Generate a Summary based on header ids. Need to use `$this->headerid` before to scan text
	 *
	 * @return string html list with anchor link
	 */
    public function sumparser()
    {
        $sumstring = '';

        
        foreach ($this->sum as $type => $element) {
            if(!empty($element) && (empty($this->element) || $type === $this->element)) {

                $filteredsum = [];
        
                foreach ($element as $key => $menu) {
                    $deepness = array_keys($menu)[0];
                    if($deepness >= $this->min && $deepness <= $this->max) {
                        $filteredsum[$key] = $menu;
                    }
                }
        
                $last = 0;
                foreach ($filteredsum as $title => $list) {
                    foreach ($list as $h => $link) {
                        if ($h > $last) {
                            for ($i = 1; $i <= ($h - $last); $i++) {
                                $sumstring .= '<ul>' . PHP_EOL;
                            }
                            $sumstring .= '<li><a href="#' . $title . '">' . $link . '</a></li>' . PHP_EOL;
                        } elseif ($h < $last) {
                            for ($i = 1; $i <= ($last - $h); $i++) {
                                $sumstring .= '</ul>' . PHP_EOL;
                            }
                            $sumstring .= '<li><a href="#' . $title . '">' . $link . '</a></li>' . PHP_EOL;
                        } elseif ($h = $last) {
                            $sumstring .= '<li><a href="#' . $title . '">' . $link . '</a></li>' . PHP_EOL;
                        }
                        $last = $h;
                    }
                }
                for ($i = 1; $i <= ($last); $i++) {
                    $sumstring .= '</ul>' . PHP_EOL;
                }

            }
        }
		return $sumstring;
    }



    // __________________________________________________ G E T ____________________________________________________________


    public function fullmatch()
    {
        return $this->fullmatch;
    }

    public function options()
    {
        return $this->options;
    }

    public function element()
    {
        return $this->element;
    }


    // __________________________________________________ S E T ____________________________________________________________


    public function setfullmatch(string $fullmatch)
    {
        $this->fullmatch = $fullmatch;
    }


    public function setoptions(string $options)
    {
        if (!empty($options)) {
            $this->options = $options;
        }
    }

    public function setmin($min)
    {
        $min = intval($min);
        if($min >= 1 && $min <= 6) {
            $this->min = $min;
        }
    }

    public function setmax($max)
    {
        $max = intval($max);
        if($max >= 1 && $max <= 6) {
            $this->max = $max;
        }
    }

    public function setsum(array $sum)
    {
        $this->sum = $sum;
    }

    public function setelement(string $element)
    {
        if(in_array($element, Model::TEXT_ELEMENTS)) {
            $this->element = $element;
        }
    }

}
