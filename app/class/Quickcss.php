<?php

namespace Wcms;

class Quickcss extends Item
{


    private $active = [];
    private $values = [];
    private $units = [];
    private $new = [];
    private $jsoncss = [];
    
    private $quickcss = [];

    const COLOR = ['color', 'background-color', 'border-color', 'text-decoration-color'];
    const SIZE = ['width', 'height', 'margin', 'padding', 'border-radius', 'border-width', 'left', 'right', 'top', 'bottom'];
    const UNIQUE = ['background-image', 'opacity', 'font-size'];

    const OPTIONS = [
        'text-align' => ['left', 'right', 'center', 'justify'],
        'border-style' => ['solid', 'double', 'outset', 'ridge'],
        'font-family' => ['serif', 'sans-serif', 'monospace', 'cursive', 'fantasy'],
        'text-decoration-line' => ['none', 'underline', 'overline', 'line-through', 'underline overline'],
        'display' => ['none', ]
    ];


    private static function getselect()
    {
        return array_keys(self::OPTIONS);
    }

    private static function getparams()
    {
        $params = array_merge(self::COLOR, self::SIZE, self::getselect(), self::UNIQUE);
        sort($params, SORT_STRING );
        return $params;
    }

    public function __construct($data)
    {
        $this->hydrate($data);    
    }

    public function calc()
    {
        $quickcss = $this->intersect($this->values,$this->active);        
        $quickcss = $this->merge($quickcss, $this->new);
        $quickcss = $this->addunits($quickcss, $this->units);
        $quickcss = $this->merge($this->jsoncss, $quickcss);

        $this->quickcss = $quickcss;
    }



    // _________________________________________ P O S T __________________________________________________

    public function setvalues($data)
    {
        if(is_array($data)) {
            $this->values = $data;
        }
    }

    public function setunits($data)
    {
        if(is_array($data)) {
            $this->units = $data;
        }
    }

    public function setactive($data)
    {
        if(is_array($data)) {
            $this->active = $data;
        }
    }

    public function setnew($data)
    {
        if (!empty($data['element']) && !empty($data['param']) && in_array($data['param'], self::getparams())) {
            $new = array($data['element'] => array($data['param'] => ''));
            $this->new = $new;
        }
    }

    
    public function setjson($jsoncss)
    {
        if(!empty($jsoncss) && is_string($jsoncss)) {
            $jsoncss = json_decode($jsoncss);
            if(is_array($jsoncss)) {
                $this->jsoncss = $jsoncss;
            } else {
                $this->jsoncss = [];
            }
        }
    }


    // _______________________________________ C A L C ___________________________________________________

    public function intersect($values, $active)
    {
        $intersect = array_intersect_key($values, $active);

        foreach ($intersect as $element => $css) {
            $intersect[$element] = array_intersect_key($values[$element], $active[$element]);
        }
        return $intersect;
    }

    public function merge($quickcss, $new)
    {
        $quickcss = array_merge_recursive($quickcss, $new);
        return $quickcss;
    }

    public function addunits($quickcss, $units)
    {
        foreach ($units as $element => $css) {
            foreach ($css as $param => $unit) {
                if (array_key_exists($element, $quickcss) && array_key_exists($param, $quickcss[$element])) {
                    $quickcss[$element][$param] = $quickcss[$element][$param] . $unit;
                }
            }
        }
        return $quickcss;
    }



    // __________________________________________ C O M _________________________________________

    public function tocss()
    {
        $string = '';
        foreach ($this->quickcss as $element => $css) {
            $string .= PHP_EOL . $element . ' {';
            foreach ($css as $param => $value) {
                $string .= PHP_EOL . '    ' . $param . ': ' . $value . ';';
            }
            $string .= PHP_EOL . '}' . PHP_EOL;
        }
        return $string;
    }

    public function tojson()
    {
        return json_encode($this->quickcss);
    }




    // _____________________________________________ F O R M ____________________________________________

    public function form($action)
    {
        echo '<form action="' . $action . '" method="post">';
        echo '</br><input type="submit" value="submit">';
        $this->inputs($this->quickcss);
        echo '</br><input type="submit" value="submit">';
        echo '</form>';

    }

    public function inputs($quickcss)
    {
        echo '<h1>Add element</h1>';

        echo '<input type="text" name="new[element]" list="used">';
        echo '<datalist id="used">';
        foreach (array_keys($quickcss) as $element) {
            echo '<option value ="'.$element.'">';
        }
        echo '</datalist>';

        echo '<select name="new[param]">';
        foreach (self::getparams() as $param) {
            echo '<option value="' . $param . '">' . $param . '</option>';
        }
        echo '</select>';

        foreach ($quickcss as $element => $css) {
            echo '<h3>' . $element . '</h3>';
            foreach ($css as $param => $value) {

                echo '<div class="quicklabel">';
                echo '<input type="checkbox" name="active[' . $element . '][' . $param . ']" id="active[' . $element . '][' . $param . ']" checked>';
                echo '<label for="active[' . $element . '][' . $param . ']">' . $param . '</label>';
                echo '</div>';

                echo '<div class="quickinput">';

                if (in_array($param, self::COLOR)) {
                    echo '<input type="color" name="values[' . $element . '][' . $param . ']" value="' . $quickcss[$element][$param] . '" id="values[' . $element . '][' . $param . ']">';
                }

                if (in_array($param, self::SIZE)) {
                    $this->sizeinput($element, $param, $value);
                }

                if (in_array($param, self::getselect())) {
                    $this->selectinput($element, $param, $value);
                }

                if (in_array($param, self::UNIQUE)) {
                    $method = str_replace('-', '', $param) . 'input';
                    if (method_exists($this, $method)) {
                        $this->$method($element, $param, $value);
                    }
                }

                echo '</div>';
            }
        }


    }






    // ____________________________________ I N P U T __________________________________

    public function sizeinput($element, $param, $value)
    {
        echo '<input type="number" name="values[' . $element . '][' . $param . ']" value="' . intval($value) . '" id="values[' . $element . '][' . $param . ']">';

        $unit = preg_replace('/\d/', '', $value);
        ?>
        <select name="units[<?= $element ?>][<?= $param ?>]" >
            <option value="px" <?= $unit == 'px' ? 'selected' : '' ?>>px</option>
            <option value="%" <?= $unit == '%' ? 'selected' : '' ?>>%</option>
        </select>
        <?php

    }

    public function fontsizeinput($element, $param, $value)
    {
        echo '<input type="number" name="values[' . $element . '][' . $param . ']" value="' . intval($value) . '" id="values[' . $element . '][' . $param . ']">';

        $unit = preg_replace('/\d/', '', $value);
        ?>
        <select name="units[<?= $element ?>][<?= $param ?>]" >
            <option value="px" <?= $unit == 'px' ? 'selected' : '' ?>>px</option>
            <option value="em" <?= $unit == 'em' ? 'selected' : '' ?>>em</option>
        </select>
        <?php

    }

    public function opacityinput($element, $param, $value)
    {
        echo '<input type="number" name="values[' . $element . '][' . $param . ']" value="' . $value . '" id="values[' . $element . '][' . $param . ']" step="0.1" min="0" max="1">';
    }

    public function selectinput($element, $param, $value)
    {
        echo '<select name="values[' . $element . '][' . $param . ']">';
        foreach (self::OPTIONS[$param] as $option) {
            if($option == $value) {
                echo '<option value="'.$option.'" selected>'.$option.'</option>';
            } else {
                echo '<option value="'.$option.'">'.$option.'</option>';
            }
        }
        echo '</select>';
    }


}



?>