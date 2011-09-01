<?php

/**
 * flavaSpriteMap 
 * 
 * @package 
 * @version $id$
 * @author Joshua Morse <joshua.morse@iostudio.com> 
 */
class flavaSpriteMapGenerator
{
  private
    $file,
    $height,
    $map_width,
    $map_height,
    $name, 
    $positions,
    $sprite_meta,
    $width
  ;

  /**
   * __construct 
   * 
   * @access public
   * @return void
   */
  public function __construct($file, $name, array $sprite_meta)
  {
    $this->setFile($file); 
    $this->setName($name);
    $this->setMeta();
    $this->setSpriteMeta($sprite_meta);
    $this->setSpriteMapDimensions();
    $this->setBackgroundPositions();
  }

  /**
   * setName 
   * 
   * @param mixed $name 
   * @access public
   * @return void
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * setSpriteMeta 
   * 
   * @param mixed $meta 
   * @access public
   * @return void
   */
  public function setSpriteMeta($sprite_meta)
  {
    $this->sprite_meta = $sprite_meta;
  }

  /**
   * setFile 
   * 
   * @param mixed $file 
   * @access public
   * @return void
   */
  public function setFile($file)
  {
    if (!file_exists($file))
    {
      throw new exception(sprintf('Unable to find the file: %s', $file)); 
    }

    $this->file = $file;
  }

  /**
   * setMeta 
   * 
   * @access private
   * @return void
   */
  public function setMeta()
  {
    $info = getimagesize($this->file);

    $this->height = $info[1];
    $this->width = $info[0];
  }

  /**
   * numberOfSpritesByWidth 
   * 
   * @access public
   * @return void
   */
  public function numberOfSpritesByWidth()
  {
    $e = $this->width / $this->sprite_meta['width'];

    return $e;
  }

  /**
   * numberOfSpritesByHeight 
   * 
   * @access public
   * @return void
   */
  public function numberOfSpritesByHeight()
  {
    $e = $this->height / $this->sprite_meta['height'];

    return $e;
  }

  /**
   * setSpriteMapDimensions 
   * 
   * @access private
   * @return void
   */
  private function setSpriteMapDimensions()
  {
    $this->map_width = $this->numberOfSpritesByWidth();    
    $this->map_height = $this->numberOfSpritesByHeight();    
  }

  /**
   * setBackgroundPositions 
   * 
   * @access private
   * @return void
   */
  private function setBackgroundPositions()
  {
    $positions = array();

    for ($h = 0; $h <= $this->map_height; ++$h)
    {
      $y = ($h * $this->sprite_meta['height']) - (($h * $this->sprite_meta['height']) * 2);

      for ($w = 0; $w <= $this->map_width; ++$w)
      {
        $x = ($w * $this->sprite_meta['width']) - (($w * $this->sprite_meta['width']) * 2);

        $positions[$this->getPositionName($w, $h)] = $this->plotPosition($x, $y);
      }
    }

    $this->positions = $positions;
  }

  private function getPositionName($w, $h)
  {
    return $w . '-' . $h;
  }

  private function plotPosition($x, $y)
  {
    return $x . 'px ' . $y . 'px';
  }

  /**
   * generateCss 
   * 
   * @access public
   * @return void
   */
  public function generateCss()
  {
    $content = false;

    foreach ($this->positions as $class => $position)
    {
      $content .= sprintf('.%s-%s { background: url(%s) %s no-repeat; display: block; height: %spx; width: %spx; }' . "\n",
        $this->name,
        $class,
        $this->file,
        $position,
        $this->sprite_meta['height'],
        $this->sprite_meta['width']
      );
    }

    return $content;
  }

  /**
   * writeFile 
   * 
   * @access public
   * @return void
   */
  public function writeFile()
  {
    $f = fopen($this->name . '.css', 'w');
    fwrite($f, $this->generateCss());
    fclose($f);
  }
}


$f = new flavaSpriteMap($argv[1], 'map-sprite', array('height' => $argv[2], 'width' => $argv[3]));
$f->writeFile();
