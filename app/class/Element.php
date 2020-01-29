<?php

namespace Wcms;

use Exception;

class Element extends Item
{
    protected $fullmatch;
    protected $type;
    protected $options;
    protected $sources = [];
    protected $params = [];
    protected $autolink = 0;
    protected $markdown = 1;
    protected $content = '';

    const OPTIONS = ['autolink', 'markdown'];



    // __________________________________________________ F U N ____________________________________________________________



	public function __construct($datas = [], $pageid)
	{
        $this->hydrate($datas);
        $this->analyse($pageid);
	}

    private function analyse(string $pageid)
    {
        if(!empty($this->options)) {
            
            // Replace "!" by the real page name
            $this->options = str_replace('!', $pageid, $this->options);

            preg_match('~(:([a-z0-9-_+!]+))?(\/([a-z0-9-,_+=]+))?~', $this->options, $matches);
            if(isset($matches[2]) && !empty($matches[2])) {
                $this->sources = explode('+', $matches[2]);
            } else {
                $this->sources[] = $pageid;
            }
            if(isset($matches[4])) {
                $this->params = explode(',', $matches[4]);
            }

            $this->readoptions();

        } else {
            $this->sources[] = $pageid;
        }
    }
    
    private function readoptions()
    {
        if(!empty($this->params)) {
            foreach ($this->params as $param ) {
                preg_match('~([a-z0-9-_]+)(=(-?[0-9]+))?~', $param, $optionmatch);
                if(isset($optionmatch[1])) {
                    $key = $optionmatch[1];
                }
                if(isset($optionmatch[3])) {
                    $value = $optionmatch[3];
                } else {                    
                    $read = '<h2>Rendering error :</h2><p>Paramaters must have a value like : <strong><code>/' . $key . '=__value__</code></strong> for parameter : <strong><code>' . $key . '</code></strong></p>';
                    //throw new Exception($read);
                }
                $method = 'set' . $key;
                if (in_array($key, self::OPTIONS)) {
                    if (!$this->$method($value)) {
                        $read = '<h2>Rendering error :</h2><p>Invalid value input : <strong><code>' . $value . '</code></strong> for parameter : <strong><code>' . $key . '</code></strong></p>';
                        //throw new Exception($read);

                    }
                } else {
                    $read = '<h2>Rendering error :</h2><p>Parameter name : <strong><code>' . $optionmatch[1] . '</code></strong> does not exist<p>';
                    //throw new Exception($read);
                }
            }
        }
    }

    public function addtags()
    {
        $this->content = PHP_EOL . '<' . $this->type() . '>' . PHP_EOL . $this->content() . PHP_EOL . '</' . $this->type() . '>' . PHP_EOL;
    }





    // __________________________________________________ G E T ____________________________________________________________


    public function fullmatch()
    {
        return $this->fullmatch;
    }

    public function type()
    {
        return $this->type;
    }

    public function options()
    {
        return $this->options;
    }

    public function params()
    {
        return $this->params;
    }

    public function sources()
    {
        return $this->sources;
    }

    public function autolink()
    {
        return $this->autolink;
    }

    public function markdown()
    {
        return $this->markdown;
    }

    public function content()
    {
        return $this->content;
    }






    // __________________________________________________ S E T ____________________________________________________________


    public function setfullmatch(string $fullmatch)
    {
        $this->fullmatch = $fullmatch;
    }

    public function settype(string $type)
    {
        $type = strtolower($type);
        if(in_array($type, Model::TEXT_ELEMENTS)) {
            $this->type = $type;
        }
    }

    public function setoptions(string $options)
    {
        if(!empty($options)) {
            $this->options = $options;
        }
    }

    public function setautolink(int $level)
    {
        if($level >= 0 && $level <= 16) {
            $this->autolink = $level;
            return true;
        } else {
            return false;
        }
    }

    public function setmarkdown(int $level)
    {
        if($level >= 0 && $level <= 1) {
            $this->markdown = $level;
            return true;
        } else {
            return false;
        }
    }

    public function setcontent(string $content)
    {
        $this->content = $content;
    }

}




?>