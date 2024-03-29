<?php declare( strict_types=1 );

namespace FernleafSystems\Wordpress\Plugin\Shield\Modules\Plugin\Rules\Build;

use FernleafSystems\Wordpress\Plugin\Shield\Rules\{
	Build\BuildRuleCoreShieldBase,
	Build\RuleTraits,
	Conditions,
	Responses
};
use FernleafSystems\Wordpress\Plugin\Shield\Modules\IPs\Rules\Build\{
	IpWhitelisted,
	IsPathWhitelisted
};

class RequestBypassesAllRestrictions extends BuildRuleCoreShieldBase {

	use RuleTraits\InstantExec;

	public const SLUG = 'shield/request_bypasses_all_restrictions';

	protected function getName() :string {
		return 'A Request That Bypasses Restrictions';
	}

	protected function getDescription() :string {
		return 'Does the request bypass all plugin restrictions.';
	}

	protected function getConditions() :array {
		return [
			'logic' => static::LOGIC_AND,
			'group' => [
				[
					'rule'         => RequestIsSiteBlockdownBlocked::SLUG,
					'invert_match' => true,
				],
				[
					'logic' => static::LOGIC_OR,
					'group' => [
						[
							'condition' => Conditions\IsForceOff::SLUG,
						],
						[
							'rule'         => IsPublicWebRequest::SLUG,
							'invert_match' => true,
						],
						[
							'rule' => IsTrustedBot::SLUG,
						],
						[
							'rule' => IsPathWhitelisted::SLUG,
						],
						[
							'rule' => IpWhitelisted::SLUG,
						],
					]
				],
			]
		];
	}

	protected function getResponses() :array {
		return [
			[
				'response' => Responses\SetRequestBypassesAllRestrictions::SLUG,
			],
		];
	}
}