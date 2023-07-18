{**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License 3.0 (AFL-3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author PrestaShop SA <contact@prestashop.com>
  * @copyright 2007-2017 PrestaShop SA
  * @license https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
  * International Registered Trademark & Property of PrestaShop SA
  *}

  <section class="section-padding">
    <div class="container-fluid">
      <div class="row mb-5">
        <div class="col-12">
          <h2 class="h2 text-center">{l s='Store information' d='Shop.Theme.Global'}</h2>
        </div>
      </div>

      <div class="row">
        <div class="col-12 col-xl-4 mb-4 mb-xl-0">
          <div class="row text-center">
            <div class="icon col-12">
              <div class="contact-rich-info-circle mb-3">
              <i class="material-icons">&#xE55F;</i>
              </div>
            </div>
            <div class="col-12 data">{$contact_infos.address.formatted nofilter}</div>
          </div>
        </div>
        {if $contact_infos.phone}
        <div class="col-12 col-xl-4 mb-4 mb-xl-0">          
          <div class="row text-center">
            <div class="icon col-12">
              <div class="contact-rich-info-circle mb-3">
              <i class="material-icons">&#xE0CD;</i>
              </div>
            </div>
            <div class="col-12 data">
              {l s='Call us:' d='Shop.Theme.Global'}<br />
              <a href="tel:{$contact_infos.phone}">{$contact_infos.phone}</a>
            </div>
          </div>
        </div>
        {/if}
        {if $contact_infos.fax}
        <div class="col-12 col-xl-4 mb-4 mb-xl-0">
          <div class="row text-center">
            <div class="icon col-12">
              <div class="contact-rich-info-circle mb-3">
              <i class="material-icons">&#xE0DF;</i>
              </div>
            </div>
            <div class="col-12 data">
              {l s='Fax:' d='Shop.Theme.Global'}<br />
              {$contact_infos.fax}
            </div>
          </div>
        </div>
        {/if}
        {if $contact_infos.email}
        <div class="col-12 col-xl-4 mb-4 mb-xl-0">          
          <div class="row text-center">
            <div class="icon col-12">
              <div class="contact-rich-info-circle mb-3">
              <i class="material-icons">&#xE158;</i>
              </div>
            </div>
            <div class="col-12 data email">
              {l s='Email us:' d='Shop.Theme.Global'}<br />
              {mailto address=$contact_infos.email encode="javascript"}
            </div>
          </div>
        </div>
        {/if}

      </div>
    </div>
  </section>