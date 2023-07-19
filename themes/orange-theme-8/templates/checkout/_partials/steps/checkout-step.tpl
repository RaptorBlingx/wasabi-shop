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
{block name='step'}
  <section  id    = "{$identifier}"
            class = "{[
                        'checkout-step'   => true,
                        '-current'        => $step_is_current,
                        '-reachable'      => $step_is_reachable,
                        '-complete'       => $step_is_complete,
                        'js-current-step' => $step_is_current
                    ]|classnames} card"
  >
    <div class="card-header js-checkout-step-header border-bottom" id="heading-{$identifier}" data-identifier="{$identifier}">
    <h2 class="step-title h3 font-weight-normal position-relative" style="text-transform: none;">
      <i class="material-icons rtl-no-flip done text-success" style="margin-top: 5px;">&#xE876;</i>
      <span class="step-number mr-2">{$position}</span>
      {$title}

      {if $step_is_reachable}
      <button class="step-edit text-muted btn btn-sm btn-link py-0 position-absolute" 
      style="min-width:0; right: 0;top: 50%;transform: translateY(-50%);" 
      data-toggle="collapse" data-target="#content-{$identifier}" 
      aria-expanded="{if $step_is_current}true{else}false{/if}" 
      aria-controls="content-{$identifier}">
        <i class="material-icons edit ">mode_edit</i>
      </button>
      {/if}
    </h2>
    </div>
    <div id="content-{$identifier}" class="collapse{if $step_is_current} show{/if}" aria-labelledby="heading-{$identifier}" data-parent="#js-checkout-process">
      <div class="content card-body">
        {block name='step_content'}DUMMY STEP CONTENT{/block}
      </div>
    </div>
  </section>  
{/block}
