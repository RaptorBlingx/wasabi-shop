{*
* 2019 Areama
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@areama.net so we can send you a copy immediately.
*
*
*  @author Areama <contact@areama.net>
*  @copyright  2019 Areama
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of Areama
*}

<div class="arproductlists-config-panel {if $activeTab != 'ArPLRulesConfig'}hidden{/if}" id="arproductlists-rules">
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-cog"></i> {l s='Related products rules' mod='arproductlists'}
            <span class="panel-heading-action">
                <a class="list-toolbar-btn" onclick="arPL.rules.create(); return false;" href="#">
                    <span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="New relation" data-html="true" data-placement="top">
                        <i class="process-icon-new"></i>
                    </span>
                </a>
                <a class="list-toolbar-btn" onclick="arPL.rules.reload(); return false;" href="#">
                    <span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Refresh list" data-html="true" data-placement="top">
                        <i class="process-icon-refresh"></i>
                    </span>
                </a>
            </span>
        </div>
        <div class="form-wrapper">
            <div class="row">
                <div class="col-sm-9" id="arpl-rules">
                    
                </div>
                {include file="./_empty_rule.tpl"}
                <div class="col-sm-3" style="font-size: 15px">
                    <p>{l s='In this section you can create relations between Features, Categories or Brands.' mod='arproductlists'}</p>
                    <p>{l s='These relations will be used for [1]Related products[/1] list.' mod='arproductlists' tags=['<b>']}</p>
                    <p>{l s='[1]Example[/1]:' mod='arproductlists' tags=['<b>']}</p>
                    <a href="https://media.areama.net/uploads/2019-08-29_111944.png" rel="group1" class="fancybox">
                        <img src="https://media.areama.net/uploads/2019-08-29_111944.png" class="img-responsive" />
                    </a>
                    <a href="https://media.areama.net/uploads/2019-08-29_120042.png" rel="group1" class="fancybox">
                        <img src="https://media.areama.net/uploads/2019-08-29_120042.png" class="img-responsive" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
