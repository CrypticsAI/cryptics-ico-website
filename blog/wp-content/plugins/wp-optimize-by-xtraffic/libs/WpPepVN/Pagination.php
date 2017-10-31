<?php 

namespace WpPepVN;

use WpPepVN\Exception
;

class Pagination
{
	/**
	* _variables
	* 
	* Sets default variables for the rendering of the pagination markup.
	* 
	* @var    array
	* @access protected
	*/
	protected $_variables = array(
		'classes' => array('clearfix', 'pagination'),
		'crumbs' => 5,
		'rpp' => 10,
		'key' => 'page',
		'target' => '',
		'next' => 'Next &raquo;',
		'previous' => '&laquo; Previous',
		'alwaysShowPagination' => false,
		'clean' => false
	);

	/**
	* __construct
	* 
	* @access public
	* @param  integer $current (default: null)
	* @param  integer $total (default: null)
	* @return void
	*/
	public function __construct($current = null, $total = null)
	{
		// current instantiation setting
		if (!is_null($current)) {
			$this->setCurrent($current);
		}

		// total instantiation setting
		if (!is_null($total)) {
		$this->setTotal($total);
		}

		// Pass along get (for link generation)
		$this->_variables['get'] = $_GET;
	}

	/**
	* _check
	* 
	* Checks the current (page) and total (records) parameters to ensure
	* they've been set. Throws an exception otherwise.
	* 
	* @access protected
	* @return void
	*/
	protected function _check()
	{
		if (!isset($this->_variables['current'])) {
			throw new Exception('Pagination::current must be set.');
		} elseif (!isset($this->_variables['total'])) {
			throw new Exception('Pagination::total must be set.');
		}
	}

	/**
	* addClasses
	* 
	* Sets the classes to be added to the pagination div node.
	* Useful with Twitter Bootstrap (eg. pagination-centered, etc.)
	* 
	* @see    <http://twitter.github.com/bootstrap/components.html#pagination>
	* @access public
	* @param  mixed $classes
	* @return void
	*/
	public function addClasses($classes)
	{
		$this->_variables['classes'] = array_merge(
			$this->_variables['classes'],
			(array) $classes
		);
	}

	/**
	* alwaysShowPagination
	* 
	* Tells the rendering engine to show the pagination links even if there
	* aren't any pages to paginate through.
	* 
	* @access public
	* @return void
	*/
	public function alwaysShowPagination()
	{
		$this->_variables['alwaysShowPagination'] = true;
	}

	/**
	* getCanonicalUrl
	* 
	* @access public
	* @return string
	*/
	public function getCanonicalUrl()
	{
		$target = $this->_variables['target'];
		if (empty($target)) {
			$target = $_SERVER['PHP_SELF'];
		}
		$page = (int) $this->_variables['current'];
		
		if ($page !== 1) {
			return 'http://' . ($_SERVER['HTTP_HOST']) . ($target) . $this->getPageParam();
		}
		
		return 'http://' . ($_SERVER['HTTP_HOST']) . ($target);
	}

	/**
	* getPageParam
	* 
	* @access public
	* @param  boolean|integer $page (default: false)
	* @return string
	*/
	public function getPageParam($page = false)
	{
		if ($page === false) {
			$page = (int) $this->_variables['current'];
		}
		$key = $this->_variables['key'];
		return '?' . ($key) . '=' . ((int) $page);
	}

	/**
	* getPageUrl
	* 
	* @access public
	* @param  boolean|integer $page (default: false)
	* @return string
	*/
	public function getPageUrl($page = false)
	{
		$target = $this->_variables['target'];
		if (empty($target)) {
			$target = $_SERVER['PHP_SELF'];
		}
		return 'http://' . ($_SERVER['HTTP_HOST']) . ($target) . ($this->getPageParam($page));
	}

	/**
	* getRelPrevNextLinkTags
	* 
	* @see    http://support.google.com/webmasters/bin/answer.py?hl=en&answer=1663744
	* @see    http://googlewebmastercentral.blogspot.ca/2011/09/pagination-with-relnext-and-relprev.html
	* @see    http://support.google.com/webmasters/bin/answer.py?hl=en&answer=139394
	* @access public
	* @return array
	*/
	public function getRelPrevNextLinkTags()
	{
		// generate path
		$target = $this->_variables['target'];
		if (empty($target)) {
			$target = $_SERVER['PHP_SELF'];
		}
		$key = $this->_variables['key'];
		$params = $this->_variables['get'];
		$params[$key] = 'pgnmbr';
		$href = ($target) . '?' . http_build_query($params);
		$href = preg_replace(
			array('/=$/', '/=&/'),
			array('', '&'),
			$href
		);
		$href = 'http://' . ($_SERVER['HTTP_HOST']) . $href;

		// Pages
		$currentPage = (int) $this->_variables['current'];
		$numberOfPages = (
			(int) ceil(
				$this->_variables['total'] /
				$this->_variables['rpp']
			)
		);

		// On first page
		if ($currentPage === 1) {

			// There is a page after this one
			if ($numberOfPages > 1) {
				$href = str_replace('pgnmbr', 2, $href);
				return array(
					'<link rel="next" href="' . ($href) . '" />'
				);
			}
			return array();
		}

		// Store em
		$prevNextTags = array(
			'<link rel="prev" href="' . (str_replace('pgnmbr', $currentPage - 1, $href)) . '" />'
		);

		// There is a page after this one
		if ($numberOfPages > $currentPage) {
			array_push(
				$prevNextTags,
				'<link rel="next" href="' . (str_replace('pgnmbr', $currentPage + 1, $href)) . '" />'
			);
		}
		return $prevNextTags;
	}

	/**
	* parse
	* 
	* Parses the pagination markup based on the parameters set and the
	* logic found in the render.inc.php file.
	* 
	* @access public
	* @return void
	*/
	public function parse()
	{
		// ensure required parameters were set
		$this->_check();

		// bring variables forward
		foreach ($this->_variables as $_name => $_value) {
			$$_name = $_value;
		}
		
		
		
		// total page count calculation
		$pages = ((int) ceil($total / $rpp));

		// if it's an invalid page request
		if ($current < 1) {
			return '';
		} elseif ($current > $pages) {
			return '';
		}
		
		$_response = '';

		// if there are pages to be shown
		if ($pages > 1 || $alwaysShowPagination === true) {
			$_response .= '<ul class="'.implode(' ', $classes).'">';
			
			/**
			 * Previous Link
			 */

			// anchor classes and target
			$classes = array('copy', 'previous');
			$params = $get;
			$params[$key] = ($current - 1);
			$href = ($target) . '?' . http_build_query($params);
			$href = preg_replace(
				array('/=$/', '/=&/'),
				array('', '&'),
				$href
			);
			if ($current === 1) {
				$href = '#';
				array_push($classes, 'disabled');
			}
			
			$_response .= '<li class="'.implode(' ', $classes).'"><a href="'.$href.'">'.$previous.'</a></li>';
			
			/**
         * if this isn't a clean output for pagination (eg. show numerical
         * links)
         */
        if (!$clean) {

            /**
             * Calculates the number of leading page crumbs based on the minimum
             *     and maximum possible leading pages.
             */
            $max = min($pages, $crumbs);
            $limit = ((int) floor($max / 2));
            $leading = $limit;
            for ($x = 0; $x < $limit; ++$x) {
                if ($current === ($x + 1)) {
                    $leading = $x;
                    break;
                }
            }
			
            for ($x = $pages - $limit; $x < $pages; ++$x) {
                if ($current === ($x + 1)) {
                    $leading = $max - ($pages - $x);
                    break;
                }
            }

            // calculate trailing crumb count based on inverse of leading
            $trailing = $max - $leading - 1;

            // generate/render leading crumbs
            for ($x = 0; $x < $leading; ++$x) {

                // class/href setup
                $params = $get;
                $params[$key] = ($current + $x - $leading);
                $href = ($target) . '?' . http_build_query($params);
                $href = preg_replace(
                    array('/=$/', '/=&/'),
                    array('', '&'),
                    $href
                );
				
				$_response .= '<li class="number"><a data-pagenumber="'.($current + $x - $leading).'" href="'.($href).'">'.($current + $x - $leading).'</a></li>';
			}
			
			// print current page
			$_response .= '<li class="number active"><a data-pagenumber="'.($current).'" href="#">'.($current).'</a></li>';
			
			// generate/render trailing crumbs
            for ($x = 0; $x < $trailing; ++$x) {

                // class/href setup
                $params = $get;
                $params[$key] = ($current + $x + 1);
                $href = ($target) . '?' . http_build_query($params);
                $href = preg_replace(
                    array('/=$/', '/=&/'),
                    array('', '&'),
                    $href
                );
				
				$_response .= '<li class="number"><a data-pagenumber="'.($current + $x + 1).'" href="'.($href).'">'.($current + $x + 1).'</a></li>';
				
				}
			}

			/**
			* Next Link
			*/

			// anchor classes and target
			$classes = array('copy', 'next');
			$params = $get;
			$params[$key] = ($current + 1);
			$href = ($target) . '?' . http_build_query($params);
			$href = preg_replace(
				array('/=$/', '/=&/'),
				array('', '&'),
				$href
			);
			if ($current === $pages) {
				$href = '#';
				array_push($classes, 'disabled');
			}
			
			$_response .= '<li class="'.implode(' ', $classes).'"><a href="'.($href).'">'.($next).'</a></li>';
			
		} //if ($pages > 1 || $alwaysShowPagination === true) 
		
		return $_response;
	}

	/**
	* setClasses
	* 
	* @see    <http://twitter.github.com/bootstrap/components.html#pagination>
	* @access public
	* @param  mixed $classes
	* @return void
	*/
	public function setClasses($classes)
	{
		$this->_variables['classes'] = (array) $classes;
	}

	/**
	* setClean
	* 
	* Sets the pagination to exclude page numbers, and only output
	* previous/next markup. The counter-method of this is self::setFull.
	* 
	* @access public
	* @return void
	*/
	public function setClean()
	{
		$this->_variables['clean'] = true;
	}

	/**
	* setCrumbs
	* 
	* Sets the maximum number of 'crumbs' (eg. numerical page items)
	* available.
	* 
	* @access public
	* @param  integer $crumbs
	* @return void
	*/
	public function setCrumbs($crumbs)
	{
		$this->_variables['crumbs'] = $crumbs;
	}

	/**
	* setCurrent
	* 
	* Sets the current page being viewed.
	* 
	* @access public
	* @param  integer $current
	* @return void
	*/
	public function setCurrent($current)
	{
		$this->_variables['current'] = $current;
	}

	/**
	* setFull
	* 
	* See self::setClean for documentation.
	* 
	* @access public
	* @return void
	*/
	public function setFull()
	{
		$this->_variables['clean'] = false;
	}

	/**
	* setKey
	* 
	* Sets the key of the <_GET> array that contains, and ought to contain,
	* paging information (eg. which page is being viewed).
	* 
	* @access public
	* @param  string $key
	* @return void
	*/
	public function setKey($key)
	{
		$this->_variables['key'] = $key;
	}

	/**
	* setNext
	* 
	* Sets the copy of the next anchor.
	* 
	* @access public
	* @param  string $str
	* @return void
	*/
	public function setNext($str)
	{
		$this->_variables['next'] = $str;
	}

	/**
	* setPrevious
	* 
	* Sets the copy of the previous anchor.
	* 
	* @access public
	* @param  string $str
	* @return void
	*/
	public function setPrevious($str)
	{
		$this->_variables['previous'] = $str;
	}

	/**
	* setRPP
	* 
	* Sets the number of records per page (used for determining total
	* number of pages).
	* 
	* @access public
	* @param  integer $rpp
	* @return void
	*/
	public function setRPP($rpp)
	{
		$this->_variables['rpp'] = $rpp;
	}

	/**
	* setTarget
	* 
	* Sets the leading path for anchors.
	* 
	* @access public
	* @param  string $target
	* @return void
	*/
	public function setTarget($target)
	{
		$this->_variables['target'] = $target;
	}

	/**
	* setTotal
	* 
	* Sets the total number of records available for pagination.
	* 
	* @access public
	* @param  integer $total
	* @return void
	*/
	public function setTotal($total)
	{
		$this->_variables['total'] = $total;
	}
}