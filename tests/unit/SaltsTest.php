<?php

use PHPUnit\Framework\TestCase;
use WP_CLI_Dotenv\Fixtures;
use WP_CLI_Dotenv\Salts\Salts;

class SaltsTest extends TestCase
{
    use Fixtures;

    /**
     * @test
     * @throws Exception
     */
    function it_parses_the_php_from_the_wordpress_generator_into_an_array()
    {
        $salts = new Salts($this->get_fixture_path('wp-org-api-generated-salts-php'));
        $collection = $salts->collect();

        $this->assertEquals([
            ['AUTH_KEY'         , '$.*g{oO(WxCzKMZ#ud{#i{XETVyN7affnoZ>c9lp+0L,AFq3_FA!;MR5t~7%~0bk'],
            ['SECURE_AUTH_KEY'  , '#ISZfl8<$4hUN}-hMY|Q>Utt.;vQ2Wi1+n|[Bw*afW(u(+~)(%_| L/!]|$9W(_s'],
            ['LOGGED_IN_KEY'    , '^MCHBc&gzV,-2IUxLV30v0El-$Ck)|R=R3{KhL?<p<F},Q+n(uR#}xqeH|y.]R0S'],
            ['NONCE_KEY'        , 'RYv.eOVr|8L$(hF$QtcBU{$hTN^a[67.F:Ma2R#$1f%:A+8Y|>JDOeKcVf/:BJ#y'],
            ['AUTH_SALT'        , 'cKkVFNG<zd|*X-bzM]*_0zf$,VqQfFI9j| ~DbOVY&RkwS1+C#$*[PXb+^c7=?5k'],
            ['SECURE_AUTH_SALT' , ' ;O3(wNH@!,*F$<^I-TI86E^`RvY9R(!~>h%3cY_AnJt4ze?b:dbP![Xf{F9_7n^'],
            ['LOGGED_IN_SALT'   , 'c+/,-o{]RCUjmGYd;n.!JZpMfR+PP$8- Tt&m}3JfZ5d%TccrzrIN9+UC^_eH):{'],
            ['NONCE_SALT'       , 'o*!J1UjHZ3-3GMgtZlnFh5MgT7Aw@.x_+q@,%(Tk4t:-A61niZXa1|/RbSbkG- :'],
        ],
            $collection->all()
        );
    }

    /**
     * @test
     */
    function it_blows_up_if_the_wordpress_org_api_is_down()
    {
        $this->expectException(Exception::class);

        (new Salts($this->get_fixture_path('wp-org-api-down')))->collect();
    }
}
