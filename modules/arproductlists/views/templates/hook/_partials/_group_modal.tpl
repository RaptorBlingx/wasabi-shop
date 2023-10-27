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

<div class="modal fade" id="arpl-group-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-hg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div class="modal-title" style="font-size: 18px;" id="myModalLabel">{l s='New group' mod='arproductlists'}</div>
            </div>
            <form class="form-horizontal form" id="arpl-group-form" onsubmit="arPL.group.save(); return false;">
                <input type="hidden" id="arpl-group-form_id" value="" data-default="">
                <input type="hidden" id="arpl-group-form_hook" value="" data-default="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-10">
                            <div class="form-group">
                                <label class="control-label col-sm-4">{l s='Group name' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="arpl-group-form_title" placeholder="{l s='Leave this field blank to autogenerate group name' mod='arproductlists'}" name="title" data-serializable="true" data-default="">
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">{l s='Group view type' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="type" id="arpl-group-form_type" data-serializable="true" data-default="simple">
                                        <option value="simple">{l s='Non-tabbed view' mod='arproductlists'}</option>
                                        <option value="tabbed">{l s='Tabbed view' mod='arproductlists'}</option>
                                    </select>
                                    <div class="errors"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">{l s='Shop context' mod='arproductlists'}</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="id_shop" id="arpl-group-form_id_shop" data-serializable="true" data-default="0">
                                        <option value="0">{l s='All shops' mod='arproductlists'}</option>
                                        {foreach $shops as $shop}
                                            <option value="{$shop.id_shop|intval}">{$shop.name|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    </select>
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