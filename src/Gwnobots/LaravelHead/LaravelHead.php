<?php namespace Gwnobots\LaravelHead;

use App;
use Config;
use File;
use URL;
 
class LaravelHead {

	protected $charset;

	protected $title;

	protected $description;

	protected $meta = array();

	protected $misc;

	public function render()
	{
		return 
			$this->tagCharset().
			$this->tagTitle().
			$this->tagDescription().
			$this->tagMeta().

			$this->tagMisc()
		;
	}

	public function setCharset($charset)
	{
		$this->charset = $charset;
	}

	protected function tagCharset()
	{
		$charset = ($this->charset) ? $this->charset : Config::get('laravel-head::charset');
		
		if ($charset)
		{
			return '<meta charset="'.$charset.'">' . "\n\t";
		}
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function doSitename()
	{
		Config::set('laravel-head::title.sitename', true);
	}

	public function noSitename()
	{
		Config::set('laravel-head::title.sitename', false);
	}

	protected function renderTitle()
	{
		$sitename = Config::get('laravel-head::title.sitename');

		$title = ($this->title) ? $this->title : $sitename;
				
		$separator = Config::get('laravel-head::title.separator');

		$show = Config::get('laravel-head::title.show_sitename');

		$first = Config::get('laravel-head::title.first');

		if ($show)
		{
			if ($sitename == $title)
			{
				return $sitename;
			}
			
			elseif (!$sitename)
			{
				return $title;
			}
		
			elseif (!$title)
			{
				return $sitename;
			}
		
			else
			{
				return ($first) ? $title.$separator.$sitename : $sitename.$separator.$title;
			}
		}

		else
		{
			return $title;
		}
	}

	protected function tagTitle()
	{
		$title = $this->renderTitle();

		if ($title)
		{
			return '<title>'.$title.'</title>' . "\n\t";
		}
	}

	public function addMeta($meta = array())
	{
		$this->meta = array_merge($this->meta, $meta);
	}

	protected function tagMeta()
	{
		$html = '';

		$this->addRobots();

		$this->addIeEdge();

		$this->addResponsive();

		$this->addFacebook();

		$this->addTwitter();

		foreach ($this->meta as $type => $val)
		{
			if ($type == 'name' || $type == 'http-equiv' || $type == 'property')
			{
				foreach ($val as $value => $content)
				{
					if ($value && $content)
					{
						$html .= '<meta '.$type.'="'.$value.'" content="'.$content.'">' . "\n\t";
					}
				}
			}
		}

		return $html;
	}

	public function addOneMeta($type, $value, $content)
	{
		$this->meta[$type][$value] = $content;
	}

	protected function addRobots()
	{
		if (!App::environment('production'))
		{
			$this->addOneMeta('name', 'robots', 'none');
		}
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}

	protected function tagDescription()
	{
		$description = $this->description;

		if ($description)
		{
			return '<meta name="description" content="'.$description.'">' . "\n\t";
		}
	}

	protected function addIeEdge()
	{
		if (Config::get('laravel-head::ie_edge'))
		{
			if (!array_key_exists('http-equiv', $this->meta) || !array_key_exists('X-UA-Compatible', $this->meta['http-equiv']))
			{
				$this->addOneMeta('http-equiv', 'X-UA-Compatible', 'IE=edge,chrome=1');
			}
		}
	}

	public function noIeEdge()
	{
		Config::set('laravel-head::ie_edge', false);
	}

	public function doIeEdge()
	{
		Config::set('laravel-head::ie_edge', true);
	}

	protected function addResponsive()
	{
		if (Config::get('laravel-head::responsive'))
		{
			if (!array_key_exists('name', $this->meta) || !array_key_exists('viewport', $this->meta['name']))
			{
				$this->addOneMeta('name', 'viewport', 'width=device-width, initial-scale=1.0');
			}
		}
	}

	public function doResponsive()
	{
		Config::set('laravel-head::responsive', true);
	}

	public function noResponsive()
	{
		Config::set('laravel-head::responsive', false);
	}

	protected function addFacebook()
	{
		$sitename = Config::get('laravel-head::title.sitename');

		$title = $this->renderTitle();

		$description = $this->description;

		$admins = Config::get('laravel-head::facebook.admins');

		$page_id =Config::get('laravel-head::facebook.page_id');

		$app_id = Config::get('laravel-head::facebook.app_id');

		$image = Config::get('laravel-head::facebook.image');

		if (Config::get('laravel-head::facebook.active'))
		{
			if (!array_key_exists('property', $this->meta) || !array_key_exists('fb:page_id', $this->meta['property']))
			{
				$this->addOneMeta('property', 'fb:page_id', $page_id);
			}

			if (!array_key_exists('property', $this->meta) || !array_key_exists('fb:app_id', $this->meta['property']))
			{
				$this->addOneMeta('property', 'fb:app_id', $app_id);
			}

			if (!array_key_exists('property', $this->meta) || !array_key_exists('fb:admins', $this->meta['property']))
			{
				$this->addOneMeta('property', 'fb:admins', $admins);
			}

			if (File::isFile(public_path($image) && !array_key_exists('property', $this->meta) || !array_key_exists('og:image', $this->meta['property'])))
			{
				$this->addOneMeta('property', 'og:image', asset($image));
			}

			if (!array_key_exists('property', $this->meta) || !array_key_exists('og:url', $this->meta['property']))
			{
				$this->addOneMeta('property', 'og:url', URL::current());
			}

			if (!array_key_exists('property', $this->meta) || !array_key_exists('og:type', $this->meta['property']))
			{
				$this->addOneMeta('property', 'og:type', 'website');
			}

			if (!array_key_exists('property', $this->meta) || !array_key_exists('og:site_name', $this->meta['property']))
			{
				$this->addOneMeta('property', 'og:site_name', $sitename);
			}

			if (!array_key_exists('property', $this->meta) || !array_key_exists('og:title', $this->meta['property']))
			{
				$this->addOneMeta('property', 'og:title', $title);
			}

			if (!array_key_exists('property', $this->meta) || !array_key_exists('og:description', $this->meta['property']))
			{
				$this->addOneMeta('property', 'og:description', $description);
			}
		}
	}

	public function doFacebook()
	{
		Config::set('laravel-head::facebook.active', true);
	}

	public function noFacebook()
	{
		Config::set('laravel-head::facebook.active', false);

		foreach ($this->meta as $type => $val)
		{
			if (array_key_exists('property', $this->meta))
			{
				foreach ($val as $value => $content)
				{
					if (starts_with($value, 'og:') || starts_with($value, 'fb:'))
					{
						$this->meta['property'] = array_except($this->meta['property'], array($value));
					}
				}
			}
		}
	}

	protected function addTwitter()
	{
		$title = $this->renderTitle();

		$description = $this->description;

		$image = Config::get('laravel-head::twitter.image');

		$site = Config::get('laravel-head::twitter.site');

		$creator = Config::get('laravel-head::twitter.creator');

		if (Config::get('laravel-head::twitter.active'))
		{
			$this->addOneMeta('name', 'twitter:card', 'summary');

			if (!array_key_exists('name', $this->meta) || !array_key_exists('twitter:title', $this->meta['property']))
			{
				$this->addOneMeta('name', 'twitter:title', $title);
			}

			if (!array_key_exists('name', $this->meta) || !array_key_exists('twitter:description', $this->meta['property']))
			{
				$this->addOneMeta('name', 'twitter:description', $description);
			}

			if (File::isFile(public_path($image)) && (!array_key_exists('name', $this->meta) || !array_key_exists('twitter:image:src', $this->meta['property'])))
			{
				$this->addOneMeta('name', 'twitter:image:src', asset($image));
			}

			if (!array_key_exists('name', $this->meta) || !array_key_exists('twitter:site', $this->meta['property']))
			{
				$this->addOneMeta('name', 'twitter:site', $site);
			}

			if (!array_key_exists('name', $this->meta) || !array_key_exists('twitter:creator', $this->meta['property']))
			{
				$this->addOneMeta('name', 'twitter:creator', $creator);
			}

			if (!array_key_exists('name', $this->meta) || !array_key_exists('twitter:url', $this->meta['property']))
			{
				$this->addOneMeta('name', 'twitter:url', URL::current());
			}
		}
	}

	public function doTwitter()
	{
		Config::set('laravel-head::twitter.active', true);
	}

	public function noTwitter()
	{
		Config::set('laravel-head::twitter.active', false);

		foreach ($this->meta as $type => $val)
		{
			if (array_key_exists('name', $this->meta))
			{
				foreach ($val as $value => $content)
				{
					if (starts_with($value, 'twitter:') || starts_with($value, 'twitter:'))
					{
						$this->meta['name'] = array_except($this->meta['name'], array($value));
					}
				}
			}
		}
	}

	protected function tagMisc()
	{
		$html = '';

		$this->addShiv();

 		foreach ($this->misc as $line)
 		{
 			$html .= $line . "\n\t";
 		}

 		return $html;
	}

	public function addMisc($tag)
 	{
 		if (is_array($tag))
 		{
 			foreach ($tag as $line)
 			{
 				array_push($this->misc, $line);
 			}
 		}

 		elseif (is_string($tag))
 		{
 			array_push($this->misc, $tag);
 		}
 	}

 	protected function addShiv()
 	{
 		if (Config::get('laravel-head::html5_shiv'))
 		{
 			$this->addMisc('<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->');
 		}
 	}

 	protected function addAnalytics()
 	{
 		$id = Config::get('laravel-head::analytics.id');

 		$script = Config::get('laravel-head::analytics.script');

 		if (App::environment('production') && Config::get('laravel-head::analytics.active'))
 		{
 			if ($script)
 			{
 				$this->addMisc('<script>'.$script.'</script>');
 			}

 			elseif ($id)
 			{
 				$this->addMisc("<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create', '".$id."', 'auto');ga('send', 'pageview');</script>");
 			}
 		}
 	}

 	public function doShiv()
	{
		Config::set('laravel-head::html5_shiv', true);
	}

 	public function noShiv()
	{
		Config::set('laravel-head::html5_shiv', false);
	}

	public function doAnalytics()
	{
		Config::set('laravel-head::analytics.active', true);
	}

	public function noAnalytics()
	{
		Config::set('laravel-head::analytics.active', false);
	}

}