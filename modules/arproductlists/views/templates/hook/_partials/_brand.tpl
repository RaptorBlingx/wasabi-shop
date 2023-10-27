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

<li data-id="{$brand->id|intval}" id="arpl-brand-{$brand->id|intval}">
    <div class="arpl-brand-handle">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M276 236.075h115.85v-76.15c0-10.691 12.926-16.045 20.485-8.485l96.149 96.149c4.686 4.686 4.686 12.284 0 16.971l-96.149 96.149c-7.56 7.56-20.485 2.206-20.485-8.485v-76.149H275.999v115.776h76.15c10.691 0 16.045 12.926 8.485 20.485l-96.149 96.15c-4.686 4.686-12.284 4.686-16.971 0l-96.149-96.149c-7.56-7.56-2.206-20.485 8.485-20.485H236V276.075H120.149v76.149c0 10.691-12.926 16.045-20.485 8.485L3.515 264.56c-4.686-4.686-4.686-12.284 0-16.971l96.149-96.149c7.56-7.56 20.485-2.206 20.485 8.485v76.15H236V120.15h-76.149c-10.691 0-16.045-12.926-8.485-20.485l96.149-96.149c4.686-4.686 12.284-4.686 16.971 0l96.149 96.149c7.56 7.56 2.206 20.485-8.485 20.485H276v115.925z" class=""></path></svg>
    </div>
    <div class="img-container">
        <img src="{$image|escape:'htmlall':'UTF-8'}" />
    </div>
    <div class="arpl-brand-name">{$brand->name|escape:'htmlall':'UTF-8'}</div>
    <div class="arpl-brand-ref">ID: {$brand->id|intval}</div>
    <button class="arpl-brand-delete" type="button">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-trash-alt fa-w-14 fa-3x"><path fill="currentColor" d="M296 432h16a8 8 0 0 0 8-8V152a8 8 0 0 0-8-8h-16a8 8 0 0 0-8 8v272a8 8 0 0 0 8 8zm-160 0h16a8 8 0 0 0 8-8V152a8 8 0 0 0-8-8h-16a8 8 0 0 0-8 8v272a8 8 0 0 0 8 8zM440 64H336l-33.6-44.8A48 48 0 0 0 264 0h-80a48 48 0 0 0-38.4 19.2L112 64H8a8 8 0 0 0-8 8v16a8 8 0 0 0 8 8h24v368a48 48 0 0 0 48 48h288a48 48 0 0 0 48-48V96h24a8 8 0 0 0 8-8V72a8 8 0 0 0-8-8zM171.2 38.4A16.1 16.1 0 0 1 184 32h80a16.1 16.1 0 0 1 12.8 6.4L296 64H152zM384 464a16 16 0 0 1-16 16H80a16 16 0 0 1-16-16V96h320zm-168-32h16a8 8 0 0 0 8-8V152a8 8 0 0 0-8-8h-16a8 8 0 0 0-8 8v272a8 8 0 0 0 8 8z" class=""></path></svg>
    </button>
</li>