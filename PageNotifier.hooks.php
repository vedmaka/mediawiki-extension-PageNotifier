<?php

class PageNotifierHooks {

	public static function onExtensionLoad() {
		//TODO: ...
	}

	/**
	 * @param WikiPage $wikiPage
	 * @param User $user
	 * @param Content $content
	 * @param string $summary
	 * @param bool $isMinor
	 * @param bool $isWatch
	 * @param $section
	 * @param int $flags
	 * @param Revision $revision
	 * @throws ConfigException
	 * @throws MWException
	 */
	public static function onPageContentInsertComplete( &$wikiPage, User &$user, $content, $summary, $isMinor, $isWatch,
		$section, &$flags, Revision $revision ) {
		PageNotifier::getInstance()->handleInsertPage( $wikiPage->getTitle(), $user );
	}

}