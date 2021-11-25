<?php
/**
 * @author    Blue Acorn iCi <code@blueacornici.com>
 * @copyright 2021 Blue Acorn iCi. All Rights Reserved.
 */
declare(strict_types=1);

namespace PayYourWay\Pyw\Api;

/**
 * Onboarding Lookup Service
 * This message initiates an Onboarding lookup.
 * Please see {@see OnboardingRequestInterface} for more information on the fields available.
 *
 * @api
 */
interface OnboardingLookupInterface
{
    /**
     * Look up the access token for a request
     *
     * @param OnboardingRequestInterface $request
     * @return string
     */
    public function execute(
        OnboardingRequestInterface $request
    ): string;
}
