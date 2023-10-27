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

<div class="arpl-rule arpl-rule-empty expanded">
    <input type="hidden" name="rule_id" value="" />
    <div class="arplr-rule-header">
        <div class="arplr-rule-name">
            <input name="rule_name" type="text" class="form-control" value="" placeholder="Enter rule name or leave empty to auto-generate" />
        </div>
        <div class="arplr-rule-rel">
            <div class="row">
                <div class="col-sm-6 col-md-5 col-lg-4 text-right">
                    <p>
                        {l s='Related rule:' mod='arproductlists'}
                    </p>
                </div>
                <div class="col-sm-6 col-md-7 col-lg-8">
                    <select class="form-control" name="rule_rel" placeholder="Related rule">

                    </select>
                </div>
            </div>
        </div>
        <div class="arpl-rule-actions">
            <button class="arpl-group-action arpl-rule-toggle" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="caret-down"><path fill="currentColor" d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z" class=""></path></svg>
            </button>
            <button class="arpl-group-action arpl-rule-add-group" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M376 232H216V72c0-4.42-3.58-8-8-8h-32c-4.42 0-8 3.58-8 8v160H8c-4.42 0-8 3.58-8 8v32c0 4.42 3.58 8 8 8h160v160c0 4.42 3.58 8 8 8h32c4.42 0 8-3.58 8-8V280h160c4.42 0 8-3.58 8-8v-32c0-4.42-3.58-8-8-8z" class=""></path></svg>
            </button>
            <button class="arpl-group-action arpl-rule-remove" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M296 432h16a8 8 0 0 0 8-8V152a8 8 0 0 0-8-8h-16a8 8 0 0 0-8 8v272a8 8 0 0 0 8 8zm-160 0h16a8 8 0 0 0 8-8V152a8 8 0 0 0-8-8h-16a8 8 0 0 0-8 8v272a8 8 0 0 0 8 8zM440 64H336l-33.6-44.8A48 48 0 0 0 264 0h-80a48 48 0 0 0-38.4 19.2L112 64H8a8 8 0 0 0-8 8v16a8 8 0 0 0 8 8h24v368a48 48 0 0 0 48 48h288a48 48 0 0 0 48-48V96h24a8 8 0 0 0 8-8V72a8 8 0 0 0-8-8zM171.2 38.4A16.1 16.1 0 0 1 184 32h80a16.1 16.1 0 0 1 12.8 6.4L296 64H152zM384 464a16 16 0 0 1-16 16H80a16 16 0 0 1-16-16V96h320zm-168-32h16a8 8 0 0 0 8-8V152a8 8 0 0 0-8-8h-16a8 8 0 0 0-8 8v272a8 8 0 0 0 8 8z" class=""></path></svg>
            </button>
        </div>
    </div>
    <div class="arplr-rule-content">
        <div class="arpl-rule-group">
            <div class="row">
                <div class="col-sm-12">
                    <div class="arpl-rule-group-op">
                        <select class="form-control" name="op">
                            <option value="AND">{l s='AND' mod='arproductlists'}</option>
                            <option value="OR">{l s='OR' mod='arproductlists'}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="arpl-rule-group-content">
                <div class="arpl-rule-condition active">
                    <input type="hidden" name="condition_status" value="1" />
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="arpl-rule-condition-op">
                                <select class="form-control" name="op">
                                    <option value="AND">{l s='AND' mod='arproductlists'}</option>
                                    <option value="OR">{l s='OR' mod='arproductlists'}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="arpl-rule-condition-content">
                        <div class="row">
                            <div class="col-sm-3 arpl-rule-condition-type">
                                <select class="form-control" name="type">
                                    <option value="feature">{l s='Feature' mod='arproductlists'}</option>
                                    <option value="category">{l s='Category' mod='arproductlists'}</option>
                                    <option value="manufacturer">{l s='Manufacturer' mod='arproductlists'}</option>
                                </select>
                            </div>
                            <div class="col-sm-7">
                                <div class="row arpl-rule-type-feature">
                                    <div class="col-sm-1 text-center">

                                    </div>
                                    <div class="col-sm-5 arpl-rule-condition-type">
                                        <select class="form-control" name="id_feature">
                                            
                                        </select>
                                    </div>
                                    <div class="col-sm-1 text-center">
                                        <svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="equals" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="svg-inline--fa fa-equals fa-w-12 fa-3x"><path fill="currentColor" d="M376 304H8c-4.42 0-8 3.58-8 8v32c0 4.42 3.58 8 8 8h368c4.42 0 8-3.58 8-8v-32c0-4.42-3.58-8-8-8zm0-144H8c-4.42 0-8 3.58-8 8v32c0 4.42 3.58 8 8 8h368c4.42 0 8-3.58 8-8v-32c0-4.42-3.58-8-8-8z" class=""></path></svg>
                                    </div>
                                    <div class="col-sm-5 arpl-rule-condition-type">
                                        <select class="form-control" name="id_feature_value">
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="row arpl-rule-type-category hidden">
                                    <div class="col-sm-1 text-center">
                                        <svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="equals" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="svg-inline--fa fa-equals fa-w-12 fa-3x"><path fill="currentColor" d="M376 304H8c-4.42 0-8 3.58-8 8v32c0 4.42 3.58 8 8 8h368c4.42 0 8-3.58 8-8v-32c0-4.42-3.58-8-8-8zm0-144H8c-4.42 0-8 3.58-8 8v32c0 4.42 3.58 8 8 8h368c4.42 0 8-3.58 8-8v-32c0-4.42-3.58-8-8-8z" class=""></path></svg>
                                    </div>
                                    <div class="col-sm-11 arpl-rule-condition-type">
                                        <select class="form-control" name="id_category">
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="row arpl-rule-type-manufacturer hidden">
                                    <div class="col-sm-1 text-center">
                                        <svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="equals" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="svg-inline--fa fa-equals fa-w-12 fa-3x"><path fill="currentColor" d="M376 304H8c-4.42 0-8 3.58-8 8v32c0 4.42 3.58 8 8 8h368c4.42 0 8-3.58 8-8v-32c0-4.42-3.58-8-8-8zm0-144H8c-4.42 0-8 3.58-8 8v32c0 4.42 3.58 8 8 8h368c4.42 0 8-3.58 8-8v-32c0-4.42-3.58-8-8-8z" class=""></path></svg>
                                    </div>
                                    <div class="col-sm-11 arpl-rule-condition-type">
                                        <select class="form-control" name="id_manufacturer">
                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <button class="arpl-condition-action arpl-condition-toggle" data-id="" type="button">
                                    <svg xmlns="http://www.w3.org/2000/svg" title="Group is active" viewBox="0 0 512 512" class="icon-checked"><path fill="currentColor" d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z" class=""></path></svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" title="Group is not active" viewBox="0 0 512 512" class="icon-unchecked"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm216 248c0 118.7-96.1 216-216 216-118.7 0-216-96.1-216-216 0-118.7 96.1-216 216-216 118.7 0 216 96.1 216 216z" class=""></path></svg>
                                </button>
                                <button class="arpl-condition-action arpl-condition-add" data-id="" type="button">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M376 232H216V72c0-4.42-3.58-8-8-8h-32c-4.42 0-8 3.58-8 8v160H8c-4.42 0-8 3.58-8 8v32c0 4.42 3.58 8 8 8h160v160c0 4.42 3.58 8 8 8h32c4.42 0 8-3.58 8-8V280h160c4.42 0 8-3.58 8-8v-32c0-4.42-3.58-8-8-8z" class=""></path></svg>
                                </button>
                                <button class="arpl-condition-action arpl-condition-remove" data-id="" type="button">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M296 432h16a8 8 0 0 0 8-8V152a8 8 0 0 0-8-8h-16a8 8 0 0 0-8 8v272a8 8 0 0 0 8 8zm-160 0h16a8 8 0 0 0 8-8V152a8 8 0 0 0-8-8h-16a8 8 0 0 0-8 8v272a8 8 0 0 0 8 8zM440 64H336l-33.6-44.8A48 48 0 0 0 264 0h-80a48 48 0 0 0-38.4 19.2L112 64H8a8 8 0 0 0-8 8v16a8 8 0 0 0 8 8h24v368a48 48 0 0 0 48 48h288a48 48 0 0 0 48-48V96h24a8 8 0 0 0 8-8V72a8 8 0 0 0-8-8zM171.2 38.4A16.1 16.1 0 0 1 184 32h80a16.1 16.1 0 0 1 12.8 6.4L296 64H152zM384 464a16 16 0 0 1-16 16H80a16 16 0 0 1-16-16V96h320zm-168-32h16a8 8 0 0 0 8-8V152a8 8 0 0 0-8-8h-16a8 8 0 0 0-8 8v272a8 8 0 0 0 8 8z" class=""></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="arplr-rule-footer">
        <button class="btn btn-default arpl-rule-cancel" type="button">
            {l s='Cancel' mod='arproductlists'}
        </button>
        <button class="btn btn-primary arpl-rule-save" type="button">
            {l s='Save' mod='arproductlists'}
        </button>
    </div>
</div>