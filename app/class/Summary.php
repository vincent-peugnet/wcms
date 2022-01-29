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

    /** @var array[] Headers datas */
    protected $sum = [];

    /** @var string|null Name of element to display */
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
        // check if a element is specified
        if (!is_null($this->element) && isset($this->sum[$this->element])) {
            $headers = $this->sum[$this->element()];
        } else {
            $headers = flatten($this->sum);
        }

        $sumstring = '';
        $minlevel = $this->min - 1;
        $prevlevel = $minlevel;

        foreach ($headers as $header) {
            if ($header->level < $this->min || $header->level > $this->max) {
                // not in the accepted range, skiping this header.
                continue;
            };
            for ($i = $header->level; $i > $prevlevel; $i--) {
                $class = $i === $this->min ? ' class="summary"' : '';
                $sumstring .= "<ul$class><li>";
            }
            for ($i = $header->level; $i < $prevlevel; $i++) {
                $sumstring .= '</li></ul>';
            }
            if ($header->level <= $prevlevel) {
                $sumstring .= '</li><li>';
            }
            $sumstring .= "<a href=\"#$header->id\">$header->title</a>";
            $prevlevel = $header->level;
        }
        for ($i = $minlevel; $i < $prevlevel; $i++) {
            $sumstring .= "</li></ul>";
        }
        return $sumstring;
    }



    // ________________________________________________ G E T ________________________________________________________


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


    // ________________________________________________ S E T ________________________________________________________


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
        if ($min >= 1 && $min <= 6) {
            $this->min = $min;
        }
    }

    public function setmax($max)
    {
        $max = intval($max);
        if ($max >= 1 && $max <= 6) {
            $this->max = $max;
        }
    }

    public function setsum(array $sum)
    {
        $this->sum = $sum;
    }

    public function setelement(string $element)
    {
        if (in_array($element, Model::TEXT_ELEMENTS)) {
            $this->element = $element;
        }
    }
}
