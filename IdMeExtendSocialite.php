<?php

namespace SocialiteProviders\IdMe;

use SocialiteProviders\Manager\SocialiteWasCalled;

class IdMeExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('idme', Provider::class);
    }
}