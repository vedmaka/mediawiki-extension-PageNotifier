<?php

class PageNotifier {

	/**
	 * @var PageNotifier|null
	 */
	private static $instance = null;

	/**
	 * @return PageNotifier
	 */
	public static function getInstance() {
		if( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @param Title $title
	 * @param User $user
	 * @throws ConfigException
	 */
	public function handleInsertPage( $title, $user ) {
		if( $this->checkEligible( $title ) && $this->canNotify( $user ) ) {
			$this->notify();
		}
	}

	/**
	 * @param User $user
	 * @return bool
	 * @throws ConfigException
	 */
	private function canNotify( $user ) {
		global $wgUser;
		$config = \MediaWiki\MediaWikiServices::getInstance()->getMainConfig();
		$targetUser = $config->get('NotifyUser');
		if( !$targetUser ) {
			return false;
		}
		$realUser = User::newFromName($targetUser);
		if( !$realUser->getId() ) {
			return false;
		}
		if( $user->getId() === $wgUser->getId() ) {
			return false;
		}
		if( !$realUser->getEmail() ) {
			return false;
		}
		return true;
	}

	/**
	 * @param Title $title
	 * @return bool
	 * @throws ConfigException
	 */
	private function checkEligible( $title ) {
		$config = \MediaWiki\MediaWikiServices::getInstance()->getMainConfig();
		$namespaces = $config->get('NotifyWatchNamespaces');
		if(!count($namespaces)) {
			return false;
		}
		if( !in_array($title->getNamespace(), $namespaces) ) {
			return false;
		}
		return true;
	}

	/**
	 * @throws ConfigException
	 * @throws MWException
	 */
	private function notify() {
		$config = \MediaWiki\MediaWikiServices::getInstance()->getMainConfig();
		$targetUserName = $config->get('NotifyWatchNamespaces');
		$targetUser = User::newFromName( $targetUserName );
		$subject = '';
		$mailText = '';
		UserMailer::send( new MailAddress($targetUser->getEmail()), new MailAddress( $config->get('PasswordSender') ), $subject, $mailText );
	}

}