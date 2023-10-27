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

<div class="arproductlists-config-panel {if $activeTab != 'ArPLRelCatConfig'}hidden{/if}" id="arproductlists-relcat">
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-cog"></i> {l s='Related categories' mod='arproductlists'}
            <span class="panel-heading-action">
                <a class="list-toolbar-btn" onclick="arPL.relCat.create(); return false;" href="#">
                    <span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="New relation" data-html="true" data-placement="top">
                        <i class="process-icon-new"></i>
                    </span>
                </a>
                <a class="list-toolbar-btn" onclick="arPL.relCat.reload(); return false;" href="#">
                    <span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Refresh list" data-html="true" data-placement="top">
                        <i class="process-icon-refresh"></i>
                    </span>
                </a>
            </span>
        </div>
        <div class="form-wrapper">
            <div class="row" id="arpl-relcat">
                
            </div>
        </div>
    </div>
</div>
            
<div class="modal fade" id="arpl-relcat-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div class="modal-title" style="font-size: 18px;" id="myModalLabel">{l s='Category relation' mod='arproductlists'}</div>
            </div>
            <form class="form-horizontal form" id="arpl-relcat-form" onsubmit="arPL.relCat.save(); return false;">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-10">
                            <div class="form-group">
                                <label class="control-label required col-sm-4">{l s='Category' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    {$relCategoriesTree nofilter}
                                    <div id="arpl-relcat-form_source"></div>
                                    <div class="errors"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-10">
                            <div class="form-group">
                                <label class="control-label required col-sm-4">{l s='Related categories' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    {$relRelatedCategoriesTree nofilter}
                                    <div id="arpl-relcat-form_rels"></div>
                                    <div class="errors"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" type="button" data-dismiss="modal">{l s='Close' mod='arproductlists'}</button>
                    <button class="btn btn-success" type="submit">{l s='Save' mod='arproductlists'}</button>
                </div>
            </form>
        </div>
    </div>
</div>