<?php

class Medialist
{
    /** @var string full regex match */
    protected $fullmatch;
    /** @var string options */
    protected $options = '';
    /** @var string directory of media */
    protected $path = '';
    /** @var string */
    protected $sortby = 'id';
    /** @var string */
    protected $order = 1;
    /** @var string */
    protected $recursive = 'id';

    const SORT_BY_OPTIONS = ['id', 'size'];



    // __________________________________________________ F U N ____________________________________________________________



	public function __construct(array $datas = [])
	{
        $this->hydrate($datas);
        $this->readoptions();

	}

	public function hydrate($datas)
	{
		foreach ($datas as $key => $value) {
			$method = 'set' . $key;

			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
    }

    public function readoptions()
    {
        parse_str($this->options, $datas);
        $this->hydrate($datas);
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



    // __________________________________________________ S E T ____________________________________________________________


    public function setfullmatch(string $fullmatch)
    {
        $this->fullmatch = $fullmatch;
    }


    public function setoptions(string $options)
    {
        if(!empty($options)) {
            $this->options = $options;
        }
    }

    public function setpath(string $path)
    {
        $this->path = $path;
    }
    
    public function setsortby(string $sortby)
    {
        if(in_array($sortby, self::SORT_BY_OPTIONS)) {
            $this->sortby = $sortby;
        }
    }

    public function setorder(int $order)
    {
        if($order === -1 || $order === 1) {
            $this->order = $order;
        }
    }
    

}