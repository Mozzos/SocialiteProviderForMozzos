<?php

namespace Udoless\SocialiteProviders\Mozzos;

use SocialiteProviders\Manager\SocialiteWasCalled;

class MozzosExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite(
            'mozzos', __NAMESPACE__.'\Provider'
        );
    }
}
