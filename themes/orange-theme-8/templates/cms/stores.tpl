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
  {extends file='page.tpl'}



  {block name='page_content_container'}
  <div class="container">
    <div class="row">
      <div class="col-12 text-center mb-5">
        <h1 class="h1">I nostri negozi</h1>
      </div>
    </div>
    <div class="row justify-content-center">
      {foreach $stores as $store}
        <div class="col-12 col-lg-6 col-xl-4 mb-5">
          <article id="store-{$store.id}">
            <img src="{$store.image.bySize.stores_default.url}" alt="{$store.image.legend}" title="{$store.image.legend}" class="img-fluid w-100 rounded-top">
            <div class="p-4 bg-light rounded-bottom">
              <h2 class="h3"><strong>{$store.name}</strong></h2>
              <address>{$store.address.formatted nofilter}</address>
              {if $store.note || $store.phone || $store.fax || $store.email}
              <a data-toggle="collapse" href="#about-{$store.id}" aria-expanded="false"
                aria-controls="about-{$store.id}"><strong>{l s='About and Contact' d='Shop.Theme.Global'}</strong><i
                  class="material-icons">&#xE409;</i></a>
              {/if}
              {foreach $store.business_hours as $day}
                <div class="row flex-nowrap mb-2">
                  <div class="col-auto" style="min-width: 100px;">
                    <strong>
                      {$day.day|truncate:4:'.'}
                    </strong>
                  </div>
                  <div class="col ">
                    {foreach $day.hours as $h}
                    {$h}
                    {/foreach}
                  </div>
                </div>
              {/foreach}
              <footer id="about-{$store.id}" class="collapse">
                <div class="store-item-footer divide-top">
                  <div class="card-block">
                    {if $store.note}
                    <p class="text-justify">{$store.note}
                    <p>
                      {/if}
                  </div>
                  <ul class="card-block">
                    {if $store.phone}
                    <li><i class="material-icons">&#xE0B0;</i>{$store.phone}</li>
                    {/if}
                    {if $store.fax}
                    <li><i class="material-icons">&#xE8AD;</i>{$store.fax}</li>
                    {/if}
                    {if $store.email}
                    <li><i class="material-icons">&#xE0BE;</i>{$store.email}</li>
                    {/if}
                  </ul>
                </div>
              </footer>
            </div>
          </article>
        </div>
      {/foreach}
    </div>
  </div>
{/block}



    