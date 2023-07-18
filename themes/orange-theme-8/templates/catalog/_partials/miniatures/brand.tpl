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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{block name='brand_miniature_item'}
  <div class="col-6 col-md-4 col-lg-3 col-xl-auto text-center">
    <div class="border border-secondary rounded w-auto mx-auto pb-3 my-4" style="max-width: 200px;">
      <div class="brand-img">
        <a href="{$brand.url}">
        {if !$brand.image|strstr:'default-small_default'}
          <img src="{$brand.image}" alt="{$brand.name}" loading="lazy" class="mx-auto w-100 p-3" style="max-width: 150px; height: 150px; aspect-ratio: 1; object-fit: contain;">
          <h2 class="mx-auto d-none font-weight-bold">{$brand.name}</h2>
        {else}
        <span style="width: 150px; height: 150px; display: flex; align-items: center;" class="text-center mx-auto ">
          <h2 class="mx-auto h6 my-0 font-weight-bold px-2">{$brand.name}</h2>
        </span>
        {/if}
      </a>
      </div>
      <div class="brand-infos">
        <h3 class="mt-4 font-weight-bold small"><a href="{$brand.url}">{$brand.nb_products}</a></h3>
      </div>
    </div>
  </div>
{/block}