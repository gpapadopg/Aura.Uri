<?php
namespace Aura\Uri;

class Path extends \ArrayObject
{
    /**
     * 
     * The dot-format extension of the last path element (for example, the 
     * "rss" in "feed.rss").
     * 
     * @var string
     * 
     */
    protected $format;
    
    /**
     * 
     * Returns the path array as a string, including the format.
     * 
     * @return string The path string.
     * 
     */
    public function __toString()
    {
        // Url-encode only these characters in path elements.
        // Characters are ' ' (space), '/', '?', '&', and '#'.
        $encode = array (
            ' ' => '+',
            '/' => '%2F',
            '?' => '%3F',
            '&' => '%26',
            '#' => '%23',
        );

        $keys = array_keys($encode);
        $vals = array_values($encode);
        
        // encode each path element
        $path = [];
        foreach ($spec as $elem) {
            $path[] = str_replace($keys, $vals, $elem);
        }
        
        // create a string from the encoded elements
        $path = implode('/', $path);
        
        // create a format string. we use trim() instead of empty() to allow
        // for string-zero values.
        $format = trim($this->format) === ''
                ? ''
                : '.' . urlencode($this->format);
        
        // done!
        return $path . $format;
    }

    /**
     * 
     * Sets the $path array and $format value from a string.
     * 
     * This will overwrite any previous values.
     * 
     * @param string $spec The path string to use; for example,
     * "/foo/bar/baz/dib.gir".  A leading slash will *not* create an empty
     * first element; if the string has a leading slash, it is ignored.
     * 
     * @return void
     * 
     */
    public function setFromString($path)
    {
        $this->exchangeArray([]);
        $path = explode('/', $path);
        foreach ($path as $key => $val) {
            $this[$key] = urldecode($val);
        }

        // $key and $val are already at the end
        $this->setFormat(null);
        if ($val) {
            // find the last dot in the value
            $pos = strrpos($val, '.');
            if ($pos !== false) {
                // remove from the path and retain as the format
                $this[$key] = substr($val, 0, $pos);
                $this->setFormat(substr($val, $pos + 1));
            }
        }
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }
    
    public function getFormat()
    {
        return $this->format;
    }
}
