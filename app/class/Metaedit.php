<?php

namespace Wcms;

class Pageedit extends Page
{
    protected $resettag;
    protected $resetdate;
	protected $emptycontent;




	/**
	 * Edit a page based on object seting
	 * 
	 * @param Page $page Page to be metaedited
	 * 
	 * @return Page Edited page object
	 */
	public function editpage(Page $page)
	{
		if($this->resettag) {
			$page->tag([]);
		}
		$page->addtag($this->tag);
		if($this->resetdate) {
			$page->date()
		}
		$page->secure($this->secure);
		$page->templatebody($this->templatebody);
		$page->templatecss($this->templatecss);
		$page->templatejavascript($this->templatejavascript);

		return $page;
	}

}





?>