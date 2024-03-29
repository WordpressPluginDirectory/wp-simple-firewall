<?php declare( strict_types=1 );

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\SecurityAdmin;

use FernleafSystems\Wordpress\Plugin\Shield;
use FernleafSystems\Wordpress\Plugin\Shield\Modules\BaseShield;

class ModCon extends BaseShield\ModCon {

	public const SLUG = 'admin_access_restriction';

	/**
	 * @var Lib\WhiteLabel\WhitelabelController
	 */
	private $whitelabelCon;

	/**
	 * @var Lib\SecurityAdmin\SecurityAdminController
	 */
	private $securityAdminCon;

	protected function enumRuleBuilders() :array {
		return [
			Rules\Build\IsSecurityAdmin::class,
		];
	}

	public function getWhiteLabelController() :Lib\WhiteLabel\WhitelabelController {
		return $this->whitelabelCon ?? $this->whitelabelCon = new Lib\WhiteLabel\WhitelabelController();
	}

	public function getSecurityAdminController() :Lib\SecurityAdmin\SecurityAdminController {
		return $this->securityAdminCon ?? $this->securityAdminCon = new Lib\SecurityAdmin\SecurityAdminController();
	}

	public function runDailyCron() {
		parent::runDailyCron();
		$this->runMuHandler();
	}

	public function onConfigChanged() :void {
		$this->getWhiteLabelController()->verifyUrls();
		if ( $this->opts()->isOptChanged( 'enable_mu' ) ) {
			$this->runMuHandler();
		}
	}

	private function runMuHandler() {
		$mu = self::con()->mu_handler;
		try {
			$this->opts()->isOpt( 'enable_mu', 'Y' ) ? $mu->convertToMU() : $mu->convertToStandard();
		}
		catch ( \Exception $e ) {
		}
		$this->opts()->setOpt( 'enable_mu', $mu->isActiveMU() ? 'Y' : 'N' );
	}

	/**
	 * @deprecated 18.5
	 */
	public function preProcessOptions() {
	}

	/**
	 * @deprecated 18.5
	 */
	public function doPrePluginOptionsSave() {
	}
}