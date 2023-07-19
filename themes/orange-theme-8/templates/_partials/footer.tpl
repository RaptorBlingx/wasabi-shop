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

  <section class="bg-primary section-padding">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-12 col-xl flex-xl-nowrap text-center text-xl-left text-white">
          <h3 class="h2 text-white mb-2">Iscriviti alla nostra newsletter</h3>
          <p class="mb-0">Per restare sempre aggiornato su novità e promozioni esclusive</p>
        </div>
        <div class="col-12 col-xl-auto text-center text-xl-right mt-3 mt-xl-0">
            <a href="#" class="btn btn-outline-white ">Iscriviti subito alla newsletter</a>
        </div>
      </div>
    </div>
  </section>

  <section class="bg-light py-4 border-top">
    <div class="container-fluid py-3">
      <div class="row flex-lg-nowrap align-items-center">
        <div class="col-12 col-md-4 col-xl-4 mb-3 mb-md-0">
          <a href="#" class="btn btn-primary btn-primary-outline-hover w-100" target="_blank">Condividi su Facebook</a>
        </div>
        <div class="col-12 col-md-4 col-xl-4 mb-3 mb-md-0">
          <a href="#" class="btn btn-primary btn-primary-outline-hover w-100" target="_blank">Condividi su Whatsapp</a>
        </div>
        <div class="col-12 col-md-4 col-xl-4 mb-0 mb-md-0">
          <a href="#" class="btn btn-primary btn-primary-outline-hover w-100" target="_blank">Condividi su Twitter</a>
        </div>
      </div>
    </div>
  </section>

  <section class="footer-container section-padding bg-light m-0">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-3">
          <p class="footer__title footer__title--desktop">Prodotti</p>
          <a href="#footerMenu1" class="footer__title--mobile footer__title" data-toggle="collapse">Prodotti</a>
          <ul id="footerMenu1" class="footer-menu collapse show" data-collapse-hide-mobile="">
            <li>
              <a id="link-product-page-prices-drop-1" class="cms-page-link" href="#" title="Our special products">
                Offerte
              </a>
            </li>
            <li>
              <a id="link-product-page-new-products-1" class="cms-page-link" href="#"
                title="I nostri nuovi prodotti, gli ultimi arrivi">
                Nuovi prodotti
              </a>
            </li>
            <li>
              <a id="link-product-page-best-sales-1" class="cms-page-link" href="#"
                title="I nostri prodotti più venduti">
                Più venduti
              </a>
            </li>
          </ul>
        </div>
        <div class="col-lg-3">
          <p class="footer__title footer__title--desktop">Chi siamo</p>
          <a href="#footerMenu2" class="footer__title--mobile footer__title" data-toggle="collapse">Chi siamo</a>
          <ul id="footerMenu2" class="footer-menu collapse show" data-collapse-hide-mobile="">
            <li>
              <a id="link-cms-page-1-2" class="cms-page-link" href="{$link->getCMSLink('4')|escape:'html'}" title="Chi siamo">
                Chi siamo
              </a>
            </li>
            <li>
              <a id="link-cms-page-2-2" class="cms-page-link" href="#" title="  Blog">
                Blog
              </a>
            </li>
            <li>
              <a id="link-cms-page-3-2" class="cms-page-link" href="#" title="Lavora con noi">
                Lavora con noi
              </a>
            </li>
            <li>
              <a id="link-cms-page-4-2" class="cms-page-link" href="/contattaci" title="Scoprite chi siamo">
                Contattaci
              </a>
            </li>
          </ul>
        </div>
        <div id="block_myaccount_infos" class="col-lg-3 links wrapper">
          <p class="footer__title footer__title--desktop">Customer care</p>
          <a href="#footerMenu3" class="footer__title--mobile footer__title" data-toggle="collapse">Customer care</a>
          <ul class=" footer-menu account-list collapse show" data-collapse-hide-mobile="" id="footerMenu3">
            <li>
              <a href="/account" title="Account" rel="nofollow">
                Account
              </a>
            </li>
            <li>
              <a href="{$link->getCMSLink('1')|escape:'html'}" title="Spedizioni" rel="nofollow">
                Spedizioni
              </a>
            </li>
            <li>
              <a href="{$link->getCMSLink('5')|escape:'html'}" title="Pagamenti" rel="nofollow">
                Pagamenti
              </a>
            </li>
            <li>
              <a href="{$link->getCMSLink('3')|escape:'html'}" title="Condizioni di vendita" rel="nofollow">
                Condizioni di vendita
              </a>
            </li>
          </ul>
        </div>
        <div class="block-contact col-lg-3">
          <p class="footer__title footer__title--desktop">Contatti</p>
          <a href="#footerMenu4" class="footer__title--mobile footer__title" data-toggle="collapse">Contatti</a>
          <div id="footerMenu4" class="footer-menu account-list collapse show" data-collapse-hide-mobile="">
            Via Milano 94
            <br>
            Biella (BI) 13900 - Italy
            <br>
            Chiamaci: <a href="#">+39 XXXXXXXXXX</a>
            <br>
            Mail: <a href="#">info@orangepix.it</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="bg-light-medium py-4">
    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-auto text-center">
          <div class="footer-payment-section">
            <a href="{$link->getCMSLink('5')|escape:'html'}">
              <img src="{$urls.img_url}payments/Visa.svg" class="footer-payment" alt="Visa">
              <img src="{$urls.img_url}payments/Mastercard.svg" class="footer-payment" alt="Mastercard">
              <img src="{$urls.img_url}payments/Maestro.svg" class="footer-payment" alt="Maestro">
              <img src="{$urls.img_url}payments/Postepay.svg" class="footer-payment" alt="Postepay">
              <img src="{$urls.img_url}payments/Amex.svg" class="footer-payment" alt="Amex">
              <img src="{$urls.img_url}payments/Paypal.svg" class="footer-payment" alt="Paypal">
              <img src="{$urls.img_url}payments/Klarna.svg" class="footer-payment" alt="Klarna">
              <img src="{$urls.img_url}payments/Soisy.svg" class="footer-payment" alt="Soisy">
              <img src="{$urls.img_url}payments/ApplePay.svg" class="footer-payment" alt="Apple Pay">
              <img src="{$urls.img_url}payments/BonificoBancario.svg" class="footer-payment" alt="Bonifico">
              <img src="{$urls.img_url}payments/TaxFree.svg" class="footer-payment" alt="Taxfree">
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="bg-black py-4 legal-information">
    <div class="container-fluid">
      <div class="row justify-content-center align-items-center ">
        <div class="col-12 col-xl text-center text-xl-left text-footer-legal ">
            <small>
              Copyright © 2023 Orangepix OrangeTheme8 | P.IVA 02582120024
              <br>
              <a href="#" class="text-footer-legal" title="Company Info">Company info</a> | 
              <a href="#" class="text-footer-legal" title="Privacy policy">Privacy policy</a> |
              <a href="#" class="text-footer-legal" title="Cookie policy">Cookie policy</a> |
              <a href="javascript: showCookieConsentModal()" class="text-footer-legal" title="Cookie">Preferenze cookie</a>
            </small>
        </div>
        <div class="col-12 col-xl-auto text-center text-xl-right pt-2 pt-xl-0">
          <span id="opx-footer-cdn"></span>
          <script src="https://cdn.orangepix.it/orangepix/opxfooterlogo.js"></script>
        </div>
      </div>
    </div>
  </section>

  