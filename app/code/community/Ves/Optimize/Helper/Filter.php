<?php

/**
 * Ves_FrontendOptimisation_Helper_Data
 *
 * Helper du module FacebookConnect
 *
 * @category   Ves
 * @package    Ves_FacebookConnect
 * @see Mage_Core_Model_Abstract
 * @version 1.0
 * @copyright  Copyright (c) 2011
 */
class Ves_Optimize_Helper_Filter extends Mage_Core_Helper_Abstract
{
    protected $jsExternalFiles = array();
    protected $jsInlineScripts = array();

    public function processContent($contents)
    {
        try{
            $contents = str_replace(array("<img","<script","<link"),array("\n<img","\n<script","\n<link"),$contents);
            // process lazy load image
            if($this->isLazyloadImage()){
                //$pattern = '@<img.*src=["'](.*)["'].*width="([^"]*)".*height="([^"]*)".*/>@';
                $contents = preg_replace_callback('@<img(.*)src="([^"]*)"([^>]*)/>@i', function($matches){
                    $result = '<img src="'.$matches[1].'"'.$matches[2].$matches[3].'/>';
                    try{
                        list($width, $height, $type, $attr) = getimagesize($matches[2]);
                        if(!empty($width) && !empty($height)){
                            $img = new stdClass();
                            $img->width = $width;
                            $img->height = $height;
                            if($this->isAllowLazyloadImage($img)){
                                $result = '<img src="'.$this->getPlaceHolderImage().'" data-src="'.$matches[2].'"'.$matches[3]."/>";
                            }
                        }
                    }
                    catch(Exception $e){

                    }
                    return $result;
                }, $contents);
            }

            $pattern = '/<script(.*)src="([^"]*)"([^>]*)>/i';
            $contents = preg_replace($pattern,'<script type="text/rocketscript" data-rocketsrc="$2"$3>',$contents);

            $pattern = '/<script(.*)type="([^"]*)"([^>]*)>/i';
            $contents = preg_replace($pattern,'<script$1type="text/rocketscript"$3>',$contents);

            // lazy load javascript
            $cloudfarePath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS)."ves/";
            $scriptHtml=<<<TEXT
                <script>
                window.onload = function() {
                var
                    _scripts = document.getElementsByTagName("script"),
                    _doc = document,
                    _txt = "text/rocketscript";

                for(var i=0,l=_scripts.length;i<l;i++){
                    var _type = _scripts[i].getAttribute("type");
                        if(_type && _type.toLowerCase() ==_txt){

                            _scripts[i].parentNode.replaceChild((function(sB){
                                var _s = _doc.createElement('script');
                                _s.type = 'text/javascript';
                                if(sB.getAttribute('data-rocketsrc') != ''){
                                    _s.src = sB.getAttribute('data-rocketsrc');
                                }else{
                                    _s.innerHTML = sB.innerHTML;
                                }
                                console.log(_s);
                                return _s;
                            })(_scripts[i]), _scripts[i]);
                        }
                }
            };
            </script>
TEXT;

            $scriptHtml = str_replace('&amp;','&',$scriptHtml);

            $scriptBefore = '';
            if($this->isLazyloadImage()){
                $scriptBefore = '<style>.lazy-load, .lazy-loaded {-webkit-transition: opacity 0.3s;-moz-transition: opacity 0.3s;-ms-transition: opacity 0.3s;-o-transition: opacity 0.3s;transition: opacity 0.3s;opacity: 0;} .lazy-loaded { opacity: 1; }</style>';
                // lazy load javascript
                $scriptBefore .= '<script type="text/rocketscript" data-rocketsrc="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'ves/lazyload.min.js"></script>';
            }

            $pattern = '/<body(\b[^>]*)>(.*?)<\/body>/si';
            $contents = preg_replace($pattern,'<body$1>$2'.$scriptBefore.$scriptHtml.'</body>',$contents);

            if($this->isCompressHtml()){
                $search = array(
                    '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
                    '/[^\S ]+\</s',  // strip whitespaces before tags, except space
                    '/(\s)+/s'       // shorten multiple whitespace sequences
                );

                $replace = array(
                    '>',
                    '<',
                    '\\1'
                );

                $contents = preg_replace($search, $replace, $contents);
            }
        }
        catch(Exception $e){
            Mage::logException($e);
        }
        return $contents;
    }

    public function isCombineJs()
    {
        return true;
    }

    public function isLazyloadImage()
    {
        return true;
    }

    public function isCompressHtml()
    {
        return false;
    }

    protected function writeFile($content,$file,$type)
    {
        $dir  = Mage::getBaseDir('media') . DS . 'ves' . DS . $type;
        $filename = $dir . DS . $file;
        if (!is_dir($dir)) {
            mkdir($dir,0777,true);
        }
        file_put_contents($filename, $content);
        chmod($filename, 0777);

        $path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . '/ves/'.$type . '/' . $file;
        return $path;
    }

    protected function getContents($url)
    {
        $return = "";
        $filePath = "";
        $baseDir = Mage::getBaseDir().DS;
        $baseUrl = Mage::getBaseUrl();
        $filePath = str_replace($baseUrl,$baseDir,$url);
        $filePath = str_replace('/',DS,$filePath);
        if(file_exists($filePath)){
            $return = file_get_contents($filePath);
        }
        else{
            $ch = curl_init();
            $header = array(
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
                'Accept-Language: en-us;q=0.8,en;q=0.6'
            );
            $options = array(
                CURLOPT_URL => $url,
                CURLOPT_HEADER => 0,
                CURLOPT_FOLLOWLOCATION => TRUE,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en; rv:1.9.2.13) Gecko/20101203 Firefox/3.6.13',
                CURLOPT_HTTPHEADER => $header
            );
            curl_setopt_array($ch, $options);
            $return = curl_exec($ch);
            curl_close($ch);
        }

        return $return;
    }

    public function getPlaceHolderImage()
    {
        return  Mage::getDesign()->getSkinUrl('images/placeholder.png');
    }

    public function getAllowImageWidth()
    {
        return 100;
    }

    public function getAllowImageHeight()
    {
        return 100;
    }

    public function isAllowLazyloadImage($img)
    {
        $result = false;
        if(!empty($img)){
            if(!empty($img->width) && $img->height){
                $width = intval($img->width);
                $height = intval($img->height);
                if($width >= $this->getAllowImageWidth() && $height >= $this->getAllowImageHeight()){
                    $result = true;
                }
            }
        }
        return $result;
    }
}
