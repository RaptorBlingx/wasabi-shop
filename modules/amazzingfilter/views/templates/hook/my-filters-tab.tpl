{*
*  @author    Amazzing <mail@amazzing.ru>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{if !$is_modern}<li class="my-filters">{/if}
	<a class="af{if $is_modern} col-lg-4 col-md-6 col-sm-6 col-xs-12{/if}" href="{$href|escape:'html':'UTF-8'}">
		<span class="link-item">
			<i class="{$layout_classes['icon-filter']|escape:'html':'UTF-8'} material-icons"></i>
			{l s='Filtering preferences' mod='amazzingfilter'}
		</span>
	</a>
{if !$is_modern}</li>{/if}
{* since 3.2.0 *}
