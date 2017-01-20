<?php

namespace Providers\Tools;

use \Session;
use \Request;
use \Router;

class TemplateRunner {

    private $viewRoot;

    private $compiledViewRoot;

    private $compileFileName = NULL;

    private $viewNonces;

    public function __construct($nonces){

        $this->viewNonces = $nonces;

        $this->viewRoot = $GLOBALS['env']['app.path.views'];

        $this->compiledViewRoot = str_replace('/views', '/storage/compiled/', $this->viewRoot);

    }

    private function getCompiledFileName($name){

       if($this->compileFileName === NULL){

           $this->compileFileName = $this->compiledViewRoot . preg_replace('/\//', '_', $name) . '.php';
       }

       return $this->compileFileName;

    }

    private function compile($name__){

    	$view__string;

		if(!isset($name__)){

		      throw new \Exception("No View File For Compilation");
		}
		try{
       $view__path = ($this->viewRoot . (starts_with($name__, '/')? substr($name__, 1) : $name__) . '.view');

		   $view__string  = file_get_contents($view__path);

		}catch(\Exception $e){

		     throw new \Exception("View File Not Found >> ['" . $name__ . "'] Compilation Not Successful");

		}

        $compileFn = array(&$this, 'compile');

        $compileRoot = $this->compiledViewRoot;

        if(array_key_exists('script', $this->viewNonces)
            || array_key_exists('style', $this->viewNonces)){

            $view__string = preg_replace(array('/<(script|style)[ ]*([\w\S\s]|[^>]*)>([\w\S\s]|[^<]+)<\/\1>/'), array('<${1} ${2} nonce="<?php echo $_nce[\'${1}\'] ?>">${3}</${1}>'), $view__string);
        }

        $templateFileIncludeToken = '/\[!import\((.*?)\);?\]/i';

    	$templateBaseTokens = array('/\[\s*?(if|elseif)\:([\w\S]+)?\@(\w+)(.+)\s*?\]/i', '/(?<!\[)\=\@(\w+)(?!\])/i', '/\[\@(\w+)\]/', '/\[\@(\w+)\=(\w+)\]/i', '/\[\s*?loop\:\@(\w+)\s*?\]/i', '/\[\s*?\/if\s*?\]/i', '/\[\s*?\/loop\s*?\]/i', '/\[\s*?choose\:\@(\w+)([\S\s\w]*)?\]/i', '/\[\s*?\/choose\s*?\]/i', '/\[\s*?when\:([\w\S ]+)\s*?\]/i', '/\[\s*?\/when\s*?\]/i', '/\[\!asset\((.*?)\);?\]/i', '/\[\!url\((.*?)\);?\]/i');
        $templateBaseTokensReplace  = array('<?php ${1}(${2}$${3}${4}): ?>', '<?php echo $${1}; ?>', '<?php echo $${1}; ?>', '<?php echo $${1}[\'${2}\']; ?>', '<?php foreach($${1} as $${1}_index => $${1}_value): ?>', '<?php endif; ?>', '<?php endforeach; ?>', '<?php switch ($${1}${2}): ?>', '<?php endswitch; ?>', '<?php case ${1}: ?>', '<?php ; ?>', '<?php echo asset(\$__url, \'${1}\'); ?>', '<?php echo url(\$__url, \'${1}\'); ?>');
		$templateRenderedOnce = preg_replace($templateBaseTokens, $templateBaseTokensReplace, $view__string);

        $renderCallback = function($matches) use ($compileFn, $compileRoot){

                $compiled = $compileFn($matches[1]); // indirect recursion here

                return '<?php include_once(__DIR__ . \'' . str_replace($compileRoot, '', $compiled) . '\'); ?>';

        };

        $templateRenderedFinal = preg_replace_callback($templateFileIncludeToken, $renderCallback, $templateRenderedOnce);

        $compiledFile = $this->getCompiledFileName($name__);

		file_put_contents($compiledFile, $templateRenderedFinal);

		return $compiledFile;

    }

    public function render($view__name, array $data__array = array()){


        $__file0 = $this->getCompiledFileName($view__name);

        $__viewss = Session::get('__views');

        $__routee = Router::currentRoute();

        $__sett = NULL;

        if(!isset($__viewss) || $__viewss === FALSE){

            $__viewss = array();
        }

        if(array_key_exists($__routee, $__viewss)){

             $__sett = $__viewss[$__routee];
        }

        if(isset($__sett) && file_exists($__file0)){

        	if($__sett['compiled'] === $__file0){

                if($__sett['size'] <= filemtime($__file0)){ // detect change in the view file '.view'

                     $__file0 = $this->compile($view__name); // comiple the view
                }
            }
        }else{

        	$__file0 = $this->compile($view__name); // compile the view

        }

        $__viewss[$__routee] = array('compiled' => $__file0, 'size' => filemtime($__file0));

        Session::put('__views', $__viewss);

        $data__array['csrftoken'] = Session::token();

		return $this->draw($__file0, $view__name, $data__array);

	}

	private function draw($__file, $__name, array $vars){

	     // variables created by 'extract()' are not visible in outer or global scope
         // so this is a safe operation within this function method (__FUNCTION__)

        $vars['_nce'] = $this->viewNonces;

        $appRoot = $GLOBALS['env']['app.root'];
        $appHost = $GLOBALS['app']->getHost('/');

        if(index_of(Request::header('REQUEST_URI'), $appRoot) > -1){

            $vars['__url'] = '//' . $appHost . $appRoot .'/';

        }else{

            $vars['__url'] = '//' . $appHost;
        }


	   extract($vars);

       try{

         // 'require_once()' uses variables defined in only both its' immediate outer scope and the global scope
         require_once($__file);

       }catch(\Exception $e){

		   throw new \Exception("Error in View File >> [" . $__name . "] => " . $e->getMesage());

 	   }

        $___drawn =  ob_get_contents();

        return $___drawn;

	}

}



?>